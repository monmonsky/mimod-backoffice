# Add Product Partials Summary

Create these 4 partial blade files in `resources/views/pages/catalog/add-products/partials/`:

## 1. basic-info.blade.php

Product basic information form with:
- Product Name (with auto-slug generation)
- Slug (editable)
- Description (textarea)
- Brand (select dropdown from $brands)
- Age Range: Min & Max in months
- Tags (comma-separated input)
- Status (select: active, inactive, draft)
- Featured checkbox
- Save button

Form ID: `productForm`
Method: POST for create, PUT for edit
Action: `/catalog/products/store` or `/catalog/products/{id}`

## 2. categories.blade.php

Multi-select categories with:
- Checkbox list of categories
- Show parent-child hierarchy
- Pre-select categories if editing (use $selectedCategories array)
- Save button (same as basic info, updates same product)

Render categories from $categories variable
Use recursive loop or tree structure

## 3. images.blade.php

Image management section (only shown when editing):
- Multiple image upload input
- Preview uploaded images in grid
- Drag & drop to reorder images (Sortable.js)
- Set primary image (star icon)
- Delete individual images
- Show product images from $product->images if available

Upload URL: POST `/catalog/products/{id}/images/upload`
Delete URL: DELETE `/catalog/products/{productId}/images/{imageId}`
Set Primary: POST `/catalog/products/{productId}/images/{imageId}/set-primary`
Reorder: POST `/catalog/products/{productId}/images/update-order`

## 4. variants.blade.php

Variants management table (only shown when editing):
- Table showing all variants
- Columns: SKU, Size, Color, Price, Compare Price, Stock, Actions
- Add Variant button (opens modal)
- Edit/Delete buttons per variant
- Show low stock warning (< 10 items)

Table renders $product->variants if available

Add Variant URL: POST `/catalog/products/{productId}/variants/store`
Update Variant URL: PUT `/catalog/products/{productId}/variants/{variantId}`
Delete Variant URL: DELETE `/catalog/products/{productId}/variants/{variantId}`

---

## Form Data Flow

### Create Product Flow:
1. Fill Basic Info tab → Save → Creates product → Redirects to edit mode
2. Now Categories, Images, Variants tabs appear
3. Fill other tabs and save

### Edit Product Flow:
1. All 4 tabs visible
2. Basic Info + Categories can be updated
3. Images can be uploaded/deleted/reordered
4. Variants can be added/edited/deleted

### JavaScript Handles:
- Auto-slug generation from product name
- Form submission (AJAX)
- Category selection
- Image upload with preview
- Image drag & drop reordering
- Variant modal (add/edit)
- Variant CRUD operations

All forms use AJAX to prevent page reload and show Toast notifications.
