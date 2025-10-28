# SKU & Barcode Auto-Generation Documentation

## Overview
System auto-generates SKU dan Barcode saat membuat variant baru. Frontend hanya perlu mengirim data size, color, stock, dll tanpa SKU & Barcode.

## SKU Format

### Format Structure
```
MM-{BRAND_CODE}-{CATEGORY_CODE}-{SIZE_CODE}-{COLOR_CODE}-{STOCK}
```

### Example
**Input:**
- Brand: Minimoda (code: MI)
- Category: Kemeja (code: KMJ)
- Size: 5-6
- Color: Clear Blue
- Stock: 10

**Generated SKU:**
```
MM-MI-KMJ-56-CB-10
```

### Components

#### 1. Prefix: `MM`
- Fixed prefix dari config `app.sku_prefix`
- Default: `MM` (Minimoda)

#### 2. Brand Code: `{BRAND_CODE}`
- 2-character brand code
- Diambil dari column `brands.code`
- Contoh: Minimoda → `MI`, Uriah David → `UR`

#### 3. Category Code: `{CATEGORY_CODE}`
- 3-character category code
- Diambil dari column `categories.code`
- Contoh: Kemeja → `KMJ`, Kaos → `KSX`, Dress → `DRE`

#### 4. Size Code: `{SIZE_CODE}`
- Size dengan special characters dihapus (space, dash, slash)
- Uppercase
- Contoh:
  - `5-6` → `56`
  - `S` → `S`
  - `2 - 3` → `23`
  - `0-3m` → `03M`

#### 5. Color Code: `{COLOR_CODE}`
- 2-3 character color code
- Auto-detect untuk multi-word colors (ambil huruf pertama tiap kata)
- Predefined mapping untuk warna umum
- Contoh:
  - `Clear Blue` → `CB`
  - `Navy Blue` → `NB`
  - `Dark Red` → `DR`
  - `Merah` → `RED`
  - `Biru` → `BLU`

#### 6. Stock: `{STOCK}`
- Stock quantity saat variant dibuat
- Contoh: `10`, `50`, `100`

### Color Code Mapping

#### Bahasa Indonesia
- `merah` → `RED`
- `biru` → `BLU`
- `hijau` → `GRN`
- `kuning` → `YLW`
- `pink` → `PNK`
- `ungu` → `PRP`
- `hitam` → `BLK`
- `putih` → `WHT`
- `abu-abu` / `abu` → `GRY`
- `coklat` → `BRN`
- `orange` / `oranye` → `ORG`

#### English
- `clear blue` → `CB`
- `navy blue` → `NB`
- `dark blue` → `DB`
- `light blue` → `LB`
- `sky blue` → `SB`

#### Multi-word Colors (Auto-detect)
Untuk warna yang tidak ada di mapping, sistem akan ambil huruf pertama dari setiap kata:
- `Soft Pink` → `SP`
- `Dark Green` → `DG`
- `Bright Yellow` → `BY`

## Barcode Format

### EAN-13 (Default)
```
{COMPANY_PREFIX}{PRODUCT_CODE}{CHECK_DIGIT}
```

**Example:**
```
8991230000011
└─┬──┘└──┬─┘└┘
  │     │   └─ Check digit (calculated)
  │     └───── Sequential product code (6 digits)
  └─────────── Company prefix (from config)
```

### Configuration
```env
BARCODE_COMPANY_PREFIX=899123
BARCODE_TYPE=EAN13
BARCODE_AUTO_GENERATE=true
```

### Features
- Sequential numbering per company prefix
- Check digit calculation (EAN-13 standard)
- Uniqueness validation
- Alternative formats: UPC, CODE128

## API Integration

### Create Variant (Auto-generate)

**Endpoint:** `POST /api/catalog/products/variants`

**Request (tanpa SKU & barcode):**
```json
{
  "product_id": 1,
  "size": "5-6",
  "color": "Clear Blue",
  "weight_gram": 200,
  "price": 150000,
  "stock_quantity": 10,
  "images": []
}
```

