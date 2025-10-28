# Move Temp Media with Metadata - Updated Guide

## Problem

Saat move media dari temp ke permanent location, **metadata tidak terbawa** (media_type, duration, file_size, thumbnail_url).

**Before (issue):**
```json
// Temp upload response - HAS metadata
{
  "images": [{
    "media_type": "video",
    "duration": 14,
    "file_size": 540048,
    "thumbnail_url": "https://..."
  }]
}

// Move response - MISSING metadata ❌
{
  "images": [{
    "id": 4,
    "url": "https://...",
    "path": "...",
    "sort_order": 2
    // No media_type, duration, file_size, thumbnail_url!
  }]
}
```

## Solution

Frontend harus **pass metadata** dari temp upload response ke move request.

---

## 🔧 Updated API

### Endpoint: `POST /api/upload/move`

### New Request Format

**Before (old):**
```json
{
  "temp_paths": ["temp/products/session-123/video.mp4"],
  "type": "product",
  "product_id": 1
}
```

**After (new):**
```json
{
  "temp_paths": ["temp/products/session-123/video.mp4"],
  "type": "product",
  "product_id": 1,
  "metadata": [
    {
      "media_type": "video",
      "duration": 14,
      "file_size": 540048,
      "thumbnail_url": "https://media.minimoda.id/temp/.../thumbnails/video_thumb.jpg"
    }
  ]
}
```

### Response (updated)

```json
{
  "status": true,
  "message": "Images moved successfully",
  "data": {
    "images": [
      {
        "id": 4,
        "url": "https://media.minimoda.id/products/breezy-long-sleeve/video.mp4",
        "path": "products/breezy-long-sleeve/video.mp4",
        "is_primary": false,
        "sort_order": 2,
        "media_type": "video",       // ✅ Now included!
        "file_size": 540048,         // ✅ Now included!
        "duration": 14,              // ✅ Now included!
        "thumbnail_url": "https://..." // ✅ Now included!
      }
    ],
    "count": 1,
    "type": "product",
    "product_id": 1
  }
}
```

---

## 📝 Frontend Implementation

### 1. Store Metadata from Temp Upload

```javascript
class ProductMediaManager {
  constructor() {
    this.uploadedMedia = [];
    this.sessionId = this.generateSessionId();
  }

  /**
   * Upload media to temp storage
   * Store complete media info including metadata
   */
  async uploadToTemp(files) {
    const formData = new FormData();

    Array.from(files).forEach(file => {
      formData.append('images[]', file);
    });

    formData.append('type', 'product');
    formData.append('session_id', this.sessionId);

    const response = await fetch('/api/upload/temp', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${getAuthToken()}`
      },
      body: formData
    });

    const result = await response.json();

    if (result.status) {
      // ✅ IMPORTANT: Store COMPLETE media objects with metadata
      this.uploadedMedia.push(...result.data.images);

      console.log('Uploaded media with metadata:', this.uploadedMedia);
      // Example:
      // [
      //   {
      //     url: "https://.../temp/.../video.mp4",
      //     path: "temp/.../video.mp4",
      //     media_type: "video",
      //     duration: 14,
      //     file_size: 540048,
      //     thumbnail_url: "https://.../thumbnail.jpg"
      //   }
      // ]

      return result.data;
    }

    throw new Error(result.message);
  }

  /**
   * Move media from temp to permanent with metadata
   */
  async moveMediaToPermanent(productId) {
    // Extract temp paths
    const tempPaths = this.uploadedMedia.map(media => media.path);

    // ✅ Extract metadata for each media
    const metadata = this.uploadedMedia.map(media => ({
      media_type: media.media_type,
      duration: media.duration || null,
      file_size: media.file_size,
      thumbnail_url: media.thumbnail_url || null
    }));

    const requestBody = {
      temp_paths: tempPaths,
      type: 'product',
      product_id: productId,
      metadata: metadata  // ✅ Pass metadata array
    };

    console.log('Moving media with metadata:', requestBody);

    const response = await fetch('/api/upload/move', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${getAuthToken()}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(requestBody)
    });

    const result = await response.json();

    if (result.status) {
      console.log('Moved media with metadata:', result.data.images);
      // ✅ Response now includes metadata!
      // [
      //   {
      //     id: 4,
      //     url: "https://.../products/.../video.mp4",
      //     media_type: "video",
      //     duration: 14,
      //     file_size: 540048,
      //     thumbnail_url: "https://..."
      //   }
      // ]

      return result.data;
    }

    throw new Error(result.message);
  }

  /**
   * Complete product creation flow
   */
  async createProduct(productData) {
    try {
      // 1. Create product first
      const productResponse = await fetch('/api/catalog/products', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${getAuthToken()}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(productData)
      });

      const productResult = await productResponse.json();

      if (!productResult.status) {
        throw new Error(productResult.message);
      }

      const productId = productResult.data.id;

      // 2. Move temp media to permanent location with metadata
      if (this.uploadedMedia.length > 0) {
        const moveResult = await this.moveMediaToPermanent(productId);
        console.log(`Moved ${moveResult.count} media files with complete metadata`);
      }

      return productResult.data;
    } catch (error) {
      console.error('Failed to create product:', error);
      throw error;
    }
  }
}
```

---

## 📊 Complete Example Flow

### Step 1: Upload to Temp

```javascript
const mediaManager = new ProductMediaManager();

