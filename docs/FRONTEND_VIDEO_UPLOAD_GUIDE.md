# Frontend Video Upload Guide

## Overview
Backend sudah fully support upload images **dan** videos dalam satu endpoint. Frontend tinggal menggunakan endpoint yang sama (`/api/upload/temp`) tanpa perlu perubahan struktur request.

---

## 🎯 API Endpoint

**Endpoint:** `POST /api/upload/temp`

**Tidak ada perubahan pada struktur request!** Frontend tetap menggunakan field name `images[]` untuk image dan video.

---

## 📤 Frontend Implementation

### 1. HTML - File Input

```html
<!-- Accept both images and videos -->
<input
  type="file"
  id="mediaInput"
  name="media[]"
  multiple
  accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml,video/mp4,video/webm,video/quicktime,video/x-msvideo"
>
```

### 2. JavaScript - Upload Function

```javascript
/**
 * Upload mixed media (images + videos) to temp storage
 * @param {FileList} files - Selected files from input
 * @param {string} type - 'product' or 'variant'
 * @param {string} sessionId - Unique session identifier
 * @param {number} productId - Optional, for direct upload to existing product
 * @returns {Promise<Object>} Upload response
 */
async function uploadMedia(files, type, sessionId = null, productId = null) {
  const formData = new FormData();

  // Add all files to FormData
  // IMPORTANT: Use 'images[]' field name (not 'videos[]')
  Array.from(files).forEach(file => {
    formData.append('images[]', file);
  });

  // Add metadata
  formData.append('type', type);

  if (sessionId) {
    formData.append('session_id', sessionId);
  }

  if (productId) {
    formData.append('product_id', productId);
  }

  try {
    const response = await fetch('/api/upload/temp', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${getAuthToken()}`,
        // Don't set Content-Type for FormData, browser will set it automatically
      },
      body: formData
    });

    const result = await response.json();

    if (!result.status) {
      throw new Error(result.message || 'Upload failed');
    }

    return result.data;
  } catch (error) {
    console.error('Upload error:', error);
    throw error;
  }
}
```

---

## 🎨 Display Uploaded Media

Backend response akan include `media_type` field. Gunakan ini untuk render media dengan benar:

```javascript
/**
 * Render media gallery from upload response
 * @param {Array} mediaItems - Array of media objects from API response
 */
function renderMediaGallery(mediaItems) {
  const gallery = document.getElementById('mediaGallery');
  gallery.innerHTML = '';

  mediaItems.forEach((media, index) => {
    const mediaCard = document.createElement('div');
    mediaCard.className = 'media-card';

    if (media.media_type === 'video') {
      // Render VIDEO
      mediaCard.innerHTML = `
        <div class="video-wrapper">
          <video controls preload="metadata">
            <source src="${media.url}" type="video/mp4">
            Your browser does not support the video tag.
          </video>

          <!-- Show thumbnail if available -->
          ${media.thumbnail_url ? `
            <img
              src="${media.thumbnail_url}"
              class="video-thumbnail"
              alt="Video thumbnail"
            >
          ` : ''}

          <!-- Show duration if available -->
          ${media.duration ? `
            <span class="duration-badge">${formatDuration(media.duration)}</span>
          ` : ''}

          <!-- Show file size -->
          <span class="filesize-badge">${formatFileSize(media.file_size)}</span>

          <div class="media-type-badge video-badge">VIDEO</div>
        </div>
      `;
    } else {
      // Render IMAGE
      mediaCard.innerHTML = `
        <div class="image-wrapper">
          <img src="${media.url}" alt="${media.alt_text || 'Product image'}">
          <span class="filesize-badge">${formatFileSize(media.file_size)}</span>
          <div class="media-type-badge image-badge">IMAGE</div>
        </div>
      `;
    }

    // Add remove button
    const removeBtn = document.createElement('button');
    removeBtn.className = 'remove-media-btn';
    removeBtn.innerHTML = '✕';
    removeBtn.onclick = () => removeMedia(index);
    mediaCard.appendChild(removeBtn);

    gallery.appendChild(mediaCard);
  });
}

/**
 * Format duration from seconds to MM:SS
 */
