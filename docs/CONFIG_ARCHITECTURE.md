# Payment & Shipping Configuration Architecture

This document explains the provider-level shared configuration system for payment and shipping methods.

## Overview

The system uses a **hierarchical config structure** to eliminate duplicate configurations and improve maintainability:

- **Provider-Level Configs**: Shared across all methods from the same provider
- **Method-Specific Configs**: Unique settings for individual methods
- **Automatic Fallback**: Methods inherit from provider configs when method-specific config doesn't exist

## Configuration Priority

When retrieving configuration for a payment/shipping method:

1. **Method-Specific Config** (highest priority)
   - If the method has its own config for a key, use it

2. **Provider-Level Config** (fallback)
   - If method doesn't have the config, check if provider has it

3. **Merged Result**
   - Return combined configs (method-specific overrides provider-level)

## Implementation

### Repository Methods

Both `PaymentMethodRepository` and `ShippingMethodRepository` implement smart config retrieval:

```php
// Get all configs (merged provider + method-specific)
$config = $paymentMethodRepo->getConfig($methodId);

// Get specific config key (with fallback to provider)
$serverKey = $paymentMethodRepo->getConfig($methodId, 'sandbox_server_key');
```

### Database Structure

```sql
-- Mark one method per provider as config holder
payment_method_config:
  payment_method_id: 4 (midtrans_bca_va)
  key: 'provider_config'
  value: 'true'

-- All other methods from same provider inherit this
payment_methods:
  id: 4, provider: 'midtrans'  -- Config holder
  id: 5, provider: 'midtrans'  -- Inherits from id=4
  id: 6, provider: 'midtrans'  -- Inherits from id=4
```

---

## Payment Methods Configuration

### Midtrans Provider (8 methods)

**Provider-Level Config** (stored in `midtrans_bca_va`):
```json
{
  "provider_config": "true",
  "environment": "sandbox",
  "sandbox_server_key": "SB-Mid-server-...",
  "sandbox_client_key": "SB-Mid-client-...",
  "sandbox_merchant_id": "M123456",
  "production_server_key": "Mid-server-...",
  "production_client_key": "Mid-client-...",
  "production_merchant_id": "M654321",
  "enable_3ds": "true"
}
```

**Methods Inheriting** (no individual configs needed):
- `midtrans_bca_va` (also the config holder)
- `midtrans_bni_va`
- `midtrans_mandiri_va`
- `midtrans_permata_va`
- `midtrans_gopay`
- `midtrans_shopeepay`

**Methods with Unique Configs**:
- `midtrans_qris`:
  - Unique: `acquirer: gopay`
  - Inherits: All Midtrans credentials

- `midtrans_credit_card`:
  - Unique: `enable_installment: false`, `enable_saved_card: true`
  - Inherits: All Midtrans credentials

### Manual Bank Transfer Provider (3 methods)

**Provider-Level Config** (stored in `bank_transfer_bca`):
```json
{
  "provider_config": "true",
  "account_name": "PT Minimoda Indonesia"
}
```

**Method-Specific Configs**:
- `bank_transfer_bca`:
  - Unique: `bank_name: Bank BCA`, `account_number: 1234567890`
  - Also holds provider config

- `bank_transfer_mandiri`:
  - Unique: `bank_name: Bank Mandiri`, `account_number: 0987654321`
  - Inherits: `account_name`

- `bank_transfer_bni`:
  - Unique: `bank_name: Bank BNI`, `account_number: 1122334455`
  - Inherits: `account_name`

### COD (Cash on Delivery)

No configs needed - handled in code.

---

## Shipping Methods Configuration

### RajaOngkir Provider (3 methods)

**Provider-Level Config** (stored in `rajaongkir_jne`):
```json
{
  "provider_config": "true",
  "api_key": "your-rajaongkir-api-key",
  "account_type": "starter",
  "origin_city_id": "501",
  "origin_province_id": "6"
}
```

**Method-Specific Configs**:
- `rajaongkir_jne`:
  - Unique: `courier_code: jne`
  - Also holds provider config

- `rajaongkir_tiki`:
  - Unique: `courier_code: tiki`
  - Inherits: API credentials and origin

- `rajaongkir_pos`:
  - Unique: `courier_code: pos`
  - Inherits: API credentials and origin

