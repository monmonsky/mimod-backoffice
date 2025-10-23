# Frontend AI SEO Integration Guide

## Endpoint
```
POST /api/ai/generate-seo
```

**Authentication:** Required (Bearer Token)

---

## Request Format

### Headers
```javascript
{
  "Authorization": "Bearer YOUR_ACCESS_TOKEN",
  "Content-Type": "application/json"
}
```

### Request Body
```typescript
{
  name: string;           // Required - Product name
  description: string;    // Required - Product description
  brand_name?: string;    // Optional - Brand name
  categories?: string[];  // Optional - Array of category names
  tags?: string[];        // Optional - Array of tags
  age_min?: number;       // Optional - Minimum age
  age_max?: number;       // Optional - Maximum age
}
```

---

## Frontend Implementation Examples

### 1. Using Composable (Recommended)

File: `composables/useAISeo.ts` (sudah ada)

```typescript
import { useAISeo } from '~/composables/useAISeo'

// In your component
const { generateSeo, loading, error } = useAISeo()

// When user clicks "Generate SEO" button
const handleGenerateSeo = async () => {
  const result = await generateSeo({
    name: form.value.name,
    description: form.value.description,
    brand_name: selectedBrand.value?.name,
    categories: selectedCategories.value.map(c => c.name),
    tags: form.value.tags,
    age_min: form.value.age_min,
    age_max: form.value.age_max
  })

  if (result) {
    // Auto-fill SEO fields
    form.value.seo_meta = {
      title: result.title,
      description: result.description,
      keywords: result.keywords
    }
  }
}
```

---

### 2. Direct API Call (Alternative)

```typescript
import { useAuthStore } from '~/stores/auth'

const authStore = useAuthStore()

async function generateSeoMetadata(productData: any) {
  try {
    const response = await $fetch('/api/ai/generate-seo', {
      method: 'POST',
      baseURL: 'http://api-local.minimoda.id',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json'
      },
      body: {
        name: productData.name,
        description: productData.description,
        brand_name: productData.brand_name,
        categories: productData.categories,
        tags: productData.tags,
        age_min: productData.age_min,
        age_max: productData.age_max
      }
    })

    if (response.status) {
      return response.data
    }
  } catch (error) {
    console.error('Failed to generate SEO:', error)
    throw error
  }
}
```

---

### 3. Complete Component Example

```vue
<template>
  <div class="seo-generator">
    <!-- SEO Title Field -->
    <div class="form-group">
      <label>SEO Title</label>
      <input
        v-model="form.seo_meta.title"
        type="text"
        maxlength="60"
        placeholder="SEO Title (max 60 characters)"
      />
      <span class="character-count">{{ form.seo_meta.title?.length || 0 }}/60</span>
    </div>

    <!-- SEO Description Field -->
    <div class="form-group">
      <label>Meta Description</label>
      <textarea
        v-model="form.seo_meta.description"
        maxlength="160"
        placeholder="Meta description (120-160 characters)"
      />
      <span class="character-count">{{ form.seo_meta.description?.length || 0 }}/160</span>
    </div>

    <!-- SEO Keywords Field -->
    <div class="form-group">
      <label>Keywords</label>
      <input
        v-model="form.seo_meta.keywords"
        type="text"
        placeholder="keyword1, keyword2, keyword3"
      />
    </div>

    <!-- Generate AI SEO Button -->
    <button
      @click="handleGenerateAiSeo"
      :disabled="loading || !canGenerateSeo"
      class="btn-generate-seo"
    >
      <LoadingSpinner v-if="loading" />
      <span v-else>✨ Generate SEO dengan AI</span>
    </button>

    <!-- Error Message -->
    <div v-if="error" class="error-message">
      {{ error }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useAISeo } from '~/composables/useAISeo'

const props = defineProps<{
  form: any
  selectedBrand: any
  selectedCategories: any[]
}>()

const { generateSeo, loading, error } = useAISeo()

// Check if minimum data is available to generate SEO
const canGenerateSeo = computed(() => {
  return props.form.name && props.form.description
})

const handleGenerateAiSeo = async () => {
  if (!canGenerateSeo.value) {
    error.value = 'Please fill in product name and description first'
    return
  }

  const result = await generateSeo({
    name: props.form.name,
    description: props.form.description,
    brand_name: props.selectedBrand?.name || '',
    categories: props.selectedCategories.map(c => c.name),
    tags: props.form.tags || [],
    age_min: props.form.age_min,
    age_max: props.form.age_max
  })

  if (result) {
    // Auto-fill SEO metadata
    if (!props.form.seo_meta) {
      props.form.seo_meta = {}
    }

    props.form.seo_meta.title = result.title
    props.form.seo_meta.description = result.description
    props.form.seo_meta.keywords = result.keywords

    // Show success notification
    showNotification('SEO metadata generated successfully!', 'success')
  }
}
</script>
```

---

## Response Format

### Success Response
```typescript
{
  status: true,
  statusCode: "200",
  message: "SEO generated successfully",
  data: {
    title: string,        // SEO Title (max 60 chars)
    description: string,  // Meta Description (120-160 chars)
    keywords: string      // Comma-separated keywords
  }
}
```