function formatDuration(seconds) {
  const minutes = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${minutes}:${secs.toString().padStart(2, '0')}`;
}

/**
 * Format file size to human readable
 */
function formatFileSize(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / 1048576).toFixed(1) + ' MB';
}
```

---

## 💡 Complete Example - Product Form

```javascript
class ProductMediaManager {
  constructor() {
    this.uploadedMedia = [];
    this.sessionId = this.generateSessionId();
  }

  generateSessionId() {
    return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
  }

  /**
   * Handle file input change
   */
  async handleFileSelect(event) {
    const files = event.target.files;

    if (files.length === 0) return;

    // Validate files
    const validFiles = this.validateFiles(files);

    if (validFiles.length === 0) {
      alert('No valid files selected');
      return;
    }

    // Show loading
    this.showLoading(true);

    try {
      // Upload to temp storage
      const result = await uploadMedia(
        validFiles,
        'product',
        this.sessionId
      );

      // Store uploaded media info
      this.uploadedMedia.push(...result.images);

      // Render gallery
      renderMediaGallery(this.uploadedMedia);

      // Show success message
      this.showMessage(`Uploaded ${result.count} files successfully`, 'success');
    } catch (error) {
      this.showMessage('Upload failed: ' + error.message, 'error');
    } finally {
      this.showLoading(false);
      event.target.value = ''; // Reset input
    }
  }

  /**
   * Validate files before upload
   */
  validateFiles(files) {
    const validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'mov', 'avi', 'webm', 'mkv'];
    const maxFileSize = 100 * 1024 * 1024; // 100MB

    return Array.from(files).filter(file => {
      const extension = file.name.split('.').pop().toLowerCase();
      const isValidExtension = validExtensions.includes(extension);
      const isValidSize = file.size <= maxFileSize;

      if (!isValidExtension) {
        console.warn(`File ${file.name} has invalid extension`);
        return false;
      }

      if (!isValidSize) {
        console.warn(`File ${file.name} exceeds max size (100MB)`);
        return false;
      }

      return true;
    });
  }

  /**
   * Submit product form with media
   */
  async submitProduct(formData) {
    // Add media URLs to form data
    const mediaUrls = this.uploadedMedia.map(media => media.url);
    formData.images = mediaUrls;

    // Submit to product API
    const response = await fetch('/api/catalog/products', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${getAuthToken()}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formData)
    });

    const result = await response.json();

    if (result.status) {
      // Backend will move temp files to permanent location
      console.log('Product created:', result.data);
      return result.data;
    } else {
      throw new Error(result.message);
    }
  }

  showLoading(show) {
    const loader = document.getElementById('uploadLoader');
    loader.style.display = show ? 'block' : 'none';
  }

  showMessage(message, type) {
    // Implement your notification system
    console.log(`[${type.toUpperCase()}] ${message}`);
  }
}

// Initialize
const mediaManager = new ProductMediaManager();

// Attach event listeners
document.getElementById('mediaInput').addEventListener('change', (e) => {
  mediaManager.handleFileSelect(e);
});

document.getElementById('productForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const formData = {
    name: document.getElementById('productName').value,
    description: document.getElementById('productDescription').value,
    price: document.getElementById('productPrice').value,
    // ... other fields
  };

  try {
    await mediaManager.submitProduct(formData);
    alert('Product created successfully!');
  } catch (error) {
    alert('Failed to create product: ' + error.message);
  }
});
```

---

## 📊 Response Format

### Upload Response

```json
{
  "status": true,
  "statusCode": "200",
  "message": "Temporary images uploaded successfully",
  "data": {
    "images": [
      {
        "url": "https://cdn.minimoda.com/temp/products/session-123/image1.jpg",
        "path": "temp/products/session-123/image1.jpg",
        "filename": "1729696545_abc.jpg",
        "temp": true,
        "media_type": "image",
        "file_size": 245678
      },
      {
        "url": "https://cdn.minimoda.com/temp/products/session-123/video1.mp4",
        "path": "temp/products/session-123/video1.mp4",
        "filename": "1729696546_def.mp4",
        "temp": true,
        "media_type": "video",
        "file_size": 5234567,
        "duration": 30,
        "thumbnail_url": "https://cdn.minimoda.com/temp/products/session-123/thumbnails/video1_thumb.jpg"
      }
    ],
    "count": 2,
    "type": "product",
    "is_temp": true,
    "session_id": "session-123",
    "note": "These images are temporary and will be moved when product/variant is saved"
  }
}
```

### Response Fields

| Field | Type | Description | Available For |
|-------|------|-------------|---------------|
| `url` | string | Full URL to access media | All |
| `path` | string | Storage path | All |
| `filename` | string | Generated filename | All |
| `temp` | boolean | Is temporary upload? | All |
| `media_type` | string | `"image"` or `"video"` | All |
| `file_size` | integer | File size in bytes | All |
| `duration` | integer | Duration in seconds | Video only |
| `thumbnail_url` | string | Thumbnail URL | Video only (if FFmpeg available) |

---

## 🎬 Video-Specific Features

### 1. **Duration Extraction**
- Backend automatically extracts video duration using getID3 library
- If library not available, `duration` will be `null`
- Frontend can display duration badge on video thumbnails

### 2. **Thumbnail Generation**
- Backend automatically generates video thumbnail using FFmpeg
- Thumbnail extracted from 1-second mark of video
- Stored in `/thumbnails` subdirectory
- If FFmpeg not available, `thumbnail_url` will be `null`
- Frontend can use thumbnail for video preview

### 3. **File Size Display**
- All media includes `file_size` in bytes
- Frontend should format to human-readable (KB/MB)

---

## 🛠️ Server Requirements (Optional Features)

### For Video Duration Extraction

```bash
# Install getID3 via Composer
composer require james-heinrich/getid3
```

### For Video Thumbnail Generation

```bash
# Install FFmpeg on Ubuntu/Debian
sudo apt update
sudo apt install ffmpeg

# Verify installation
which ffmpeg
ffmpeg -version
```

**Add to `.env`:**
```env
FFMPEG_PATH=ffmpeg  # or full path like /usr/bin/ffmpeg
```

### What happens if libraries not available?

- ✅ **Upload still works** - Videos will upload successfully
- ❌ `duration` will be `null` - No duration info
- ❌ `thumbnail_url` will be `null` - No thumbnail generated
- ✅ All other features work normally

---

## 🐛 Debugging

### Check Logs

Backend logs semua media upload dengan details:

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log | grep "Media upload"
```

**Log output example:**
```
[2025-10-23 10:30:15] local.INFO: Media upload detected {"filename":"video.mp4","mime_type":"video/mp4","extension":"mp4","detected_type":"video"}
[2025-10-23 10:30:16] local.INFO: Video metadata extracted {"duration":30,"filename":"1729696216_abc.mp4"}
[2025-10-23 10:30:17] local.INFO: Video thumbnail generated {"thumbnail_url":"https://cdn.minimoda.com/.../thumbnails/video_thumb.jpg","video_filename":"1729696216_abc.mp4"}
```

### Common Issues

#### Issue 1: Video uploaded but `media_type` is still "image"

**Diagnosis:**
- Check logs for "Media upload detected"
- Verify MIME type and extension in logs

**Solution:**
- Backend now checks both MIME type AND file extension
- Should work for all common video formats

#### Issue 2: No thumbnail generated

**Diagnosis:**
```bash
# Check if FFmpeg installed
which ffmpeg

# Check logs
tail -f storage/logs/laravel.log | grep "thumbnail"
```

**Solution:**
- Install FFmpeg (see Server Requirements)
- Or accept null thumbnail (not critical)

#### Issue 3: No duration info

**Diagnosis:**
```bash
# Check if getID3 installed
composer show | grep getid3
```

**Solution:**
- Install getID3: `composer require james-heinrich/getid3`
- Or accept null duration (not critical)

---

## ✅ Testing Checklist

### Frontend Developer Testing

- [ ] Upload single image → `media_type: "image"`
- [ ] Upload single video → `media_type: "video"`
- [ ] Upload mixed (2 images + 1 video) → Correct types for each
- [ ] Check `file_size` present for all media
- [ ] Check `duration` present for videos (if getID3 installed)
- [ ] Check `thumbnail_url` present for videos (if FFmpeg installed)
- [ ] Verify video plays in `<video>` element
- [ ] Verify image displays in `<img>` element
- [ ] Create product with mixed media → All media saved correctly

### Test Files

**Test Videos:**
- Small MP4 (< 5MB)
- Medium MP4 (10-20MB)
- Large MP4 (50-80MB)
- MOV format
- WebM format

**Test Images:**
- JPG/JPEG
- PNG
- GIF
- WebP

---

## 📝 Summary for Frontend

### What Frontend Needs to Do:

1. **✅ NO CHANGES to upload code**
   - Same endpoint: `POST /api/upload/temp`
   - Same field name: `images[]` (for both images and videos)
   - Same request structure

2. **✅ Add video formats to file input accept**
   ```html
   accept="image/*,video/*"
   ```

3. **✅ Check `media_type` in response**
   ```javascript
   if (media.media_type === 'video') {
     // Render <video>
   } else {
     // Render <img>
   }
   ```

4. **✅ Display video metadata (optional)**
   ```javascript
   // Show duration
   if (media.duration) {
     showDuration(media.duration);
   }

   // Show thumbnail
   if (media.thumbnail_url) {
     showThumbnail(media.thumbnail_url);
   }
   ```

5. **✅ Format file size for display**
   ```javascript
   formatFileSize(media.file_size); // "5.2 MB"
   ```

### API Endpoints to Use:

| Action | Endpoint | Method |
|--------|----------|--------|
| Upload temp media | `/api/upload/temp` | POST |
| Create product | `/api/catalog/products` | POST |
| Create variant | `/api/catalog/products/variants` | POST |

**That's it!** Backend handles all the complexity (detection, metadata extraction, thumbnail generation).

Frontend just:
1. Upload files
2. Get response with `media_type`
3. Render accordingly

---

## 🚀 Ready to Test!

Sistem sudah lengkap:
- ✅ Auto-detect media type (image vs video)
- ✅ Extract video duration (if getID3 available)
- ✅ Generate video thumbnail (if FFmpeg available)
- ✅ Store all metadata to database
- ✅ Graceful degradation (works without optional libraries)

**Frontend developer tinggal test upload video via `/api/upload/temp` dan check response!**
