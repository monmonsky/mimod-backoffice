# Product Dummy Data Seeder Documentation

## Overview
Seeder ini membuat 50 produk dummy lengkap dengan variant dan gambar untuk keperluan development dan testing.

## File Location
```
database/seeders/ProductDummySeeder.php
```

## Features

### 1. Product Generation
- **Jumlah**: 50 produk
- **Jenis Produk**: 10 kategori (Kemeja, Kaos, Dress, Celana Jeans, Romper, Jumpsuit, Rok, Jaket, Sweater, Cardigan)
- **Variasi**: Kombinasi warna dan pattern yang berbeda
- **Status**: 90% active, 10% draft
- **Featured**: 30% produk akan di-mark sebagai featured
- **SEO Meta**: Setiap produk dilengkapi dengan SEO title, description, dan keywords
- **Categories**: Setiap produk di-attach ke 1-3 kategori random

### 2. Product Variants
- **Jumlah per Produk**: 2-4 warna variant per produk
- **Sizes**: XS, S, M, L, XL (70% chance untuk setiap size)
- **Price Range**: Rp 50,000 - Rp 300,000
- **Compare Price**: 50% variant memiliki compare_at_price (harga coret)
- **Stock**: Random 0-100 unit per variant
- **SKU**: Auto-generated unique SKU
- **Barcode**: 50% variant memiliki barcode
- **Weight**: Random 100-500 gram

### 3. Product Images
- **Jumlah per Produk**: 2-5 gambar
- **Primary Image**: Gambar pertama otomatis di-set sebagai primary
- **Image URLs**: Menggunakan placeholder dari Unsplash (foto anak)
- **Alt Text**: Deskriptif untuk SEO
- **Sort Order**: Terurut dari 0 sampai n-1

### 4. Product Variant Images
- **Jumlah per Variant**: 1-3 gambar per variant
- **Primary Image**: Gambar pertama otomatis di-set sebagai primary
- **Image URLs**: Menggunakan placeholder dari Unsplash (foto anak)
- **Alt Text**: Include product name, color, size, dan nomor gambar
- **Sort Order**: Terurut dari 0 sampai n-1
- **Purpose**: Menampilkan gambar spesifik untuk kombinasi warna dan size tertentu

## Requirements

Sebelum menjalankan seeder, pastikan sudah ada data:
1. **Brands** - Minimal 1 brand
2. **Categories** - Minimal 1 kategori

## Usage

### Running the Seeder

```bash
# Jalankan seeder
php artisan db:seed --class=ProductDummySeeder
```

### Expected Output

```
Starting to seed 50 products...
Seeded 10 products...
Seeded 20 products...
Seeded 30 products...
Seeded 40 products...
Seeded 50 products...
Successfully seeded 50 products with variants and images!
Summary:
- Total Products: 102
- Total Variants: 979
- Total Product Images: 362
- Total Variant Images: 942
```

**Notes:**
- Total counts include both new and previously seeded data
- Each run adds 50 new products
- Variants count depends on random selection (70% probability per size)
- Variant images: 1-3 images per variant (random)

## Data Structure

### Products Table
```php
[
    'name' => 'Kemeja Anak Motif Bunga Pink',
    'slug' => 'kemeja-anak-motif-bunga-pink-1234567890-1',
    'description' => 'Koleksi Kemeja anak terbaru...',
    'brand_id' => 28,
    'age_min' => 3,
    'age_max' => 12,
    'tags' => '["kemeja","formal","casual","Pink","Motif Bunga"]',
    'status' => 'active',
    'seo_meta' => '{
        "title": "Kemeja Anak Motif Bunga Pink - Pakaian Anak Berkualitas | Minimoda",
        "description": "Koleksi Kemeja anak terbaru dengan desain Motif Bunga...",
        "keywords": "kemeja, formal, casual, pakaian anak, fashion anak, Pink, Motif Bunga"
    }',
    'view_count' => 150,
    'is_featured' => true,
    'created_by' => 1,
]
```

### Product Variants Table
```php
[
    'product_id' => 44,
    'sku' => 'SKU-A1B2C3D4',
    'size' => 'M',
    'color' => 'Pink',
    'weight_gram' => 250,
    'price' => 150000,
    'compare_at_price' => 180000,
    'stock_quantity' => 50,
    'reserved_quantity' => 0,
    'barcode' => '8990123456789',
]
```

### Product Images Table
```php
[
    'product_id' => 44,
    'url' => 'https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?w=500',
    'alt_text' => 'Kemeja Anak Motif Bunga Pink - Gambar 1',
    'is_primary' => true,
    'sort_order' => 0,
]
```

### Product Categories (Pivot Table)
```php
[
    'product_id' => 44,
    'category_id' => 43,
]
```

### Product Variant Images Table
```php
[
    'variant_id' => 589,
    'url' => 'https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?w=500',
    'alt_text' => 'Kemeja Anak Motif Bunga Pink - Pink - Size M - Gambar 1',
    'is_primary' => true,
    'sort_order' => 0,
]
```

**Use Case:** Variant images digunakan untuk menampilkan foto spesifik dari kombinasi warna dan ukuran tertentu. Misalnya:
- Variant Pink Size M memiliki foto tersendiri
- Variant Biru Size L memiliki foto yang berbeda
- Memudahkan customer melihat produk sesuai pilihan mereka

