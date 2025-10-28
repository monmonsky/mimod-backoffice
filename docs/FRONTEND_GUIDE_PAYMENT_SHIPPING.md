# Frontend Guide - Payment & Shipping Methods API

Panduan lengkap penggunaan API Payment Methods dan Shipping Methods untuk frontend developer.

## Base URL
```
https://api-local.minimoda.id/api
```

## Authentication
Semua endpoint memerlukan token authentication:
```javascript
headers: {
  'Authorization': 'Bearer {token}',
  'Content-Type': 'application/json'
}
```

---

# Payment Methods API

## 1. List Page - Get All Payment Methods

**Endpoint:** `GET /api/payment-methods`

### Query Parameters
```javascript
{
  is_active: 'true' | 'false',        // Filter by status
  type: 'bank_transfer' | 'virtual_account' | 'e_wallet' | 'qris' | 'credit_card' | 'cod',
  provider: 'manual' | 'midtrans' | 'xendit',
  search: 'bca',                       // Search by name or code
  sort_by: 'sort_order' | 'name',     // Default: sort_order
  sort_order: 'asc' | 'desc',         // Default: asc
  page: 1,
  per_page: 15                         // atau 'all' untuk tanpa pagination
}
```

### Request Example
```javascript
// Axios
const response = await axios.get('/api/payment-methods', {
  params: {
    is_active: 'true',
    type: 'bank_transfer',
    search: 'bca',
    page: 1,
    per_page: 15
  }
});

// Fetch
const response = await fetch('/api/payment-methods?is_active=true&type=bank_transfer&page=1&per_page=15', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": {
    "data": [
      {
        "id": 1,
        "code": "bank_transfer_bca",
        "name": "Bank Transfer BCA",
        "type": "bank_transfer",
        "provider": "manual",
        "logo_url": null,
        "description": "Transfer manual ke rekening BCA",
        "instructions": "Transfer ke rekening...",
        "fee_percentage": 0.00,
        "fee_fixed": 0.00,
        "min_amount": null,
        "max_amount": null,
        "expired_duration": 1440,
        "is_active": true,
        "sort_order": 0,
        "created_at": "2025-10-26T10:00:00.000000Z",
        "updated_at": "2025-10-26T10:00:00.000000Z"
      }
    ],
    "pagination": {
      "total": 100,
      "per_page": 15,
      "current_page": 1,
      "last_page": 7,
      "from": 1,
      "to": 15
    }
  }
}
```

### Frontend Implementation Example
```javascript
// React/Vue/Angular
const PaymentMethodList = () => {
  const [paymentMethods, setPaymentMethods] = useState([]);
  const [filters, setFilters] = useState({
    is_active: 'true',
    type: '',
    provider: '',
    search: '',
    page: 1,
    per_page: 15
  });

  const fetchPaymentMethods = async () => {
    try {
      const response = await axios.get('/api/payment-methods', { params: filters });
      setPaymentMethods(response.data.data.data);
    } catch (error) {
      console.error('Error fetching payment methods:', error);
    }
  };

  useEffect(() => {
    fetchPaymentMethods();
  }, [filters]);

  return (
    // Table with filters, search, pagination
  );
};
```

---

## 2. Create Page - Create New Payment Method

**Endpoint:** `POST /api/payment-methods`

### Request Body
```json
{
  "code": "midtrans_dana",
  "name": "DANA via Midtrans",
  "type": "e_wallet",
  "provider": "midtrans",
  "logo_url": "https://example.com/dana-logo.png",
  "description": "E-Wallet DANA melalui Midtrans",
  "instructions": "Scan QR code dengan aplikasi DANA Anda",
  "fee_percentage": 1.50,
  "fee_fixed": 0,
  "min_amount": 10000,
  "max_amount": 10000000,
  "expired_duration": 30,
  "is_active": true,
  "sort_order": 10
}
```

### Request Example
```javascript
const createPaymentMethod = async (formData) => {
  try {
    const response = await axios.post('/api/payment-methods', formData);

    if (response.data.status) {
      // Success - redirect to list or show detail
      console.log('Created:', response.data.data);
      return response.data.data;
    }
  } catch (error) {
    if (error.response?.status === 422) {
      // Validation errors
      console.error('Validation errors:', error.response.data.errors);
    }
  }
};
```

