# Settings API Documentation

Base URL: `/api/settings`

All endpoints require authentication using Bearer token in the `Authorization` header.

## Response Format

All API responses follow this standard format:

```json
{
  "status": true,
  "statusCode": "200",
  "message": "Success message",
  "data": {}
}
```

### Status Codes

- `200` - Success
- `204` - No Content / Not Found
- `422` - Validation Error
- `500` - Internal Server Error

---

## General Settings API

Base path: `/api/settings/general`

### Get All General Settings

```http
GET /api/settings/general
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "General settings retrieved successfully",
  "data": {
    "store.info": { ... },
    "store.contact": { ... },
    "store.address": { ... },
    "store.social": { ... }
  }
}
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/settings/general" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

### Get Specific Setting

```http
GET /api/settings/general/{key}
```

**Parameters:**
- `key` (string, required) - Setting key (e.g., `store.info`)

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Setting retrieved successfully",
  "data": {
    "key": "store.info",
    "value": {
      "name": "Store Name",
      "tagline": "Store Tagline",
      "description": "Store Description"
    }
  }
}
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/settings/general/store.info" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

### Update Setting

```http
PUT /api/settings/general/{key}
```

**Parameters:**
- `key` (string, required) - Setting key to update

**Request Body:**
```json
{
  "value": {
    "name": "New Store Name",
    "tagline": "New Tagline"
  }
}
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Setting updated successfully",
  "data": {
    "key": "store.info",
    "value": {
      "name": "New Store Name",
      "tagline": "New Tagline"
    }
  }
}
```

**Example:**
```bash
curl -X PUT "http://localhost:8000/api/settings/general/store.info" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"value":{"name":"My Store","tagline":"Best Store Ever"}}'
```

---

### Get Store Information

```http
GET /api/settings/general/store/info
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Store information retrieved successfully",
  "data": {
    "info": {
      "name": "Store Name",
      "tagline": "Store Tagline",
      "description": "Description",
      "logo": "logo.png"
    },
    "contact": {
      "phone": "+62123456789",
      "email": "store@example.com",
      "whatsapp": "+62123456789"
    },
    "address": {
      "street": "Street Address",
      "province_code": "31",
      "regency_code": "3171",
      "district_code": "317101",
      "village_code": "3171011001",
      "postal_code": "12345"
    },
    "social": {
      "facebook": "https://facebook.com/store",
      "instagram": "https://instagram.com/store",
      "twitter": "https://twitter.com/store"
    }
  }
}
```

---

### Get Email Settings

```http
GET /api/settings/general/email/settings
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Email settings retrieved successfully",
  "data": {
    "smtp": {
      "host": "smtp.gmail.com",
      "port": 587,
      "username": "user@example.com",
      "password": "encrypted_password",
      "encryption": "tls",
      "from_address": "noreply@example.com",
      "from_name": "Store Name"
    },
    "notifications": {
      "new_order": true,
      "order_status": true,
      "low_stock": true,
      "new_customer": false
    }
  }
}
```

---

### Get SEO Settings

```http
GET /api/settings/general/seo/settings
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "SEO settings retrieved successfully",
  "data": {
    "basic": {
      "site_title": "Store Title",
      "meta_description": "Store Description",
      "meta_keywords": "keyword1, keyword2",
      "robots": "index, follow"
    },
    "opengraph": {
      "og_title": "Store OG Title",
      "og_description": "Store OG Description",
      "og_image": "og-image.jpg"
    },
    "twitter": {
      "twitter_card": "summary_large_image",
      "twitter_site": "@storehandle",
      "twitter_creator": "@creatorhandle"
    }
  }
}
```

---

### Get System Configuration

```http
GET /api/settings/general/system/config
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "System configuration retrieved successfully",
  "data": {
    "general": {
      "timezone": "Asia/Jakarta",
      "date_format": "Y-m-d",
      "time_format": "H:i:s",
      "currency": "IDR",
      "currency_symbol": "Rp"
    },
    "security": {
      "two_factor_auth": false,
      "session_timeout": 120,
      "password_expires": 90,
      "max_login_attempts": 5
    },
    "maintenance": {
      "maintenance_mode": false,
      "maintenance_message": "Site under maintenance",
      "allowed_ips": []
    }
  }
}
```

---

## Payment Settings API

Base path: `/api/settings/payment`

### Get All Payment Settings

```http
GET /api/settings/payment
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Payment settings retrieved successfully",
  "data": {
    "payment.tax": { ... },
    "payment.midtrans.api": { ... },
    "payment.midtrans.methods": { ... },
    "payment.midtrans.transaction": { ... },
    "payment.methods": { ... }
  }
}
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/settings/payment" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