// User selects files (images + videos)
document.getElementById('mediaInput').addEventListener('change', async (e) => {
  const files = e.target.files;

  try {
    const result = await mediaManager.uploadToTemp(files);

    // ✅ mediaManager.uploadedMedia now contains complete metadata
    console.log('Uploaded media:', mediaManager.uploadedMedia);
    // [
    //   {
    //     url: "...",
    //     path: "temp/.../video.mp4",
    //     media_type: "video",
    //     duration: 14,
    //     file_size: 540048,
    //     thumbnail_url: "..."
    //   }
    // ]

    // Render gallery
    renderMediaGallery(mediaManager.uploadedMedia);
  } catch (error) {
    alert('Upload failed: ' + error.message);
  }
});
```

### Step 2: Create Product

```javascript
document.getElementById('productForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const productData = {
    name: document.getElementById('name').value,
    description: document.getElementById('description').value,
    price: document.getElementById('price').value,
    // ... other fields
  };

  try {
    const product = await mediaManager.createProduct(productData);

    console.log('Product created with media:', product);
    alert('Product created successfully!');

  } catch (error) {
    alert('Failed: ' + error.message);
  }
});
```

---

## 🔍 Request/Response Example

### 1. Temp Upload Response (Store This!)

```json
POST /api/upload/temp

Response:
{
  "status": true,
  "data": {
    "images": [
      {
        "url": "https://media.minimoda.id/temp/products/temp_123/video.mp4",
        "path": "temp/products/temp_123/video.mp4",
        "filename": "1761214579_abc.mp4",
        "temp": true,
        "media_type": "video",
        "file_size": 540048,
        "duration": 14,
        "thumbnail_url": "https://media.minimoda.id/temp/products/temp_123/thumbnails/video_thumb.jpg"
      }
    ],
    "count": 1,
    "type": "product",
    "is_temp": true,
    "session_id": "temp_123"
  }
}
```

### 2. Move with Metadata

```json
POST /api/upload/move

Request:
{
  "temp_paths": [
    "temp/products/temp_123/video.mp4"
  ],
  "type": "product",
  "product_id": 1,
  "metadata": [
    {
      "media_type": "video",
      "duration": 14,
      "file_size": 540048,
      "thumbnail_url": "https://media.minimoda.id/temp/products/temp_123/thumbnails/video_thumb.jpg"
    }
  ]
}