### Response (Success)
```json
{
  "status": true,
  "statusCode": "201",
  "message": "Payment method created successfully",
  "data": {
    "id": 13,
    "code": "midtrans_dana",
    "name": "DANA via Midtrans",
    "type": "e_wallet",
    "provider": "midtrans",
    // ... rest of fields
  }
}
```

### Response (Validation Error)
```json
{
  "status": false,
  "statusCode": "422",
  "message": "Validation failed",
  "errors": {
    "code": ["The code has already been taken."],
    "name": ["The name field is required."]
  }
}
```

---

## 3. Edit Page - Get & Update Payment Method

### 3a. Load Data for Edit

**Endpoint:** `GET /api/payment-methods/{id}`

```javascript
const loadPaymentMethod = async (id) => {
  const response = await axios.get(`/api/payment-methods/${id}`);
  return response.data.data;
};

// Response includes payment_method AND configs
{
  "status": true,
  "data": {
    "payment_method": {
      "id": 4,
      "code": "midtrans_bca_va",
      "name": "BCA Virtual Account",
      // ... rest of fields
    },
    "config": {
      "provider_config": "true",
      "environment": "sandbox",
      "sandbox_server_key": "SB-Mid-server-...",
      "sandbox_client_key": "SB-Mid-client-...",
      "enable_3ds": "true",
      "account_name": "PT Minimoda Indonesia"  // inherited from provider
    }
  }
}
```

### 3b. Update Payment Method

**Endpoint:** `PUT /api/payment-methods/{id}`

```javascript
const updatePaymentMethod = async (id, formData) => {
  const response = await axios.put(`/api/payment-methods/${id}`, formData);
  return response.data;
};

// Request body (hanya field yang diubah)
{
  "name": "BCA Virtual Account (Updated)",
  "fee_percentage": 2.00,
  "is_active": false
}
```

---

## 4. Toggle Active Status

**Endpoint:** `POST /api/payment-methods/{id}/toggle-active`

### Request Example
```javascript
const toggleActive = async (id) => {
  try {
    const response = await axios.post(`/api/payment-methods/${id}/toggle-active`);

    // Response includes updated payment method with new is_active value
    console.log('New status:', response.data.data.is_active);
    return response.data.data;
  } catch (error) {
    console.error('Error toggling status:', error);
  }
};

// Usage in table action button
<button onClick={() => toggleActive(method.id)}>
  {method.is_active ? 'Deactivate' : 'Activate'}
</button>
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Payment method status updated successfully",
  "data": {
    "id": 4,
    "is_active": false,  // toggled value
    // ... rest of fields
  }
}
```

---

## 5. Config Management - Dedicated API

### 5a. Get All Configs (Merged Provider + Method-Specific)

**Endpoint:** `GET /api/payment-methods/{id}/configs`

### Request Example
```javascript
const getAllConfigs = async (id) => {
  const response = await axios.get(`/api/payment-methods/${id}/configs`);
  return response.data.data;
};
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": {
    "payment_method": {
      "id": 4,
      "code": "midtrans_bca_va",
      "name": "BCA Virtual Account",
      "provider": "midtrans"
    },
    "configs": {
      "provider_config": "true",
      "environment": "sandbox",
      "sandbox_server_key": "SB-Mid-server-...",
      "sandbox_client_key": "SB-Mid-client-...",
      "sandbox_merchant_id": "YOUR_SANDBOX_MERCHANT_ID",
      "production_server_key": "YOUR_PRODUCTION_SERVER_KEY",
      "production_client_key": "YOUR_PRODUCTION_CLIENT_KEY",
      "production_merchant_id": "YOUR_PRODUCTION_MERCHANT_ID",
      "enable_3ds": "true"
    },
    "is_provider_config_holder": true
  }
}
```

**Note:** Config yang di-return adalah **merged** antara provider-level dan method-specific configs.

---

### 5b. Get Single Config Value

**Endpoint:** `GET /api/payment-methods/{id}/configs/{key}`