**Response:**
```json
{
  "status": true,
  "statusCode": "201",
  "message": "Variant created successfully",
  "data": {
    "id": 1,
    "product_id": 1,
    "sku": "MM-MI-KMJ-56-CB-10",
    "barcode": "8991230000011",
    "size": "5-6",
    "color": "Clear Blue",
    "stock_quantity": 10,
    "price": 150000,
    ...
  }
}
```

### Manual Generation Endpoints (optional)

#### Generate SKU Only
```
POST /api/catalog/products/variants/{id}/generate-sku
```

#### Generate Barcode Only
```
POST /api/catalog/products/variants/{id}/generate-barcode
```

#### Generate Both (and save)
```
POST /api/catalog/products/variants/{id}/generate-sku-barcode
```

#### Batch Generate
```
POST /api/catalog/products/{productId}/variants/batch-generate
{
  "generate_all": true
}
```

## Frontend Flow

### Smooth Create Flow
1. User mengisi form:
   - Size: `5-6`
   - Color: `Clear Blue`
   - Stock: `10`
   - Price: `150000`
   - Weight: `200g`
   - (SKU & Barcode **tidak perlu diisi**)

2. Click "Save"

3. Frontend send POST request ke `/api/catalog/products/variants`

4. Backend:
   - Create variant dengan temp SKU
   - Auto-generate SKU: `MM-MI-KMJ-56-CB-10`
   - Auto-generate Barcode: `8991230000011`
   - Update variant dengan SKU & Barcode

5. Response berisi variant lengkap dengan SKU & Barcode

### Validation
- SKU uniqueness dihandle otomatis (append suffix jika duplicate)
- Barcode sequential & unique
- Brand & Category code harus ada di database

## Examples

### Example 1: Kemeja Anak
```
Input:
- Product: Kemeja Anak Casual
- Brand: Minimoda (MI)
- Category: Kemeja (KMJ)
- Size: 5-6
- Color: Clear Blue
- Stock: 10

Output SKU: MM-MI-KMJ-56-CB-10
Output Barcode: 8991230000011
```

### Example 2: Dress
```
Input:
- Product: Dress Anak Flower
- Brand: Uriah David (UR)
- Category: Dress (DRE)
- Size: 2-3
- Color: Soft Pink
- Stock: 25

Output SKU: MM-UR-DRE-23-SP-25
Output Barcode: 8991230000012
```

### Example 3: Kaos
```
Input:
- Product: Kaos Polo Anak
- Brand: Minimoda (MI)
- Category: Kaos Polo (KAO)
- Size: M
- Color: Navy Blue
- Stock: 50

Output SKU: MM-MI-KAO-M-NB-50
Output Barcode: 8991230000013
```

## Configuration

### .env Settings
```env
# SKU Configuration
SKU_PREFIX=MM
SKU_FORMAT=simple

# Barcode Configuration
BARCODE_COMPANY_PREFIX=899123
BARCODE_TYPE=EAN13
BARCODE_AUTO_GENERATE=true
```

### Brand & Category Codes
Run seeder untuk generate codes:
```bash
php artisan db:seed --class=GenerateBrandCategoryCodesSeeder
```

## Notes

### SKU Uniqueness
Jika generated SKU sudah ada, sistem akan append suffix:
- `MM-MI-KMJ-56-CB-10` (original)
- `MM-MI-KMJ-56-CB-10-01` (if duplicate)
- `MM-MI-KMJ-56-CB-10-02` (if still duplicate)

### Stock in SKU
Stock di SKU menunjukkan initial stock saat variant dibuat. Tidak akan update otomatis saat stock berubah.

### Color Code Expansion
Anda bisa menambah mapping color di `app/Helpers/SkuBarcodeGenerator.php`:
```php
$colorMap = [
    'clear blue' => 'CB',
    'navy blue' => 'NB',
    // Add more colors here
];
```

## Troubleshooting

### Issue: Brand/Category code NULL
**Solution:** Run seeder untuk generate codes
```bash
php artisan db:seed --class=GenerateBrandCategoryCodesSeeder
```

### Issue: SKU terlalu panjang
**Solution:** SKU format sudah optimized. Max length ≈ 25 characters.

### Issue: Color code tidak sesuai
**Solution:** Tambahkan mapping di `generateColorCode()` method atau gunakan consistent naming di frontend.