### Manual Couriers (JNE, J&T, SiCepat, POS)

Each courier provider has shared tracking URL:

**JNE Provider** (stored in `jne_reg`):
```json
{
  "provider_config": "true",
  "tracking_url": "https://www.jne.co.id/id/tracking/trace"
}
```

Methods `jne_reg` and `jne_yes` both inherit the tracking URL.

**J&T Provider** (stored in `jnt_reg`):
```json
{
  "provider_config": "true",
  "tracking_url": "https://www.jet.co.id/track"
}
```

Methods `jnt_reg` and `jnt_express` both inherit the tracking URL.

**SiCepat Provider** (stored in `sicepat_reg`):
```json
{
  "provider_config": "true",
  "tracking_url": "https://sicepat.com/checkAwb"
}
```

Methods `sicepat_reg` and `sicepat_best` both inherit the tracking URL.

**POS Provider** (stored in `pos_reg`):
```json
{
  "provider_config": "true",
  "tracking_url": "https://www.posindonesia.co.id/id/tracking"
}
```

### Instant Couriers (GoSend, Grab)

**GoSend Provider**:
```json
{
  "provider_config": "true",
  "api_key": "your-gojek-api-key",
  "merchant_id": "MERCHANT123",
  "max_distance_km": "25"
}
```

**Grab Provider**:
```json
{
  "provider_config": "true",
  "api_key": "your-grab-api-key",
  "merchant_id": "MERCHANT456",
  "max_distance_km": "25"
}
```

### Custom Methods (Store Courier, Free Shipping)

These have no provider, only method-specific configs:

**Store Courier**:
```json
{
  "coverage_area": "Jakarta, Tangerang, Bekasi",
  "phone_number": "081234567890",
  "notes": "Pengiriman hanya untuk area Jabodetabek"
}
```

**Free Shipping**:
```json
{
  "min_purchase": "500000",
  "max_weight": "5000",
  "notes": "Gratis ongkir untuk pembelian minimal Rp 500,000"
}
```

---

## Benefits

### 1. Reduced Redundancy

**Before** (duplicate configs):
- Payment: 49 configs
- Shipping: 41 configs
- Total: 90 configs

**After** (shared configs):
- Payment: 20 configs (**-59%**)
- Shipping: 30 configs (**-27%**)
- Total: 50 configs (**-44% reduction**)

### 2. Easier Maintenance

**Update Midtrans Credentials:**
```bash
# Before: Update 8 methods individually
# After: Update 1 provider config → affects all 8 methods
```

**Switch Environment (Sandbox → Production):**
```bash
# Before: Update environment in 8 methods
# After: Update 1 config key → all methods switch
```

### 3. Consistency

- All methods from same provider use same credentials
- No risk of outdated credentials in one method
- Single source of truth per provider

### 4. Flexibility

- Can still override provider configs at method level
- Add method-specific features without affecting others
- Provider configs are optional (custom methods can skip them)

---

## Usage Examples

### Example 1: Get Midtrans GoPay Config

```php
$config = $paymentMethodRepo->getConfig($midtransGopayId);

// Returns merged config:
[
  'provider_config' => 'true',
  'environment' => 'sandbox',
  'sandbox_server_key' => 'decrypted-key',
  'sandbox_client_key' => 'client-key',
  'sandbox_merchant_id' => 'M123456',
  'production_server_key' => 'decrypted-key',
  'production_client_key' => 'client-key',
  'production_merchant_id' => 'M654321',
  'enable_3ds' => 'true'
]
```

All inherited from `midtrans_bca_va` (the provider config holder).

### Example 2: Get Midtrans QRIS Config

```php
$config = $paymentMethodRepo->getConfig($midtransQrisId);

// Returns merged config:
[
  'provider_config' => 'true',
  'environment' => 'sandbox',
  'sandbox_server_key' => 'decrypted-key',
  // ... all Midtrans credentials ...
  'enable_3ds' => 'true',
  'acquirer' => 'gopay'  // ← Method-specific config
]
```

Inherits Midtrans credentials + has its own `acquirer` config.

### Example 3: Get BNI Bank Transfer Config