### Request Example
```javascript
const getConfig = async (id, key) => {
  const response = await axios.get(`/api/payment-methods/${id}/configs/${key}`);
  return response.data.data;
};

// Example: Get environment setting
const env = await getConfig(4, 'environment');
// Returns: { key: 'environment', value: 'sandbox' }
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": {
    "key": "environment",
    "value": "sandbox"
  }
}
```

---

### 5c. Update Single Config

**Endpoint:** `PUT /api/payment-methods/{id}/configs/{key}`

### Request Body
```json
{
  "value": "production",
  "is_encrypted": false
}
```

### Request Example
```javascript
const updateSingleConfig = async (id, key, value, isEncrypted = false) => {
  try {
    const response = await axios.put(`/api/payment-methods/${id}/configs/${key}`, {
      value: value,
      is_encrypted: isEncrypted
    });

    console.log('Updated:', response.data.data);
    return response.data.data;
  } catch (error) {
    console.error('Error updating config:', error);
  }
};

// Usage: Switch to production
updateSingleConfig(4, 'environment', 'production', false);

// Usage: Update API key
updateSingleConfig(4, 'production_server_key', 'Mid-server-PROD-KEY', true);
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Config updated successfully",
  "data": {
    "key": "environment",
    "value": "production"
  }
}
```

---

### 5d. Bulk Update Configs

**Endpoint:** `POST /api/payment-methods/{id}/configs`

### Request Body
```json
{
  "configs": [
    {
      "key": "environment",
      "value": "production",
      "is_encrypted": false
    },
    {
      "key": "production_server_key",
      "value": "Mid-server-PRODUCTION-KEY-HERE",
      "is_encrypted": true
    },
    {
      "key": "production_client_key",
      "value": "Mid-client-PRODUCTION-KEY-HERE",
      "is_encrypted": false
    }
  ]
}
```

### Request Example
```javascript
const bulkUpdateConfigs = async (id, configs) => {
  try {
    const response = await axios.post(`/api/payment-methods/${id}/configs`, {
      configs: configs
    });

    // Response includes all updated merged configs
    console.log('Updated configs:', response.data.data);
    return response.data.data;
  } catch (error) {
    console.error('Error updating configs:', error);
  }
};

// Usage in config form
const handleSubmit = (formData) => {
  const configs = [
    { key: 'environment', value: formData.environment, is_encrypted: false },
    { key: 'production_server_key', value: formData.serverKey, is_encrypted: true },
    { key: 'production_client_key', value: formData.clientKey, is_encrypted: false },
  ];

  bulkUpdateConfigs(paymentMethodId, configs);
};
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Configs updated successfully",
  "data": {
    "provider_config": "true",
    "environment": "production",
    "sandbox_server_key": "SB-Mid-...",
    "production_server_key": "Mid-server-...",
    "production_client_key": "Mid-client-...",
    "enable_3ds": "true"
  }
}
```

---

### 5e. Delete Config

**Endpoint:** `DELETE /api/payment-methods/{id}/configs/{key}`

### Request Example
```javascript
const deleteConfig = async (id, key) => {
  try {
    const response = await axios.delete(`/api/payment-methods/${id}/configs/${key}`);

    if (response.data.status) {
      console.log('Config deleted successfully');
    }
  } catch (error) {
    if (error.response?.status === 404) {
      alert('Config key not found');
    }
  }
};

// Usage: Remove method-specific override
deleteConfig(5, 'enable_3ds');  // Will fallback to provider config
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Config deleted successfully",
  "data": null
}
```

---

### 5f. Get Provider-Level Configs

**Endpoint:** `GET /api/payment-methods/providers/{provider}/configs`

### Request Example
```javascript
const getProviderConfigs = async (provider) => {
  const response = await axios.get(`/api/payment-methods/providers/${provider}/configs`);
  return response.data.data;
};

// Usage: Get all Midtrans shared configs
const midtransConfigs = await getProviderConfigs('midtrans');
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": {
    "provider": "midtrans",
    "config_holder": {
      "id": 4,
      "code": "midtrans_bca_va",
      "name": "BCA Virtual Account"
    },
    "configs": {
      "provider_config": "true",
      "environment": "sandbox",
      "sandbox_server_key": "SB-Mid-server-...",
      "sandbox_client_key": "SB-Mid-client-...",
      "sandbox_merchant_id": "YOUR_SANDBOX_MERCHANT_ID",
      "production_server_key": "YOUR_PRODUCTION_SERVER_KEY",
      "production_client_key": "YOUR_PRODUCTION_CLIENT_KEY",
      "production_merchant_id": "YOUR_PRODUCTION_MERCHANT_ID",
      "enable_3ds": "true"
    }
  }
}
```

