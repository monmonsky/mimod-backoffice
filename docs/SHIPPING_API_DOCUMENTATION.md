# Shipping API Documentation (RajaOngkir Integration)

## Overview
API untuk cek ongkir (shipping cost) menggunakan RajaOngkir API. Support untuk JNE, POS Indonesia, dan TIKI.

**Base URL:** `http://api-local.minimoda.id/api/shipping`

**Authentication:** Required (Bearer Token)

---

## Setup

### 1. Get RajaOngkir API Key
1. Daftar di https://rajaongkir.com
2. Pilih plan (Starter sudah cukup untuk development)
3. Copy API Key dari dashboard

### 2. Add to `.env`
```env
RAJAONGKIR_API_KEY=your_rajaongkir_api_key_here
```

---

## Endpoints

### 1. Get Provinces

**Endpoint:** `GET /api/shipping/provinces`

Mendapatkan daftar semua provinsi di Indonesia.

#### Request Example:
```bash
curl -X GET "http://api-local.minimoda.id/api/shipping/provinces" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Response (Success - 200):
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Provinces retrieved successfully",
  "data": [
    {
      "province_id": "1",
      "province": "Bali"
    },
    {
      "province_id": "2",
      "province": "Bangka Belitung"
    },
    {
      "province_id": "6",
      "province": "DKI Jakarta"
    }
  ]
}
```

**Note:** Data di-cache selama 24 jam untuk performa lebih baik.

---

### 2. Get Cities by Province

**Endpoint:** `GET /api/shipping/cities`

Mendapatkan daftar kota/kabupaten berdasarkan provinsi.

#### Query Parameters:
```typescript
{
  province_id: integer  // Required - Province ID
}
```

#### Request Example:
```bash
curl -X GET "http://api-local.minimoda.id/api/shipping/cities?province_id=6" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Response (Success - 200):
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Cities retrieved successfully",
  "data": [
    {
      "city_id": "151",
      "province_id": "6",
      "province": "DKI Jakarta",
      "type": "Kota",
      "city_name": "Jakarta Barat",
      "postal_code": "11220"
    },
    {
      "city_id": "152",
      "province_id": "6",
      "province": "DKI Jakarta",
      "type": "Kota",
      "city_name": "Jakarta Pusat",
      "postal_code": "10540"
    },
    {
      "city_id": "153",
      "province_id": "6",
      "province": "DKI Jakarta",
      "type": "Kota",
      "city_name": "Jakarta Selatan",
      "postal_code": "12230"
    }
  ]
}
```

**Note:** Data di-cache selama 24 jam per province_id.

---

### 3. Calculate Shipping Cost (CEK ONGKIR)

**Endpoint:** `POST /api/shipping/calculate`

Menghitung biaya pengiriman berdasarkan asal, tujuan, berat, dan kurir.

#### Request Body:
```typescript
{
  origin: integer;       // Required - City ID asal (origin)
  destination: integer;  // Required - City ID tujuan
  weight: integer;       // Required - Berat dalam gram (min: 1)
  courier: string;       // Required - Kode kurir: jne | pos | tiki
}
```

#### Request Example:
```bash
curl -X POST "http://api-local.minimoda.id/api/shipping/calculate" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "origin": 501,
    "destination": 153,
    "weight": 1700,
    "courier": "jne"
  }'
```