### Get Specific Payment Setting

```http
GET /api/settings/payment/{key}
```

**Parameters:**
- `key` (string, required) - Setting key (e.g., `payment.tax`)

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Setting retrieved successfully",
  "data": {
    "key": "payment.tax",
    "value": {
      "enabled": true,
      "tax_name": "VAT",
      "tax_rate": 10,
      "included_in_price": false
    }
  }
}
```

---

### Update Payment Setting

```http
PUT /api/settings/payment/{key}
```

**Parameters:**
- `key` (string, required) - Setting key to update

**Request Body:**
```json
{
  "value": {
    "enabled": true,
    "tax_rate": 11
  }
}
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Setting updated successfully",
  "data": {
    "key": "payment.tax",
    "value": {
      "enabled": true,
      "tax_rate": 11
    }
  }
}
```

---

### Get Tax Settings

```http
GET /api/settings/payment/tax/settings
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Tax settings retrieved successfully",
  "data": {
    "tax": {
      "enabled": true,
      "tax_name": "VAT",
      "tax_rate": 10,
      "tax_number": "123456789",
      "included_in_price": false,
      "display_on_product": true
    }
  }
}
```

---

### Get Midtrans Configuration

```http
GET /api/settings/payment/midtrans/config
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Midtrans configuration retrieved successfully",
  "data": {
    "api": {
      "environment": "sandbox",
      "merchant_id": "G123456789",
      "client_key": "SB-Mid-client-xxxxx",
      "server_key": "SB-Mid-server-xxxxx",
      "is_sanitized": true,
      "is_3ds": true
    },
    "methods": {
      "credit_card": true,
      "gopay": true,
      "shopeepay": false,
      "bank_transfer": true,
      "qris": true
    },
    "transaction": {
      "expiry_duration": 24,
      "expiry_unit": "hours",
      "enable_callback": true,
      "callback_url": "https://example.com/callback"
    }
  }
}
```

---

### Get Payment Methods

```http
GET /api/settings/payment/methods/list
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Payment methods retrieved successfully",
  "data": {
    "methods": {
      "midtrans": {
        "enabled": true,
        "name": "Midtrans",
        "description": "Payment via Midtrans"
      },
      "bank_transfer": {
        "enabled": true,
        "name": "Bank Transfer",
        "description": "Manual bank transfer"
      },
      "cod": {
        "enabled": false,
        "name": "Cash on Delivery",
        "description": "Pay when order arrives"
      }
    }
  }
}
```

---

## Shipping Settings API

Base path: `/api/settings/shipping`

### Get All Shipping Settings

```http
GET /api/settings/shipping
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Shipping settings retrieved successfully",
  "data": {
    "shipping.origin_address": { ... },
    "shipping.rajaongkir.api": { ... },
    "shipping.rajaongkir.couriers": { ... },
    "shipping.methods": { ... }
  }
}
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/settings/shipping" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

### Get Specific Shipping Setting

```http
GET /api/settings/shipping/{key}
```