**Use Case:** Show which method holds the provider config in UI, or fetch shared configs for multiple methods.

---

### 5g. Legacy: Update Configs via Main Endpoint

**Endpoint:** `POST /api/payment-methods/{id}/config`

This endpoint still exists for backward compatibility but **recommended to use dedicated config endpoints above** for better granularity.

---

**Notes untuk Config Management:**
- ⚠️ Jangan tampilkan encrypted values di form (gunakan placeholder atau password input)
- ✅ Mark sensitive fields dengan `is_encrypted: true` (API keys, server keys, etc)
- ✅ Config yang di-update akan override provider config (jika ada) atau create new method-specific config
- ✅ Untuk Midtrans methods, cukup update config di 1 method (yang ada `provider_config: true`) untuk affect semua methods
- ✅ Use single update (`PUT /{id}/configs/{key}`) untuk update 1 field saja (lebih efisien)
- ✅ Use bulk update (`POST /{id}/configs`) untuk update multiple fields sekaligus
- ✅ Delete method-specific config akan fallback ke provider-level config

---

## 6. Delete Payment Method

**Endpoint:** `DELETE /api/payment-methods/{id}`

### Request Example
```javascript
const deletePaymentMethod = async (id) => {
  try {
    const response = await axios.delete(`/api/payment-methods/${id}`);

    if (response.data.status) {
      console.log('Deleted successfully');
      // Redirect to list or refresh table
    }
  } catch (error) {
    if (error.response?.status === 400) {
      // Cannot delete - used in orders
      alert(error.response.data.message);
      // Suggest deactivate instead
    }
  }
};

// Usage with confirmation
const handleDelete = (id, name) => {
  if (confirm(`Are you sure you want to delete ${name}?`)) {
    deletePaymentMethod(id);
  }
};
```

### Response (Success)
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Payment method deleted successfully",
  "data": null
}
```

### Response (Error - Used in Orders)
```json
{
  "status": false,
  "statusCode": "400",
  "message": "Cannot delete payment method that has been used in orders. Please deactivate it instead."
}
```

---

# Shipping Methods API

## 1. List Page - Get All Shipping Methods

**Endpoint:** `GET /api/shipping-methods`

### Query Parameters
```javascript
{
  is_active: 'true' | 'false',
  type: 'manual' | 'rajaongkir' | 'custom',
  provider: 'jne' | 'jnt' | 'sicepat' | 'pos' | 'rajaongkir',
  search: 'jne',
  sort_by: 'sort_order' | 'name',
  sort_order: 'asc' | 'desc',
  page: 1,
  per_page: 15
}
```

### Request Example
```javascript
const response = await axios.get('/api/shipping-methods', {
  params: {
    is_active: 'true',
    provider: 'jne',
    page: 1,
    per_page: 15
  }
});
```

### Response
```json
{
  "status": true,
  "data": {
    "data": [
      {
        "id": 1,
        "code": "jne_reg",
        "name": "JNE REG",
        "type": "manual",
        "provider": "jne",
        "base_cost": 10000.00,
        "cost_per_kg": 5000.00,
        "min_weight": null,
        "max_weight": 30000,
        "estimated_delivery": "2-3 hari",
        "is_active": true,
        "sort_order": 0
      }
    ],
    "pagination": { /* ... */ }
  }
}
```

---

## 2. Create Shipping Method

**Endpoint:** `POST /api/shipping-methods`

### Request Body
```json
{
  "code": "anteraja_reg",
  "name": "AnterAja Reguler",
  "type": "manual",
  "provider": "anteraja",
  "base_cost": 8000,
  "cost_per_kg": 4000,
  "min_weight": null,
  "max_weight": 50000,
  "estimated_delivery": "3-4 hari",
  "is_active": true,
  "sort_order": 20
}
```

### Request Example
```javascript
const createShippingMethod = async (formData) => {
  const response = await axios.post('/api/shipping-methods', formData);
  return response.data;
};
```

---

## 3. Edit Page - Get & Update

### Get Shipping Method
**Endpoint:** `GET /api/shipping-methods/{id}`

```javascript
const loadShippingMethod = async (id) => {
  const response = await axios.get(`/api/shipping-methods/${id}`);
  return response.data.data;
};

