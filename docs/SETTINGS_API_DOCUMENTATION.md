# Settings API Documentation

API untuk mengelola settings aplikasi. Semua endpoint memerlukan authentication kecuali yang ada di `/api/store`.

---

## 1. Get All Settings

Mendapatkan semua settings atau filter berdasarkan prefix.

### Endpoint
```
GET /api/settings
GET /api/settings?prefix=store
```

### Headers
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json"
}
```

### Query Parameters
- `prefix` (optional): Filter settings by prefix (e.g., "store", "payment", "shipping")

### Success Response (200)
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Settings retrieved successfully",
  "data": {
    "store.name": {
      "value": "Minimoda Store",
      "description": "Store name"
    },
    "store.email": {
      "value": "info@minimoda.id",
      "description": "Store contact email"
    },
    "store.logo": {
      "value": "https://media.minimoda.id/logo.png",
      "description": "Store logo URL"
    },
    "payment.methods": {
      "value": ["bank_transfer", "credit_card", "e-wallet"],
      "description": "Available payment methods"
    }
  }
}
```

### Frontend Usage (Next.js)
```typescript
// Get all settings
const response = await fetch('/api/settings', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
const data = await response.json()

// Get store settings only
const storeSettings = await fetch('/api/settings?prefix=store', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
})
```

---

## 2. Get Settings by Pattern

Mendapatkan settings berdasarkan pattern/prefix tertentu.

### Endpoint
```
GET /api/settings/{pattern}
```

### Example
```
GET /api/settings/store
GET /api/settings/payment
```

### Headers
```json
{
  "Authorization": "Bearer {token}",
  "Accept": "application/json"
}
```

### Success Response (200)
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Settings retrieved successfully",
  "data": {
    "store.name": {
      "value": "Minimoda Store",
      "description": "Store name"
    },
    "store.email": {
      "value": "info@minimoda.id",
      "description": "Store contact email"
    },
    "store.phone": {
      "value": "+62812345678",
      "description": "Store phone number"
    }
  }
}
```

### Frontend Usage (Next.js)
```typescript
// Get store settings
const getStoreSettings = async () => {
  const response = await fetch('/api/settings/store', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  })
  return await response.json()
}
```

---

## 3. Update Setting by Key

Update satu setting berdasarkan key.

### Endpoint
```
PUT /api/settings/{key}
```

### Example
```
PUT /api/settings/store.name
PUT /api/settings/payment.methods
```

### Headers
```json
{
  "Authorization": "Bearer {admin_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

### Request Body

#### Simple Value (String/Number)
```json
{
  "value": "Minimoda Official Store",
  "description": "Store name (optional)"
}
```

#### Array Value
```json
{
  "value": ["bank_transfer", "credit_card", "e-wallet", "cod"],
  "description": "Available payment methods"
}
```

#### Object Value
```json
{
  "value": {
    "bank_name": "BCA",
    "account_number": "1234567890",
    "account_name": "Minimoda Store"
  },
  "description": "Bank transfer details"
}
```

### Success Response (200)
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Setting updated successfully",
  "data": {
    "key": "store.name",
    "value": "Minimoda Official Store",
    "description": "Store name",
    "updated_at": "2025-10-12 00:30:15"
  }
}
```

### Error Response (404)
```json
{
  "status": false,
  "statusCode": "404",
  "message": "Setting not found: store.invalid_key"
}
```

### Validation Error (422)
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

### Frontend Usage (Next.js/React)
```typescript
// Update store name
const updateStoreName = async (newName: string) => {
  const response = await fetch('/api/settings/store.name', {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${adminToken}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      value: newName,
      description: 'Store name'
    })
  })

  const result = await response.json()

  if (result.status) {
    console.log('Setting updated:', result.data)
    // Show success notification
  } else {
    console.error('Update failed:', result.message)
    // Show error notification
  }

  return result
}

// Update payment methods (array)
const updatePaymentMethods = async (methods: string[]) => {
  const response = await fetch('/api/settings/payment.methods', {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${adminToken}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      value: methods
    })
  })

  return await response.json()
}