**Parameters:**
- `key` (string, required) - Setting key (e.g., `shipping.origin_address`)

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Setting retrieved successfully",
  "data": {
    "key": "shipping.origin_address",
    "value": {
      "street": "Main Street 123",
      "province_code": "31",
      "regency_code": "3171",
      "district_code": "317101",
      "village_code": "3171011001",
      "postal_code": "12345"
    }
  }
}
```

---

### Update Shipping Setting

```http
PUT /api/settings/shipping/{key}
```

**Parameters:**
- `key` (string, required) - Setting key to update

**Request Body:**
```json
{
  "value": {
    "street": "New Street 456",
    "postal_code": "54321"
  }
}
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Setting updated successfully",
  "data": {
    "key": "shipping.origin_address",
    "value": {
      "street": "New Street 456",
      "postal_code": "54321"
    }
  }
}
```

---

### Get Origin Address

```http
GET /api/settings/shipping/origin/address
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Origin address retrieved successfully",
  "data": {
    "origin_address": {
      "contact_name": "John Doe",
      "contact_phone": "+62123456789",
      "street": "Main Street 123",
      "province_code": "31",
      "province_name": "DKI Jakarta",
      "regency_code": "3171",
      "regency_name": "Jakarta Selatan",
      "district_code": "317101",
      "district_name": "Kebayoran Baru",
      "village_code": "3171011001",
      "village_name": "Gunung",
      "postal_code": "12120"
    }
  }
}
```

---

### Get RajaOngkir Configuration

```http
GET /api/settings/shipping/rajaongkir/config
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "RajaOngkir configuration retrieved successfully",
  "data": {
    "api": {
      "api_key": "xxxxxxxxxxxxx",
      "account_type": "starter",
      "base_url": "https://api.rajaongkir.com/starter"
    },
    "couriers": {
      "jne": {
        "enabled": true,
        "name": "JNE",
        "services": ["REG", "OKE", "YES"]
      },
      "tiki": {
        "enabled": true,
        "name": "TIKI",
        "services": ["REG", "ECO", "ONS"]
      },
      "pos": {
        "enabled": false,
        "name": "POS Indonesia",
        "services": ["Paket Kilat Khusus"]
      }
    }
  }
}
```

---

### Get Shipping Methods

```http
GET /api/settings/shipping/methods/list
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Shipping methods retrieved successfully",
  "data": {
    "methods": {
      "rajaongkir": {
        "enabled": true,
        "name": "RajaOngkir",
        "description": "Automatic shipping cost calculation"
      },
      "flat_rate": {
        "enabled": true,
        "name": "Flat Rate",
        "description": "Fixed shipping cost",
        "cost": 10000
      },
      "free_shipping": {
        "enabled": false,
        "name": "Free Shipping",
        "description": "No shipping cost",
        "minimum_order": 100000
      }
    }
  }
}
```

---

## Error Responses

### Unauthorized - Token Missing (401)

**When:** No token provided in Authorization header

```json
{
  "status": false,
  "statusCode": "401",
  "message": "Unauthorized. API token not provided.",
  "data": [],
  "error": "TOKEN_MISSING"
}
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/settings/general"
# No Authorization header
```

---

### Unauthorized - Invalid Token (401)

**When:** Token is invalid, expired, or revoked

```json
{
  "status": false,
  "statusCode": "401",
  "message": "Unauthorized. Invalid or expired API token.",
  "data": [],
  "error": "TOKEN_INVALID"
}
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/settings/general" \
  -H "Authorization: Bearer invalid_token_12345"
```

---

### Forbidden - Account Inactive (403)

**When:** User account is not active (suspended, banned, etc.)

```json
{
  "status": false,
  "statusCode": "403",
  "message": "Forbidden. Your account has been suspended.",
  "data": [],
  "error": "ACCOUNT_SUSPENDED"
}
```

---

### Validation Error (422)

**When:** Request data fails validation

```json
{
  "status": false,
  "statusCode": "422",
  "message": "Validation failed",
  "data": {
    "errors": {
      "value": ["The value field is required."]
    }
  }
}
```

**Example:**
```bash
curl -X PUT "http://localhost:8000/api/settings/general/store.info" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"invalid": "data"}'
# Missing required 'value' field
```

---

### Not Found (404)

**When:** Setting key does not exist

```json
{
  "status": false,
  "statusCode": "204",
  "message": "Setting not found",
  "data": []
}
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/settings/general/nonexistent.key" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### Internal Server Error (500)

**When:** Server encounters an unexpected error

```json
{
  "status": false,
  "statusCode": "500",
  "message": "Failed to retrieve settings: Database connection error",
  "data": []
}
```

---

## Authentication

All endpoints require authentication using Bearer token.

**Header:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/settings/general" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..." \
  -H "Accept: application/json"
```

To obtain an access token, use the login endpoint:

```bash
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

---

## Notes

1. All `PUT` requests require the `value` field to be an array/object
2. Setting keys use dot notation (e.g., `store.info`, `payment.tax`)
3. Dates are in ISO 8601 format
4. All responses include the standard response wrapper
5. Authentication token must be included in all requests
6. Request body must be valid JSON for PUT requests
7. Use `Content-Type: application/json` for PUT requests
8. Use `Accept: application/json` for all requests

---

## Postman Collection

Import this base configuration to Postman:

**Base URL:** `http://localhost:8000/api`

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
Accept: application/json
```

**Environment Variables:**
- `token` - Your access token
- `base_url` - API base URL

---

## Testing Examples

### JavaScript (Fetch API)

```javascript
const token = 'YOUR_ACCESS_TOKEN';

// Get store info
fetch('http://localhost:8000/api/settings/general/store/info', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));

// Update setting
fetch('http://localhost:8000/api/settings/general/store.info', {
  method: 'PUT',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    value: {
      name: 'New Store Name',
      tagline: 'New Tagline'
    }
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

### PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8000/api/']);
$token = 'YOUR_ACCESS_TOKEN';

// Get store info
$response = $client->get('settings/general/store/info', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json'
    ]
]);

$data = json_decode($response->getBody(), true);
print_r($data);

// Update setting
$response = $client->put('settings/general/store.info', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ],
    'json' => [
        'value' => [
            'name' => 'New Store Name',
            'tagline' => 'New Tagline'
        ]
    ]
]);

$data = json_decode($response->getBody(), true);
print_r($data);
```