// Response
{
  "status": true,
  "data": {
    "shipping_method": {
      "id": 1,
      "code": "jne_reg",
      // ... rest of fields
    },
    "config": {
      "provider_config": "true",
      "tracking_url": "https://www.jne.co.id/id/tracking/trace",
      "service_code": "REG"
    }
  }
}
```

### Update Shipping Method
**Endpoint:** `PUT /api/shipping-methods/{id}`

```javascript
const updateShippingMethod = async (id, formData) => {
  const response = await axios.put(`/api/shipping-methods/${id}`, formData);
  return response.data;
};
```

---

## 4. Toggle Active Status

**Endpoint:** `POST /api/shipping-methods/{id}/toggle-active`

```javascript
const toggleActive = async (id) => {
  const response = await axios.post(`/api/shipping-methods/${id}/toggle-active`);
  return response.data.data;
};
```

---

## 5. Config Management - Dedicated API

### 5a. Get All Configs (Merged Provider + Method-Specific)

**Endpoint:** `GET /api/shipping-methods/{id}/configs`

### Request Example
```javascript
const getAllConfigs = async (id) => {
  const response = await axios.get(`/api/shipping-methods/${id}/configs`);
  return response.data.data;
};
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": {
    "shipping_method": {
      "id": 14,
      "code": "rajaongkir_jne",
      "name": "JNE (RajaOngkir)",
      "provider": "rajaongkir"
    },
    "configs": {
      "provider_config": "true",
      "api_key": "your-rajaongkir-api-key",
      "account_type": "starter",
      "origin_city_id": "501",
      "origin_province_id": "6",
      "courier_code": "jne"
    },
    "is_provider_config_holder": true
  }
}
```

---

### 5b. Get Single Config Value

**Endpoint:** `GET /api/shipping-methods/{id}/configs/{key}`

### Request Example
```javascript
const getConfig = async (id, key) => {
  const response = await axios.get(`/api/shipping-methods/${id}/configs/${key}`);
  return response.data.data;
};

// Example: Get RajaOngkir API key
const apiKey = await getConfig(14, 'api_key');
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": {
    "key": "api_key",
    "value": "your-rajaongkir-api-key"
  }
}
```

---

### 5c. Update Single Config

**Endpoint:** `PUT /api/shipping-methods/{id}/configs/{key}`

### Request Body
```json
{
  "value": "new-rajaongkir-api-key",
  "is_encrypted": true
}
```

### Request Example
```javascript
const updateSingleConfig = async (id, key, value, isEncrypted = false) => {
  try {
    const response = await axios.put(`/api/shipping-methods/${id}/configs/${key}`, {
      value: value,
      is_encrypted: isEncrypted
    });

    return response.data.data;
  } catch (error) {
    console.error('Error updating config:', error);
  }
};

// Usage: Update RajaOngkir API key
updateSingleConfig(14, 'api_key', 'new-rajaongkir-api-key', true);

// Usage: Change origin city
updateSingleConfig(14, 'origin_city_id', '152', false);
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Config updated successfully",
  "data": {
    "key": "api_key",
    "value": "new-rajaongkir-api-key"
  }
}
```

---

### 5d. Bulk Update Configs

**Endpoint:** `POST /api/shipping-methods/{id}/configs`

### Request Body
```json
{
  "configs": [
    {
      "key": "api_key",
      "value": "your-rajaongkir-api-key",
      "is_encrypted": true
    },
    {
      "key": "origin_city_id",
      "value": "501",
      "is_encrypted": false
    },
    {
      "key": "origin_province_id",
      "value": "6",
      "is_encrypted": false
    }
  ]
}
```

### Request Example
```javascript
const bulkUpdateConfigs = async (id, configs) => {
  try {
    const response = await axios.post(`/api/shipping-methods/${id}/configs`, {
      configs: configs
    });

    // Response includes all updated merged configs
    return response.data.data;
  } catch (error) {
    console.error('Error updating configs:', error);
  }
};