#### Response (Success - 200):
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Shipping cost calculated successfully",
  "data": {
    "origin": {
      "city_id": "501",
      "province_id": "5",
      "province": "DI Yogyakarta",
      "type": "Kota",
      "city_name": "Yogyakarta",
      "postal_code": "55111"
    },
    "destination": {
      "city_id": "153",
      "province_id": "6",
      "province": "DKI Jakarta",
      "type": "Kota",
      "city_name": "Jakarta Selatan",
      "postal_code": "12230"
    },
    "weight": 1700,
    "shipping_options": [
      {
        "courier_code": "jne",
        "courier_name": "Jalur Nugraha Ekakurir (JNE)",
        "service": "OKE",
        "description": "Ongkos Kirim Ekonomis",
        "cost": 26000,
        "etd": "4-5",
        "note": ""
      },
      {
        "courier_code": "jne",
        "courier_name": "Jalur Nugraha Ekakurir (JNE)",
        "service": "REG",
        "description": "Layanan Reguler",
        "cost": 29000,
        "etd": "2-3",
        "note": ""
      },
      {
        "courier_code": "jne",
        "courier_name": "Jalur Nugraha Ekakurir (JNE)",
        "service": "YES",
        "description": "Yakin Esok Sampai",
        "cost": 38000,
        "etd": "1-1",
        "note": ""
      }
    ]
  }
}
```

---

### 4. Track Shipment

**Endpoint:** `POST /api/shipping/track`

Melacak status pengiriman berdasarkan nomor resi.

#### Request Body:
```typescript
{
  waybill: string;  // Required - Nomor resi / tracking number
  courier: string;  // Required - Kode kurir: jne | pos | tiki
}
```

#### Request Example:
```bash
curl -X POST "http://api-local.minimoda.id/api/shipping/track" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "waybill": "8839283892380001",
    "courier": "jne"
  }'
```

#### Response (Success - 200):
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Tracking information retrieved successfully",
  "data": {
    "delivered": true,
    "summary": {
      "courier_code": "jne",
      "courier_name": "JNE",
      "waybill_number": "8839283892380001",
      "service_code": "REG",
      "waybill_date": "2025-10-10",
      "shipper_name": "Toko Minimoda",
      "receiver_name": "John Doe",
      "origin": "YOGYAKARTA",
      "destination": "JAKARTA SELATAN",
      "status": "DELIVERED"
    },
    "details": {
      "waybill_number": "8839283892380001",
      "waybill_date": "2025-10-10",
      "weight": "2 kg",
      "origin": "YOGYAKARTA",
      "destination": "JAKARTA SELATAN",
      "shipper_name": "Toko Minimoda",
      "shipper_address": "Jl. Malioboro No. 1",
      "receiver_name": "John Doe",
      "receiver_address": "Jl. Sudirman No. 123"
    },
    "delivery_status": {
      "status": "DELIVERED",
      "pod_receiver": "John Doe",
      "pod_date": "2025-10-13",
      "pod_time": "14:25"
    },
    "manifest": [
      {
        "manifest_code": "1",
        "manifest_description": "Manifested",
        "manifest_date": "2025-10-10",
        "manifest_time": "10:12:00",
        "city_name": "YOGYAKARTA"
      },
      {
        "manifest_code": "2",
        "manifest_description": "On Process at Sorting Center",
        "manifest_date": "2025-10-10",
        "manifest_time": "18:43:00",
        "city_name": "YOGYAKARTA"
      },
      {
        "manifest_code": "3",
        "manifest_description": "Received On Destination Gateway",
        "manifest_date": "2025-10-12",
        "manifest_time": "03:21:00",
        "city_name": "JAKARTA"
      },
      {
        "manifest_code": "4",
        "manifest_description": "With Delivery Courier",
        "manifest_date": "2025-10-13",
        "manifest_time": "08:15:00",
        "city_name": "JAKARTA SELATAN"
      },
      {
        "manifest_code": "5",
        "manifest_description": "Delivered",
        "manifest_date": "2025-10-13",
        "manifest_time": "14:25:00",
        "city_name": "JAKARTA SELATAN"
      }
    ]
  }
}
```

---

## Courier Codes

| Code | Name | Services |
|------|------|----------|
| `jne` | JNE | OKE, REG, YES, CTCYES, JTR |
| `pos` | POS Indonesia | Pos Kilat Khusus, Express Next Day |
| `tiki` | TIKI | REG, ECO, ONS, SDS |

---

## Integration dengan Create Order

### Flow Checkout:

