# Config API Endpoints - Quick Reference

Dedicated API endpoints for managing payment and shipping method configurations.

## Overview

These endpoints provide granular control over config management, separate from the main payment/shipping method endpoints. All configs support provider-level inheritance and method-specific overrides.

---

## Payment Method Config API

Base Path: `/api/payment-methods`

### 1. Get All Configs (Merged)
```
GET /api/payment-methods/{id}/configs
```
Returns all configs merged from provider-level and method-specific.

**Response includes:**
- `payment_method` - Basic method info
- `configs` - Merged key-value pairs
- `is_provider_config_holder` - Boolean flag

---

### 2. Get Single Config
```
GET /api/payment-methods/{id}/configs/{key}
```
Get specific config value by key.

**Example:**
```javascript
GET /api/payment-methods/4/configs/environment
// Returns: { key: "environment", value: "sandbox" }
```

---

### 3. Update Single Config
```
PUT /api/payment-methods/{id}/configs/{key}
```

**Body:**
```json
{
  "value": "production",
  "is_encrypted": false
}
```

**Use Case:** Quick update of single config (e.g., switch environment, update API key)

---

### 4. Bulk Update Configs
```
POST /api/payment-methods/{id}/configs
```

**Body:**
```json
{
  "configs": [
    { "key": "environment", "value": "production", "is_encrypted": false },
    { "key": "production_server_key", "value": "KEY", "is_encrypted": true }
  ]
}
```

**Use Case:** Update multiple configs in one request (form submission)

---

### 5. Delete Config
```
DELETE /api/payment-methods/{id}/configs/{key}
```

**Use Case:** Remove method-specific override, fallback to provider config

---

### 6. Get Provider-Level Configs
```
GET /api/payment-methods/providers/{provider}/configs
```

**Example:**
```javascript
GET /api/payment-methods/providers/midtrans/configs
```

**Returns:**
- `provider` - Provider name
- `config_holder` - Method that holds the provider config
- `configs` - All provider-level configs

**Use Case:** Show shared configs, identify config holder method

---

## Shipping Method Config API

Base Path: `/api/shipping-methods`

### 1. Get All Configs (Merged)
```
GET /api/shipping-methods/{id}/configs
```

---

### 2. Get Single Config
```
GET /api/shipping-methods/{id}/configs/{key}
```

**Example:**
```javascript
GET /api/shipping-methods/14/configs/api_key
```

---

### 3. Update Single Config
```
PUT /api/shipping-methods/{id}/configs/{key}
```

**Body:**
```json
{
  "value": "new-rajaongkir-api-key",
  "is_encrypted": true
}
```

---

### 4. Bulk Update Configs
```
POST /api/shipping-methods/{id}/configs
```

**Body:**
```json
{
  "configs": [
    { "key": "api_key", "value": "KEY", "is_encrypted": true },
    { "key": "origin_city_id", "value": "501", "is_encrypted": false }
  ]
}
```

---

### 5. Delete Config
```
DELETE /api/shipping-methods/{id}/configs/{key}
```

---

### 6. Get Provider-Level Configs
```
GET /api/shipping-methods/providers/{provider}/configs
```

**Example:**
```javascript
GET /api/shipping-methods/providers/rajaongkir/configs
GET /api/shipping-methods/providers/jne/configs
```

---

## Authentication

All endpoints require authentication:
```javascript
headers: {
  'Authorization': 'Bearer {token}',
  'Content-Type': 'application/json'
}
```

---

## Response Format