// Usage in config form
const handleSubmit = (formData) => {
  const configs = [
    { key: 'api_key', value: formData.apiKey, is_encrypted: true },
    { key: 'origin_city_id', value: formData.cityId, is_encrypted: false },
    { key: 'origin_province_id', value: formData.provinceId, is_encrypted: false },
  ];

  bulkUpdateConfigs(shippingMethodId, configs);
};
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Configs updated successfully",
  "data": {
    "provider_config": "true",
    "api_key": "your-rajaongkir-api-key",
    "account_type": "starter",
    "origin_city_id": "501",
    "origin_province_id": "6"
  }
}
```

---

### 5e. Delete Config

**Endpoint:** `DELETE /api/shipping-methods/{id}/configs/{key}`

### Request Example
```javascript
const deleteConfig = async (id, key) => {
  try {
    const response = await axios.delete(`/api/shipping-methods/${id}/configs/${key}`);

    if (response.data.status) {
      console.log('Config deleted successfully');
    }
  } catch (error) {
    if (error.response?.status === 404) {
      alert('Config key not found');
    }
  }
};

// Usage: Remove method-specific courier code
deleteConfig(15, 'courier_code');  // Will fallback to provider config
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Config deleted successfully",
  "data": null
}
```

---

### 5f. Get Provider-Level Configs

**Endpoint:** `GET /api/shipping-methods/providers/{provider}/configs`

### Request Example
```javascript
const getProviderConfigs = async (provider) => {
  const response = await axios.get(`/api/shipping-methods/providers/${provider}/configs`);
  return response.data.data;
};

// Usage: Get all RajaOngkir shared configs
const rajaOngkirConfigs = await getProviderConfigs('rajaongkir');

// Usage: Get JNE tracking URL
const jneConfigs = await getProviderConfigs('jne');
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": {
    "provider": "rajaongkir",
    "config_holder": {
      "id": 14,
      "code": "rajaongkir_jne",
      "name": "JNE (RajaOngkir)"
    },
    "configs": {
      "provider_config": "true",
      "api_key": "your-rajaongkir-api-key",
      "account_type": "starter",
      "origin_city_id": "501",
      "origin_province_id": "6"
    }
  }
}
```

---

### 5g. Legacy: Update Configs via Main Endpoint

**Endpoint:** `POST /api/shipping-methods/{id}/config`

This endpoint still exists for backward compatibility but **recommended to use dedicated config endpoints above** for better granularity

---

## 6. Calculate Shipping Cost

**Endpoint:** `POST /api/shipping-methods/{id}/calculate-cost`

### Request Body
```json
{
  "weight": 1500,                    // in grams (required)
  "destination_city_id": 455,        // for RajaOngkir (optional)
  "destination_subdistrict_id": null // for RajaOngkir (optional)
}
```

### Request Example
```javascript
const calculateCost = async (methodId, weight) => {
  try {
    const response = await axios.post(`/api/shipping-methods/${methodId}/calculate-cost`, {
      weight: weight  // in grams
    });

    return response.data.data;
  } catch (error) {
    console.error('Error calculating cost:', error);
  }
};

// Usage in form or preview
const handleCalculate = () => {
  const weightInGrams = parseFloat(weight) * 1000; // convert kg to grams
  calculateCost(selectedMethodId, weightInGrams)
    .then(result => {
      console.log('Cost:', result.cost);
      console.log('Delivery:', result.estimated_delivery);
    });
};
```

### Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": {
    "cost": 17500,
    "estimated_delivery": "2-3 hari",
    "type": "manual"
  }
}
```

**Calculation Logic:**
- **Manual/Custom Methods**: `cost = base_cost + (cost_per_kg × weight_in_kg)`
- **RajaOngkir Methods**: Will integrate with RajaOngkir API (currently not implemented)

**Usage Example:**
```javascript
// For manual method (JNE REG)
// base_cost = 10000
// cost_per_kg = 5000
// weight = 1500 grams = 1.5 kg
// cost = 10000 + (5000 × 1.5) = 17500
```

---

## 7. Delete Shipping Method

**Endpoint:** `DELETE /api/shipping-methods/{id}`

