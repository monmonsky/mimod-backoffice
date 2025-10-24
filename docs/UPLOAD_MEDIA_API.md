# Upload Media API Documentation

## Overview
Endpoint `/api/upload/temp` sekarang support upload **image dan video** dalam satu endpoint yang sama. System auto-detect media type dari file yang di-upload.

## Endpoint

### Upload Temporary Media (Images + Videos)

**Endpoint:** `POST /api/upload/temp`

**Content-Type:** `multipart/form-data`

**Request Body:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `images[]` | File[] | Yes | Array of image/video files (max 20 files) |
| `type` | string | Yes | `product` or `variant` |
| `session_id` | string | No | Unique identifier untuk grouping temp files |
| `product_id` | integer | No | If editing existing product (akan upload langsung ke permanent folder) |
| `variant_id` | integer | No | If editing existing variant (akan upload langsung ke permanent folder) |
| `alt_text` | string | No | Alt text untuk semua media (max 255 chars) |

**Supported File Types:**

**Images:**
- jpeg, jpg, png, gif, webp, svg

**Videos:**
- mp4, mov, avi, webm, mkv

**Max File Size:**
- Images: 20MB
- Videos: 100MB (configurable)

**Max Files per Request:** 20 files

---

## Usage Examples

### Example 1: Upload Mixed Media (Temp)

Frontend upload 3 images + 1 video untuk product baru (belum punya product_id):

```javascript
const formData = new FormData();

// Add images
formData.append('images[]', imageFile1); // image.jpg
formData.append('images[]', imageFile2); // image2.png
formData.append('images[]', videoFile);  // video.mp4
formData.append('images[]', imageFile3); // image3.jpg

formData.append('type', 'product');
formData.append('session_id', 'unique-session-123');
formData.append('alt_text', 'Product showcase');

fetch('/api/upload/temp', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN'
  },
  body: formData
})
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Temporary images uploaded successfully",
  "data": {
    "images": [
      {
        "url": "https://cdn.minimoda.com/temp/products/unique-session-123/1234567_abc.jpg",
        "path": "temp/products/unique-session-123/1234567_abc.jpg",
        "filename": "1234567_abc.jpg",
        "temp": true,
        "media_type": "image",
        "file_size": 245678
      },
      {
        "url": "https://cdn.minimoda.com/temp/products/unique-session-123/1234568_def.png",
        "path": "temp/products/unique-session-123/1234568_def.png",
        "filename": "1234568_def.png",
        "temp": true,
        "media_type": "image",
        "file_size": 189234
      },
      {
        "url": "https://cdn.minimoda.com/temp/products/unique-session-123/1234569_ghi.mp4",
        "path": "temp/products/unique-session-123/1234569_ghi.mp4",
        "filename": "1234569_ghi.mp4",
        "temp": true,
        "media_type": "video",
        "file_size": 5234567,
        "duration": 30
      },
      {
        "url": "https://cdn.minimoda.com/temp/products/unique-session-123/1234570_jkl.jpg",
        "path": "temp/products/unique-session-123/1234570_jkl.jpg",
        "filename": "1234570_jkl.jpg",
        "temp": true,
        "media_type": "image",
        "file_size": 198765
      }
    ],
    "count": 4,
    "type": "product",
    "is_temp": true,
    "session_id": "unique-session-123",
    "note": "These images are temporary and will be moved when product/variant is saved"
  }
}
```

---

### Example 2: Upload Media Langsung (Existing Product)

Upload media langsung ke product yang sudah ada (product_id provided):

```javascript
const formData = new FormData();

formData.append('images[]', imageFile);
formData.append('images[]', videoFile);

formData.append('type', 'product');
formData.append('product_id', 123); // Existing product ID
formData.append('alt_text', 'Additional product media');

fetch('/api/upload/temp', {
  method: 'POST',
  body: formData
})
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Images uploaded and saved successfully",
  "data": {
    "images": [
      {
        "url": "https://cdn.minimoda.com/products/kemeja-anak-casual/1234567_abc.jpg",
        "path": "products/kemeja-anak-casual/1234567_abc.jpg",
        "filename": "1234567_abc.jpg",
        "temp": false,
        "media_type": "image",
        "file_size": 245678,
        "id": 45,
        "is_primary": false,
        "sort_order": 3,
        "alt_text": "Additional product media"
      },
      {
        "url": "https://cdn.minimoda.com/products/kemeja-anak-casual/1234568_def.mp4",
        "path": "products/kemeja-anak-casual/1234568_def.mp4",
        "filename": "1234568_def.mp4",
        "temp": false,
        "media_type": "video",
        "file_size": 5234567,
        "duration": 25,
        "id": 46,
        "is_primary": false,
        "sort_order": 4,
        "alt_text": "Additional product media"
      }
    ],
    "count": 2,
    "type": "product",
    "is_temp": false,
    "product_id": 123
  }
}
```

---

### Example 3: Upload Variant Media

```javascript
const formData = new FormData();

formData.append('images[]', imageFile);
formData.append('images[]', videoFile);

formData.append('type', 'variant');
formData.append('variant_id', 456); // Existing variant ID

fetch('/api/upload/temp', {
  method: 'POST',
  body: formData
})
```

---

## Response Fields

### Media Object Fields

| Field | Type | Description |
|-------|------|-------------|
| `url` | string | Full CDN URL to access the file |
| `path` | string | Relative path in storage |
| `filename` | string | Generated unique filename |
| `temp` | boolean | Is this a temporary upload? |
| `media_type` | string | `image` or `video` (auto-detected) |
| `file_size` | integer | File size in bytes |
| `duration` | integer | Video duration in seconds (videos only, optional) |
| `id` | integer | Database ID (only if saved to DB) |
| `is_primary` | boolean | Is this the primary media? (only if saved to DB) |
| `sort_order` | integer | Display order (only if saved to DB) |
| `alt_text` | string | Alt text for accessibility (only if saved to DB) |