### Success Response
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Data retrieved successfully",
  "data": { /* ... */ }
}
```

### Error Response
```json
{
  "status": false,
  "statusCode": "404",
  "message": "Payment method not found"
}
```

### Validation Error
```json
{
  "status": false,
  "statusCode": "422",
  "message": "Validation failed",
  "errors": {
    "value": ["The value field is required."]
  }
}
```

---

## Common Use Cases

### 1. Switch Environment (Sandbox → Production)

**Single Update:**
```javascript
PUT /api/payment-methods/4/configs/environment
Body: { "value": "production", "is_encrypted": false }
```

**Advantages:**
- ✅ Only updates one field
- ✅ Fast and efficient
- ✅ Atomic operation

---

### 2. Update All Midtrans Credentials

**Find config holder first:**
```javascript
GET /api/payment-methods/providers/midtrans/configs
// Returns method ID that holds provider config (e.g., id: 4)
```

**Bulk update:**
```javascript
POST /api/payment-methods/4/configs
Body: {
  "configs": [
    { "key": "production_server_key", "value": "NEW-SERVER-KEY", "is_encrypted": true },
    { "key": "production_client_key", "value": "NEW-CLIENT-KEY", "is_encrypted": false },
    { "key": "production_merchant_id", "value": "NEW-MERCHANT-ID", "is_encrypted": false }
  ]
}
```

**Result:** All Midtrans methods (BCA VA, BNI VA, GoPay, QRIS, etc.) inherit new credentials automatically.

---

### 3. Override Specific Config for One Method

**Scenario:** All Midtrans methods use `enable_3ds: true`, but you want to disable it for QRIS only.

```javascript
POST /api/payment-methods/8/configs  // QRIS method ID
Body: {
  "configs": [
    { "key": "enable_3ds", "value": "false", "is_encrypted": false }
  ]
}
```

**Result:**
- QRIS method now has `enable_3ds: false` (method-specific override)
- Other Midtrans methods still use `enable_3ds: true` (provider-level)

---

### 4. Remove Override (Fallback to Provider Config)

```javascript
DELETE /api/payment-methods/8/configs/enable_3ds
```

**Result:** QRIS method now inherits `enable_3ds: true` from provider config again.

---

### 5. Update RajaOngkir API Key

**Find RajaOngkir config holder:**
```javascript
GET /api/shipping-methods/providers/rajaongkir/configs
// Returns method ID (e.g., id: 14)
```

**Update API key:**
```javascript
PUT /api/shipping-methods/14/configs/api_key
Body: { "value": "new-api-key-here", "is_encrypted": true }
```

**Result:** All RajaOngkir methods inherit new API key.

---

### 6. Update Tracking URL for Manual Couriers

```javascript
PUT /api/shipping-methods/1/configs/tracking_url  // JNE method ID
Body: { "value": "https://new-jne-tracking-url.com", "is_encrypted": false }
```

---

## Config Priority System

When fetching configs, the system follows this priority:

1. **Method-Specific Config** (highest priority)
   - Configs stored directly on the method
   - Created via any update endpoint

2. **Provider-Level Config** (fallback)
   - Configs stored on method where `provider_config: true`
   - Shared by all methods with same provider

3. **Merged Result**
   - Method-specific overrides provider-level
   - GET endpoints return merged result

**Example:**

Provider config (Midtrans):
```json
{
  "environment": "sandbox",
  "enable_3ds": "true"
}
```

Method-specific config (QRIS):
```json
{
  "enable_3ds": "false",
  "acquirer": "gopay"
}
```

Merged result (GET /configs):
```json
{
  "environment": "sandbox",      // from provider
  "enable_3ds": "false",          // overridden by method
  "acquirer": "gopay"             // method-specific
}
```

---

## Encrypted vs Non-Encrypted Fields

### Encrypted (is_encrypted: true)
Use for sensitive data:
- API keys (`api_key`, `server_key`)
- Secret keys (`client_secret`)
- Passwords
- Access tokens

**Storage:** Laravel `Crypt::encryptString()` in database
**Retrieval:** Automatically decrypted by repository

### Non-Encrypted (is_encrypted: false)
Use for non-sensitive data:
- Environment settings (`environment`)
- URLs (`tracking_url`)
- IDs (`merchant_id`, `city_id`)
- Public keys (`client_key`)
- Boolean flags (`enable_3ds`)

---

## Frontend Implementation Tips

### 1. Environment Switcher Component
```javascript
const EnvironmentSwitcher = ({ methodId, currentEnv }) => {
  const [environment, setEnvironment] = useState(currentEnv);

  const handleSwitch = async (newEnv) => {
    await axios.put(`/api/payment-methods/${methodId}/configs/environment`, {
      value: newEnv,
      is_encrypted: false
    });
    setEnvironment(newEnv);
  };

  return (
    <select value={environment} onChange={(e) => handleSwitch(e.target.value)}>
      <option value="sandbox">Sandbox</option>
      <option value="production">Production</option>
    </select>
  );
};
```

---

### 2. Config Form with Provider Indicator
```javascript
const ConfigForm = ({ methodId }) => {
  const [configs, setConfigs] = useState({});
  const [isProviderHolder, setIsProviderHolder] = useState(false);

  useEffect(() => {
    axios.get(`/api/payment-methods/${methodId}/configs`)
      .then(res => {
        setConfigs(res.data.data.configs);
        setIsProviderHolder(res.data.data.is_provider_config_holder);
      });
  }, [methodId]);

  return (
    <>
      {isProviderHolder && (
        <Alert type="warning">
          This method holds provider-level configs.
          Changes here affect all methods with the same provider.
        </Alert>
      )}

      {/* Config fields... */}
    </>
  );
};
```

---

### 3. Sensitive Field Input
```javascript
const SensitiveInput = ({ label, configKey, methodId, currentValue }) => {
  const [value, setValue] = useState('');
  const [showValue, setShowValue] = useState(false);

  const handleSave = async () => {
    if (value) {
      await axios.put(`/api/payment-methods/${methodId}/configs/${configKey}`, {
        value: value,
        is_encrypted: true
      });
      setValue('');
      alert('API key updated successfully');
    }
  };

  return (
    <div>
      <label>{label}</label>
      <div style={{ display: 'flex' }}>
        <input
          type={showValue ? 'text' : 'password'}
          value={value}
          onChange={(e) => setValue(e.target.value)}
          placeholder={currentValue ? '••••••••••' : 'Enter new value'}
        />
        <button onClick={() => setShowValue(!showValue)}>
          {showValue ? 'Hide' : 'Show'}
        </button>
        <button onClick={handleSave}>Save</button>
      </div>
    </div>
  );
};
```

---

## Migration from Legacy Endpoint

### Before (Legacy)
```javascript
// Update configs via main endpoint
POST /api/payment-methods/4/config
Body: {
  "configs": [...]
}
```

### After (Recommended)
```javascript
// Use dedicated config endpoint
POST /api/payment-methods/4/configs
Body: {
  "configs": [...]
}
```

**Advantages of dedicated endpoints:**
- ✅ RESTful structure
- ✅ Granular operations (get/update/delete single config)
- ✅ Better separation of concerns
- ✅ More flexibility (provider-level endpoint)
- ✅ Clearer API semantics

**Note:** Legacy endpoint still works for backward compatibility.

---

## Related Documentation

- [BACKOFFICE_API.md](./BACKOFFICE_API.md) - Main API documentation
- [CONFIG_ARCHITECTURE.md](./CONFIG_ARCHITECTURE.md) - Provider-level config system explained
- [FRONTEND_GUIDE_PAYMENT_SHIPPING.md](./FRONTEND_GUIDE_PAYMENT_SHIPPING.md) - Complete frontend implementation guide

---

## Summary

**Total Endpoints:** 12 (6 for payment + 6 for shipping)

**Payment Method Config Endpoints:**
1. GET `/{id}/configs` - Get all configs
2. GET `/{id}/configs/{key}` - Get single config
3. PUT `/{id}/configs/{key}` - Update single config
4. POST `/{id}/configs` - Bulk update
5. DELETE `/{id}/configs/{key}` - Delete config
6. GET `/providers/{provider}/configs` - Get provider configs

**Shipping Method Config Endpoints:**
1. GET `/{id}/configs` - Get all configs
2. GET `/{id}/configs/{key}` - Get single config
3. PUT `/{id}/configs/{key}` - Update single config
4. POST `/{id}/configs` - Bulk update
5. DELETE `/{id}/configs/{key}` - Delete config
6. GET `/providers/{provider}/configs` - Get provider configs

All endpoints are **live and ready to use**!