```javascript
const deleteShippingMethod = async (id) => {
  try {
    const response = await axios.delete(`/api/shipping-methods/${id}`);
    return response.data;
  } catch (error) {
    if (error.response?.status === 400) {
      alert('Cannot delete - used in orders. Please deactivate instead.');
    }
  }
};
```

---

# Important Notes untuk Frontend

## 1. Provider-Level Config Architecture

**Payment Methods:**
- Semua Midtrans methods (BCA VA, BNI VA, GoPay, QRIS, etc.) **share credentials**
- Update config di method yang ada `provider_config: true` → affects all Midtrans methods
- Method-specific config (e.g., QRIS `acquirer`) akan override/extend provider config

**Shipping Methods:**
- Semua JNE methods (REG, YES) share `tracking_url`
- Semua J&T methods (REG, Express) share `tracking_url`
- RajaOngkir methods share `api_key`, `origin_city_id`, etc.

**UI Recommendation:**
```javascript
// Show indicator di config page
{config.provider_config === 'true' && (
  <Alert type="info">
    This is the provider config holder. Changes here will affect all {provider} methods.
  </Alert>
)}

// Show inherited configs differently
{config.map(item => (
  <ConfigField
    key={item.key}
    label={item.key}
    value={item.value}
    isInherited={!isMethodSpecific(item.key)}  // show with different color/icon
    isEncrypted={item.is_encrypted}
  />
))}
```

## 2. Form Validation

```javascript
// Payment Method Form
const validationRules = {
  code: 'required|unique|max:100',
  name: 'required|max:200',
  type: 'required|in:bank_transfer,virtual_account,e_wallet,qris,credit_card,cod',
  provider: 'nullable|max:50',
  fee_percentage: 'numeric|min:0|max:100',
  fee_fixed: 'numeric|min:0',
  expired_duration: 'integer|min:1'
};

// Shipping Method Form
const validationRules = {
  code: 'required|unique|max:100',
  name: 'required|max:200',
  type: 'required|in:manual,rajaongkir,custom',
  provider: 'nullable|max:50',
  base_cost: 'numeric|min:0',
  cost_per_kg: 'numeric|min:0',
  min_weight: 'integer|nullable',
  max_weight: 'integer|nullable'
};
```

## 3. Error Handling

```javascript
const handleApiError = (error) => {
  if (error.response) {
    switch (error.response.status) {
      case 400:
        // Bad request (e.g., cannot delete)
        showError(error.response.data.message);
        break;
      case 404:
        // Not found
        showError('Payment/Shipping method not found');
        break;
      case 422:
        // Validation errors
        showValidationErrors(error.response.data.errors);
        break;
      case 500:
        // Server error
        showError('Server error. Please try again later.');
        break;
    }
  }
};
```

## 4. State Management Example

```javascript
// React Context or Redux
const PaymentMethodsContext = {
  paymentMethods: [],
  loading: false,
  filters: {
    is_active: 'true',
    type: '',
    provider: '',
    search: '',
    page: 1,
    per_page: 15
  },
  pagination: null,

  // Actions
  fetchPaymentMethods: async (filters) => { /* ... */ },
  createPaymentMethod: async (data) => { /* ... */ },
  updatePaymentMethod: async (id, data) => { /* ... */ },
  deletePaymentMethod: async (id) => { /* ... */ },
  toggleActive: async (id) => { /* ... */ },
  updateConfig: async (id, configs) => { /* ... */ }
};
```

## 5. UI Components Suggestion

**List Page:**
- DataTable dengan filters (status, type, provider)
- Search box
- Pagination controls
- Action buttons (Edit, Toggle, Delete)
- Create button

**Create/Edit Page:**
- Form dengan validation
- Tabs untuk Basic Info dan Config (pada edit page)
- Save & Cancel buttons
- Loading state

**Config Page:**
- Form untuk edit configs
- Visual indicator untuk inherited configs
- Password/masked input untuk encrypted fields
- Environment switcher (sandbox/production)

---

Semua endpoint sudah siap digunakan! Refer ke [BACKOFFICE_API.md](./BACKOFFICE_API.md) untuk detail response format dan [CONFIG_ARCHITECTURE.md](./CONFIG_ARCHITECTURE.md) untuk memahami provider-level config system.