**Example:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "SEO generated successfully",
  "data": {
    "title": "Puzzle Kayu Edukasi Anak SmartKids - Ramah Lingkungan",
    "description": "Mainan puzzle kayu berkualitas tinggi dari SmartKids. Kembangkan kemampuan kognitif anak usia 3-7 tahun. Bahan eco-friendly & aman. Beli sekarang!",
    "keywords": "puzzle kayu anak, mainan edukasi, smartkids, puzzle eco-friendly, mainan kayu, edukasi anak, cognitive development, mainan usia 3-7 tahun"
  }
}
```

---

### Error Response

#### Validation Error (422)
```json
{
  "status": false,
  "statusCode": "422",
  "message": "The name field is required.",
  "data": {},
  "errors": {
    "name": ["The name field is required."],
    "description": ["The description field is required."]
  }
}
```

#### Server Error (500)
```json
{
  "status": false,
  "statusCode": "500",
  "message": "Failed to generate SEO: Gemini API key not configured",
  "data": {}
}
```

#### Authentication Error (401)
```json
{
  "status": false,
  "statusCode": "401",
  "message": "Unauthenticated",
  "data": {}
}
```

---

## Request Examples

### Example 1: Minimal Request
```javascript
{
  "name": "Puzzle Kayu Anak",
  "description": "Puzzle kayu berkualitas untuk anak usia 3-7 tahun"
}
```

### Example 2: Complete Request
```javascript
{
  "name": "Mainan Edukasi Puzzle Kayu Geometri",
  "description": "Puzzle kayu berkualitas tinggi dengan bentuk geometri untuk mengembangkan kemampuan kognitif dan motorik anak. Terbuat dari bahan kayu ramah lingkungan, aman untuk anak-anak.",
  "brand_name": "SmartKids",
  "categories": ["Mainan", "Edukasi", "Puzzle", "Kayu"],
  "tags": ["kayu", "eco-friendly", "edukasi", "puzzle", "geometri"],
  "age_min": 3,
  "age_max": 7
}
```

### Example 3: From Product Form
```javascript
// Gather data from form
const requestData = {
  name: productForm.name,
  description: productForm.description,
  brand_name: brands.find(b => b.id === productForm.brand_id)?.name,
  categories: productForm.category_ids
    .map(id => categories.find(c => c.id === id)?.name)
    .filter(Boolean),
  tags: Array.isArray(productForm.tags)
    ? productForm.tags
    : productForm.tags?.split(',').map(t => t.trim()),
  age_min: productForm.age_min,
  age_max: productForm.age_max
}

// Send request
const seoData = await generateSeo(requestData)
```

---

## Error Handling

```typescript
const { generateSeo, loading, error } = useAISeo()

try {
  const result = await generateSeo(requestData)

  if (result) {
    // Success - auto-fill fields
    form.value.seo_meta = result
    toast.success('SEO generated successfully!')
  }
} catch (err) {
  // Error handling
  if (err.statusCode === 422) {
    toast.error('Please fill in required fields: name and description')
  } else if (err.statusCode === 401) {
    toast.error('Authentication required. Please login again.')
    router.push('/login')
  } else if (err.statusCode === 500) {
    toast.error('Server error. Please try again later.')
  } else {
    toast.error(err.message || 'Failed to generate SEO')
  }
}
```

---

## Best Practices

### 1. **Validate Before Sending**
```typescript
const canGenerateSeo = computed(() => {
  return form.value.name?.trim() && form.value.description?.trim()
})
```

### 2. **Show Loading State**
```vue
<button :disabled="loading">
  <LoadingSpinner v-if="loading" />
  <span v-else>Generate SEO</span>
</button>
```

### 3. **Handle Rate Limits**
```typescript
// Implement debounce to prevent spam clicks
import { useDebounceFn } from '@vueuse/core'

const debouncedGenerateSeo = useDebounceFn(async () => {
  await generateSeo(requestData)
}, 1000)
```

### 4. **Preview Before Saving**
```vue
<div v-if="generatedSeo" class="preview">
  <h4>Preview Generated SEO:</h4>
  <div class="preview-item">
    <strong>Title:</strong> {{ generatedSeo.title }}
  </div>
  <div class="preview-item">
    <strong>Description:</strong> {{ generatedSeo.description }}
  </div>
  <div class="preview-item">
    <strong>Keywords:</strong> {{ generatedSeo.keywords }}
  </div>

  <button @click="applySeo">Apply to Form</button>
  <button @click="regenerate">Regenerate</button>
</div>
```

### 5. **Cache Results (Optional)**
```typescript
// Save last generated SEO for this product
const lastGeneratedSeo = ref(null)

watch(() => form.value.name, () => {
  // Clear cache when product name changes
  lastGeneratedSeo.value = null
})
```

---

## Testing

### Test with curl:
```bash
curl -X POST http://api-local.minimoda.id/api/ai/generate-seo \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Product",
    "description": "Test description for SEO generation",
    "brand_name": "Test Brand",
    "categories": ["Category1", "Category2"],
    "tags": ["tag1", "tag2"],
    "age_min": 3,
    "age_max": 7
  }'
```

---

## Notes

- **Rate Limits**: Gemini Free Tier allows 15 requests/minute, 1,500 requests/day
- **Timeout**: Request timeout is set to 30 seconds
- **Language**: Generated SEO will be in Indonesian (Bahasa Indonesia)
- **Character Limits**:
  - Title: max 60 characters
  - Description: 120-160 characters
  - Keywords: 5-10 keywords

---

## Troubleshooting

### Problem: "Gemini API key not configured"
**Solution:** Contact backend admin to set `GEMINI_API_KEY` in `.env`

### Problem: "Request timeout"
**Solution:** Check internet connection or try again later

### Problem: "Invalid JSON format"
**Solution:** This is an AI response parsing issue. Report to backend team.

### Problem: Rate limit exceeded
**Solution:** Wait 1 minute before trying again

---

**Last Updated:** 2025-01-13