Response:
{
  "status": true,
  "message": "Images moved successfully",
  "data": {
    "images": [
      {
        "id": 4,
        "url": "https://media.minimoda.id/products/breezy-long-sleeve/1761214717_xyz.mp4",
        "path": "products/breezy-long-sleeve/1761214717_xyz.mp4",
        "is_primary": false,
        "sort_order": 2,
        "media_type": "video",           // ✅ Preserved from temp!
        "file_size": 540048,             // ✅ Preserved from temp!
        "duration": 14,                  // ✅ Preserved from temp!
        "thumbnail_url": "https://media.minimoda.id/products/breezy-long-sleeve/thumbnails/video_thumb.jpg"
      }
    ],
    "count": 1,
    "type": "product",
    "product_id": 1,
    "variant_id": null
  }
}
```

---

## 🎯 Key Points

### 1. **Store Complete Media Objects**
```javascript
// ✅ GOOD: Store complete media object from temp upload
this.uploadedMedia.push(...result.data.images);

// ❌ BAD: Only store URLs
this.uploadedUrls.push(...result.data.images.map(img => img.url));
```

### 2. **Pass Metadata Array to Move**
```javascript
// ✅ GOOD: Extract and pass metadata
const metadata = this.uploadedMedia.map(media => ({
  media_type: media.media_type,
  duration: media.duration,
  file_size: media.file_size,
  thumbnail_url: media.thumbnail_url
}));

// ❌ BAD: Only pass paths without metadata
const requestBody = {
  temp_paths: tempPaths,
  // Missing metadata!
};
```

### 3. **Metadata Array Order Matches temp_paths Order**
```javascript
// ✅ Both arrays must be in same order!
temp_paths:  ["temp/.../video1.mp4", "temp/.../image1.jpg"]
metadata:    [
  { media_type: "video", duration: 14, ... },  // for video1.mp4
  { media_type: "image", duration: null, ... }  // for image1.jpg
]
```

---

## 🐛 Troubleshooting

### Issue: Metadata still missing after move

**Check:**
1. ✅ Are you storing complete media objects from temp upload?
   ```javascript
   console.log('Stored media:', this.uploadedMedia);
   // Should show: media_type, duration, file_size
   ```

2. ✅ Are you passing metadata array in move request?
   ```javascript
   console.log('Move request body:', requestBody);
   // Should include: metadata: [...]
   ```

3. ✅ Is metadata array in correct order (matching temp_paths)?

4. ✅ Check backend logs:
   ```bash
   tail -f storage/logs/laravel.log | grep "Moving media"
   ```

### Issue: Thumbnail not moved

**Check logs:**
```bash
tail -f storage/logs/laravel.log | grep "thumbnail"
```

**Expected log:**
```
[INFO] Moved video thumbnail {
  "from": "temp/.../thumbnails/video_thumb.jpg",
  "to": "products/.../thumbnails/video_thumb.jpg"
}
```

---

## ✅ Updated Validation

Backend now accepts optional `metadata` array:

```php
'metadata' => 'nullable|array',
'metadata.*.duration' => 'nullable|integer',
'metadata.*.media_type' => 'nullable|string|in:image,video',
'metadata.*.file_size' => 'nullable|integer',
'metadata.*.thumbnail_url' => 'nullable|string',
```

If `metadata` not provided:
- ✅ Backend will fallback to detect media_type from extension
- ✅ Backend will get file_size from FTP
- ❌ Duration will be `null` (cannot be extracted from FTP file)
- ❌ Thumbnail URL will be lost if not moved

**Recommendation:** Always pass metadata for complete data preservation!

---

## 📝 Summary

### Frontend Changes Required:

1. **Store complete media objects from temp upload response**
   ```javascript
   this.uploadedMedia.push(...result.data.images);
   ```

2. **Extract metadata array before move**
   ```javascript
   const metadata = this.uploadedMedia.map(media => ({
     media_type: media.media_type,
     duration: media.duration,
     file_size: media.file_size,
     thumbnail_url: media.thumbnail_url
   }));
   ```

3. **Pass metadata array in move request**
   ```javascript
   {
     temp_paths: [...],
     metadata: metadata  // ✅ Add this!
   }
   ```

4. **Response will now include complete metadata!**

---

## 🚀 Ready!

Backend sudah updated untuk:
- ✅ Accept metadata array dari frontend
- ✅ Preserve metadata saat move
- ✅ Move thumbnail files
- ✅ Save metadata lengkap ke database
- ✅ Return metadata lengkap di response

Frontend tinggal update untuk pass metadata array! 🎉