---

## Auto-Detection Logic

### Media Type Detection
```
MIME Type starts with "video/" → media_type = "video"
MIME Type starts with "image/" → media_type = "image"
```

### Video Duration (Optional)
Jika library `getID3` tersedia, sistem akan otomatis extract video duration.

**Install getID3 (optional):**
```bash
composer require james-heinrich/getid3
```

Jika tidak ada, field `duration` akan `null`.

---

## Frontend Flow

### Flow 1: Create New Product with Media

```javascript
// Step 1: Upload media to temp
const uploadResponse = await uploadToTemp(files, 'product', sessionId);
const tempUrls = uploadResponse.data.images.map(img => img.url);

// Step 2: Create product dengan temp URLs
const productData = {
  name: "Kemeja Anak Casual",
  description: "...",
  images: tempUrls, // Pass temp URLs
  // ...
};

const createResponse = await createProduct(productData);
// Backend will move temp files to permanent location
```

### Flow 2: Add Media to Existing Product

```javascript
// Upload langsung dengan product_id
const uploadResponse = await uploadToTemp(files, 'product', null, productId);
// Media langsung tersimpan di product
```

---

## Database Schema

### product_images & product_variant_images

```sql
CREATE TABLE product_images (
  id SERIAL PRIMARY KEY,
  product_id INTEGER REFERENCES products(id),
  url VARCHAR(500) NOT NULL,
  alt_text VARCHAR(255),
  is_primary BOOLEAN DEFAULT FALSE,
  sort_order INTEGER DEFAULT 1,

  -- New fields for video support
  media_type VARCHAR(10) DEFAULT 'image', -- 'image' or 'video'
  thumbnail_url VARCHAR(500),             -- For video thumbnails
  duration INTEGER,                       -- Video duration in seconds
  file_size BIGINT,                       -- File size in bytes

  created_at TIMESTAMP
);
```

---

## Validation Rules

### Request Validation

```php
'images' => 'required|array|min:1|max:20',
'images.*' => 'required|file|mimes:jpeg,jpg,png,gif,webp,svg,mp4,mov,avi,webm,mkv|max:102400', // 100MB
'type' => 'required|string|in:product,variant',
'session_id' => 'nullable|string',
'product_id' => 'nullable|integer|exists:products,id',
'variant_id' => 'nullable|integer|exists:product_variants,id',
'alt_text' => 'nullable|string|max:255',
```

---

## Error Responses

### Validation Error
```json
{
  "status": false,
  "statusCode": "422",
  "message": "Validation failed",
  "data": {
    "errors": {
      "images.0": ["The file must be a file of type: jpeg, jpg, png, gif, webp, svg, mp4, mov, avi, webm, mkv."],
      "images.1": ["The file may not be greater than 102400 kilobytes."]
    }
  }
}
```

### Product Not Found
```json
{
  "status": false,
  "statusCode": "404",
  "message": "Product not found",
  "data": []
}
```

---

## Notes

### File Naming
- Format: `{timestamp}_{uniqid}.{extension}`
- Example: `1729696545_673a8b21e4f2.mp4`

### Directory Structure

**Temporary uploads:**
```
temp/
  └── products/
      └── {session_id}/
          ├── image1.jpg
          ├── video1.mp4
          └── image2.png
```

**Permanent uploads:**
```
products/
  └── {product-slug}/
      ├── image1.jpg
      ├── video1.mp4
      ├── variants/
      │   ├── image1.jpg
      │   └── video1.mp4
```

### Media Type in Response
- `media_type: "image"` → Can be displayed with `<img>` tag
- `media_type: "video"` → Must use `<video>` tag

### Video Duration
- Optional field, requires `getID3` library
- Returns duration in seconds (integer)
- If library not available or extraction fails: `duration: null`

### Backward Compatibility
- Old API calls that only send images will still work
- `media_type` will default to `"image"` for existing records
- New fields (`duration`, `file_size`, etc.) are nullable

---

## Testing

### cURL Example: Upload Mixed Media

```bash
curl -X POST https://api.minimoda.com/api/upload/temp \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "images[]=@/path/to/image1.jpg" \
  -F "images[]=@/path/to/video.mp4" \
  -F "images[]=@/path/to/image2.png" \
  -F "type=product" \
  -F "session_id=test-session-123" \
  -F "alt_text=Product showcase media"
```

### Expected Response
```json
{
  "status": true,
  "message": "Temporary images uploaded successfully",
  "data": {
    "images": [
      {
        "media_type": "image",
        "url": "https://cdn.minimoda.com/temp/products/test-session-123/...",
        "file_size": 245678
      },
      {
        "media_type": "video",
        "url": "https://cdn.minimoda.com/temp/products/test-session-123/...",
        "file_size": 5234567,
        "duration": 30
      },
      {
        "media_type": "image",
        "url": "https://cdn.minimoda.com/temp/products/test-session-123/...",
        "file_size": 189234
      }
    ],
    "count": 3
  }
}
```

---

## Summary

✅ **Single endpoint** untuk upload image + video
✅ **Auto-detect** media type dari MIME type
✅ **Support temp upload** dengan session_id
✅ **Support direct upload** dengan product_id/variant_id
✅ **Video metadata** (duration, file_size) otomatis ter-extract
✅ **Backward compatible** dengan existing API calls
✅ **Validation** untuk file type dan size

**Frontend hanya perlu:**
1. Pilih files (images + videos mixed)
2. POST ke `/api/upload/temp` dengan FormData
3. Backend handle semua logic (detection, storage, database)