```php
$config = $paymentMethodRepo->getConfig($bniTransferId);

// Returns merged config:
[
  'provider_config' => 'true',          // From provider
  'account_name' => 'PT Minimoda Indonesia', // From provider
  'bank_name' => 'Bank BNI',            // Method-specific
  'account_number' => '1122334455'      // Method-specific
]
```

### Example 4: Get JNE YES Tracking URL

```php
$trackingUrl = $shippingMethodRepo->getConfig($jneYesId, 'tracking_url');

// Returns: "https://www.jne.co.id/id/tracking/trace"
// Inherited from jne_reg provider config
```

---

## How to Add New Methods

### Adding a New Midtrans Payment Method

1. Add the payment method to database (via seeder or API):
```php
DB::table('payment_methods')->insert([
  'code' => 'midtrans_linkaja',
  'name' => 'LinkAja via Midtrans',
  'type' => 'e_wallet',
  'provider' => 'midtrans',
  'is_active' => true,
]);
```

2. **No config needed!** It automatically inherits all Midtrans credentials.

3. (Optional) If method needs unique config:
```php
$paymentMethodRepo->setConfig($linkAjaId, 'deep_link', 'linkaja://pay', false);
```

### Adding a New Manual Courier

1. Add the shipping method:
```php
DB::table('shipping_methods')->insert([
  'code' => 'jne_oke',
  'name' => 'JNE OKE',
  'type' => 'manual',
  'provider' => 'jne',
  'base_cost' => 8000,
  'cost_per_kg' => 3000,
]);
```

2. **No config needed!** It automatically inherits JNE tracking URL.

---

## Best Practices

1. **Provider Config Holder Selection**
   - Choose the most commonly used method as the config holder
   - For Midtrans: Use `midtrans_bca_va` (most popular VA)
   - For manual couriers: Use the REG service (most common)

2. **Config Naming**
   - Always mark provider configs with `provider_config: true`
   - Use clear key names: `sandbox_*` and `production_*` prefixes
   - Use `is_encrypted: true` for sensitive keys

3. **Environment Switching**
   - Keep both sandbox and production configs in database
   - Use `environment` key to toggle: `sandbox` or `production`
   - Application logic reads active environment to use correct keys

4. **Testing**
   - Test config fallback by calling `getConfig()` on non-holder methods
   - Verify method-specific configs override provider configs
   - Check encrypted values are properly decrypted

---

## Migration Guide

If you have existing individual configs and want to migrate to shared configs:

1. **Identify Provider Groups**
   - Group methods by `provider` field
   - Find common configs across the group

2. **Choose Config Holder**
   - Pick one method from each provider
   - Usually the first or most popular method

3. **Move Configs to Holder**
   - Add `provider_config: true` to holder
   - Keep only unique configs in other methods
   - Delete duplicate configs

4. **Test Retrieval**
   - Verify all methods can access configs
   - Check fallback works correctly

5. **Run Seeders**
```bash
php artisan tinker --execute="DB::table('payment_method_config')->truncate();"
php artisan db:seed --class=PaymentMethodConfigSeeder

php artisan tinker --execute="DB::table('shipping_method_config')->truncate();"
php artisan db:seed --class=ShippingMethodConfigSeeder
```

---

## Troubleshooting

**Q: Method not getting provider config?**

A: Check that:
1. Provider field matches exactly (case-sensitive)
2. Config holder has `provider_config: true`
3. Repository is using the latest implementation

**Q: How to override a provider config?**

A: Just add a method-specific config with the same key. It will take priority.

**Q: Can I have multiple config holders per provider?**

A: Technically yes, but not recommended. The system will use the first one found. Keep one holder per provider for clarity.

**Q: What if provider is null?**

A: Methods with `provider: null` or `provider: custom` won't have provider-level configs. They must have all configs at method level.

---

## Summary

The provider-level shared config architecture provides:
- ✅ **44% reduction** in total configs (90 → 50)
- ✅ **Single source of truth** per provider
- ✅ **Easy environment switching** (sandbox ↔ production)
- ✅ **DRY principle** - no duplicate credentials
- ✅ **Backward compatible** - existing code still works
- ✅ **Flexible** - method-specific overrides still possible

This architecture makes the system more maintainable, consistent, and easier to manage as it scales.