```typescript
// 1. User pilih alamat/provinsi/kota
const selectedCity = 153 // Jakarta Selatan

// 2. Hitung total berat dari cart items
const totalWeight = cartItems.reduce((sum, item) => {
  return sum + (item.weight * item.quantity)
}, 0)

// 3. Cek ongkir ke semua kurir
const couriers = ['jne', 'pos', 'tiki']
const shippingOptions = []

for (const courier of couriers) {
  const result = await $fetch('/api/shipping/calculate', {
    method: 'POST',
    body: {
      origin: 501,              // Yogyakarta (store location)
      destination: selectedCity, // User's city
      weight: totalWeight,
      courier: courier
    }
  })

  shippingOptions.push(...result.data.shipping_options)
}

// 4. User pilih service shipping
const selectedShipping = shippingOptions[0] // User choice

// 5. Create order dengan shipping cost
const order = await createOrder({
  customer_id: customer.id,
  items: cartItems,
  shipping_cost: selectedShipping.cost,
  courier: selectedShipping.courier_code,
  shipping_service: selectedShipping.service,
  // ... other fields
})
```

---

## Example: Complete Checkout Flow

```typescript
// composables/useShipping.ts
export const useShipping = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const getProvinces = async () => {
    return await $fetch('/api/shipping/provinces', {
      baseURL: config.public.apiBase,
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
  }

  const getCities = async (provinceId: number) => {
    return await $fetch('/api/shipping/cities', {
      baseURL: config.public.apiBase,
      headers: { Authorization: `Bearer ${authStore.token}` },
      params: { province_id: provinceId }
    })
  }

  const calculateShipping = async (data: {
    origin: number
    destination: number
    weight: number
    courier: string
  }) => {
    return await $fetch('/api/shipping/calculate', {
      method: 'POST',
      baseURL: config.public.apiBase,
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: data
    })
  }

  return { getProvinces, getCities, calculateShipping }
}

// Usage in Checkout Page
const { calculateShipping } = useShipping()
const shippingCost = ref(0)
const selectedCourier = ref('')

const handleCalculateShipping = async () => {
  const totalWeight = cartItems.value.reduce((sum, item) => {
    return sum + (item.weight * item.quantity)
  }, 0)

  const result = await calculateShipping({
    origin: 501, // Store city ID
    destination: shippingAddress.value.city_id,
    weight: totalWeight,
    courier: 'jne'
  })

  // Show shipping options to user
  shippingOptions.value = result.data.shipping_options

  // User selects one option
  const selected = shippingOptions.value[0]
  shippingCost.value = selected.cost
  selectedCourier.value = `${selected.courier_name} - ${selected.service}`
}
```

---

## Error Responses

### 422 - Validation Error
```json
{
  "status": false,
  "statusCode": "422",
  "message": "The origin field is required.",
  "data": {
    "errors": {
      "origin": ["The origin field is required."]
    }
  }
}
```

### 500 - API Key Not Configured
```json
{
  "status": false,
  "statusCode": "500",
  "message": "RajaOngkir API key not configured",
  "data": {}
}
```

### 500 - RajaOngkir Error
```json
{
  "status": false,
  "statusCode": "500",
  "message": "Failed to calculate shipping cost: ...",
  "data": {}
}
```

---

## Notes

1. **Cache:** Province dan city data di-cache 24 jam untuk performa
2. **Weight:** Berat dalam satuan gram (1 kg = 1000 gram)
3. **Origin:** Set origin city ID sesuai lokasi toko/warehouse Anda
4. **RajaOngkir Starter:** Support 3 kurir (JNE, POS, TIKI)
5. **Rate Limit:** Check limit di akun RajaOngkir Anda

---

## RajaOngkir Plans

| Plan | Price | Couriers | Requests/Day |
|------|-------|----------|--------------|
| Starter | Rp 25.000/bulan | 3 (JNE, POS, TIKI) | 1.000 |
| Basic | Rp 75.000/bulan | 8 couriers | 5.000 |
| Pro | Rp 300.000/bulan | All couriers | 30.000 |

Link: https://rajaongkir.com/pricing

---

**Last Updated:** 2025-10-14