## Product Types Generated

| Type | Tags | Age Range |
|------|------|-----------|
| Kemeja | kemeja, formal, casual | 3-12 tahun |
| Kaos | kaos, casual, santai | 2-10 tahun |
| Dress | dress, pesta, casual | 3-12 tahun |
| Celana Jeans | jeans, celana, casual | 4-14 tahun |
| Romper | romper, baby, cute | 0-3 tahun |
| Jumpsuit | jumpsuit, fashionable, trendy | 3-10 tahun |
| Rok | rok, skirt, cute | 3-12 tahun |
| Jaket | jaket, outerwear, hangat | 3-14 tahun |
| Sweater | sweater, hangat, nyaman | 2-12 tahun |
| Cardigan | cardigan, outer, stylish | 3-12 tahun |

## Color Options
- Merah
- Biru
- Hijau
- Kuning
- Pink
- Ungu
- Hitam
- Putih
- Abu-abu
- Coklat

## Pattern Options
- Polos
- Motif Bunga
- Motif Hewan
- Striped
- Polkadot
- Karakter Kartun

## Notes

1. **Unique Slugs**: Slug menggunakan kombinasi nama + timestamp + index untuk memastikan keunikan
2. **Random Distribution**: Data didistribusikan secara random untuk variasi yang lebih natural
3. **Realistic Prices**: Harga menggunakan kelipatan 1000 untuk realisme
4. **Stock Variation**: Stock bervariasi dari 0-100 untuk simulasi keadaan real
5. **Image Placeholders**: Gunakan Unsplash placeholders, bisa diganti dengan URL gambar sendiri
6. **Categories**: Produk akan di-attach ke kategori yang sudah ada di database

## Customization

### Mengubah Jumlah Produk
Edit line 61 di seeder:
```php
for ($i = 1; $i <= 50; $i++) {  // Ubah 50 ke jumlah yang diinginkan
```

### Mengubah Image URLs
Edit array `$imageUrls` di line 48-57 dengan URL gambar Anda sendiri.

### Mengubah Product Types
Edit array `$productTypes` di line 30-41 untuk menambah/mengubah tipe produk.

### Mengubah Price Range
Edit line 125:
```php
$basePrice = rand(50, 300) * 1000; // Ubah range sesuai kebutuhan
```

## Troubleshooting

### Error: No brands found
**Solusi**: Buat brand terlebih dahulu
```bash
php artisan tinker
>>> DB::table('brands')->insert(['name' => 'Minimoda', 'slug' => 'minimoda', 'created_at' => now(), 'updated_at' => now()]);
```

### Error: No categories found
**Solusi**: Buat kategori terlebih dahulu atau import dari existing data.

### Error: Duplicate slug
**Solusi**: Seeder sudah menggunakan timestamp + index untuk menghindari duplicate. Jika tetap error, hapus produk lama atau ubah logika slug generation.

## Testing the Results

### Check Products
```bash
php artisan tinker
>>> DB::table('products')->latest('id')->limit(5)->get(['id', 'name', 'status']);
```

### Check Variants
```bash
>>> $productId = DB::table('products')->latest('id')->first()->id;
>>> DB::table('product_variants')->where('product_id', $productId)->get();
```

### Check Product Images
```bash
>>> DB::table('product_images')->where('product_id', $productId)->get();
```

### Check Variant Images
```bash
>>> $variantId = DB::table('product_variants')->latest('id')->first()->id;
>>> DB::table('product_variant_images')->where('variant_id', $variantId)->get(['url', 'alt_text', 'is_primary', 'sort_order']);
```

### Check Complete Product with Variants and Images
```bash
php artisan tinker
>>> $product = DB::table('products')->latest('id')->first();
>>> $variants = DB::table('product_variants')->where('product_id', $product->id)->get();
>>> foreach($variants as $v) {
...     $imgCount = DB::table('product_variant_images')->where('variant_id', $v->id)->count();
...     echo "{$v->color} - Size {$v->size}: {$imgCount} images\n";
... }
```

### Check via API
```bash
curl -X GET "http://localhost:8000/api/products?per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Clean Up

Untuk menghapus semua produk dummy yang di-generate (hati-hati!):
```sql
-- Delete in order (from child to parent to respect foreign keys)
DELETE FROM product_variant_images WHERE variant_id IN (
    SELECT id FROM product_variants WHERE product_id IN (
        SELECT id FROM products WHERE slug LIKE '%-1760%-%'
    )
);
DELETE FROM product_images WHERE product_id IN (SELECT id FROM products WHERE slug LIKE '%-1760%-%');
DELETE FROM product_variants WHERE product_id IN (SELECT id FROM products WHERE slug LIKE '%-1760%-%');
DELETE FROM product_categories WHERE product_id IN (SELECT id FROM products WHERE slug LIKE '%-1760%-%');
DELETE FROM products WHERE slug LIKE '%-1760%-%';
```
*Note: Ganti `1760` dengan timestamp yang sesuai (lihat di slug produk)*

## Integration with DatabaseSeeder

Untuk menjalankan seeder ini bersama seeder lainnya, tambahkan di `DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        // ... other seeders
        ProductDummySeeder::class,
    ]);
}
```

Kemudian jalankan:
```bash
php artisan db:seed
```