// Update bank details (object)
const updateBankDetails = async (bankInfo: {
  bank_name: string
  account_number: string
  account_name: string
}) => {
  const response = await fetch('/api/settings/payment.bank_transfer', {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${adminToken}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      value: bankInfo,
      description: 'Bank transfer details'
    })
  })

  return await response.json()
}
```

---

## 4. Bulk Update Settings

Update beberapa settings sekaligus dalam satu request.

### Endpoint
```
POST /api/settings/bulk-update
```

### Headers
```json
{
  "Authorization": "Bearer {admin_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

### Request Body
```json
{
  "settings": [
    {
      "key": "store.name",
      "value": "Minimoda Official Store",
      "description": "Store name"
    },
    {
      "key": "store.email",
      "value": "support@minimoda.id",
      "description": "Store contact email"
    },
    {
      "key": "store.phone",
      "value": "+628123456789"
    },
    {
      "key": "payment.methods",
      "value": ["bank_transfer", "credit_card", "e-wallet", "cod"]
    }
  ]
}
```

### Success Response (200)
```json
{
  "status": true,
  "statusCode": "200",
  "message": "4 setting(s) updated successfully",
  "data": {
    "updated": [
      "store.name",
      "store.email",
      "store.phone",
      "payment.methods"
    ],
    "not_found": [],
    "total_updated": 4,
    "total_not_found": 0
  }
}
```

### Partial Success Response (200)
```json
{
  "status": true,
  "statusCode": "200",
  "message": "3 setting(s) updated successfully",
  "data": {
    "updated": [
      "store.name",
      "store.email",
      "store.phone"
    ],
    "not_found": [
      "invalid.key"
    ],
    "total_updated": 3,
    "total_not_found": 1
  }
}
```

### Frontend Usage (Next.js/React)
```typescript
// Bulk update store settings
const updateStoreSettings = async (settings: {
  name: string
  email: string
  phone: string
  address: string
}) => {
  const response = await fetch('/api/settings/bulk-update', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${adminToken}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      settings: [
        {
          key: 'store.name',
          value: settings.name
        },
        {
          key: 'store.email',
          value: settings.email
        },
        {
          key: 'store.phone',
          value: settings.phone
        },
        {
          key: 'store.address',
          value: settings.address
        }
      ]
    })
  })

  const result = await response.json()

  if (result.status) {
    console.log(`${result.data.total_updated} settings updated`)

    if (result.data.not_found.length > 0) {
      console.warn('Settings not found:', result.data.not_found)
    }
  }

  return result
}

// Bulk update with form data
const handleSubmit = async (formData: any) => {
  const settingsToUpdate = Object.entries(formData).map(([key, value]) => ({
    key: `store.${key}`,
    value: value
  }))

  const response = await fetch('/api/settings/bulk-update', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${adminToken}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      settings: settingsToUpdate
    })
  })

  return await response.json()
}
```

---

## Complete React Example

```typescript
import { useState, useEffect } from 'react'

interface Setting {
  key: string
  value: any
  description?: string
}

export default function SettingsPage() {
  const [settings, setSettings] = useState<Record<string, any>>({})
  const [loading, setLoading] = useState(false)
  const token = 'your_admin_token'

  // Fetch settings
  useEffect(() => {
    fetchSettings()
  }, [])

  const fetchSettings = async () => {
    setLoading(true)
    try {
      const response = await fetch('/api/settings?prefix=store', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })

      const result = await response.json()
      if (result.status) {
        // Convert to simple key-value
        const settingsData: Record<string, any> = {}
        Object.entries(result.data).forEach(([key, data]: [string, any]) => {
          settingsData[key] = data.value
        })
        setSettings(settingsData)
      }
    } catch (error) {
      console.error('Failed to fetch settings:', error)
    } finally {
      setLoading(false)
    }
  }

  // Update single setting
  const updateSetting = async (key: string, value: any) => {
    try {
      const response = await fetch(`/api/settings/${key}`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ value })
      })

      const result = await response.json()

      if (result.status) {
        // Update local state
        setSettings(prev => ({
          ...prev,
          [key]: result.data.value
        }))
        alert('Setting updated successfully!')
      } else {
        alert('Failed to update: ' + result.message)
      }
    } catch (error) {
      console.error('Update error:', error)
      alert('Failed to update setting')
    }
  }

  // Bulk update
  const bulkUpdate = async (updates: Record<string, any>) => {
    const settingsArray = Object.entries(updates).map(([key, value]) => ({
      key,
      value
    }))

    try {
      const response = await fetch('/api/settings/bulk-update', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          settings: settingsArray
        })
      })

      const result = await response.json()

      if (result.status) {
        alert(`${result.data.total_updated} settings updated!`)
        fetchSettings() // Refresh
      }
    } catch (error) {
      console.error('Bulk update error:', error)
    }
  }

  return (
    <div>
      <h1>Store Settings</h1>

      {loading ? (
        <p>Loading...</p>
      ) : (
        <div>
          <input
            value={settings['store.name'] || ''}
            onChange={(e) => updateSetting('store.name', e.target.value)}
            placeholder="Store Name"
          />

          <input
            value={settings['store.email'] || ''}
            onChange={(e) => updateSetting('store.email', e.target.value)}
            placeholder="Store Email"
          />

          <button onClick={() => bulkUpdate({
            'store.name': 'New Store Name',
            'store.email': 'new@email.com'
          })}>
            Bulk Update
          </button>
        </div>
      )}
    </div>
  )
}
```

---

## Notes

1. **Value Types**: API support string, number, array, dan object
2. **Authentication**: Semua update endpoint memerlukan admin token
3. **Activity Log**: Semua update di-log dengan old_value dan new_value
4. **Validation**: value field wajib diisi
5. **Bulk Update**: Tidak akan rollback jika ada yang gagal, hanya skip setting yang tidak ditemukan
