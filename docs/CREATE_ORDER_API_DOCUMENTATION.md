# Create Order API Documentation

## Endpoint
```
POST /api/orders
```

**Authentication:** Required (Bearer Token)

---

## Request Body

```typescript
{
  customer_id: number;           // Required - Customer ID
  items: array;                  // Required - Array of order items (min 1)
  items[].variant_id: number;    // Required - Product variant ID
  items[].quantity: number;      // Required - Quantity (min 1)
  items[].price: number;         // Required - Price per unit
  shipping_address: string;      // Required - Full shipping address
  shipping_city: string;         // Required - City
  shipping_province: string;     // Required - Province
  shipping_postal_code?: string; // Optional - Postal code
  shipping_phone: string;        // Required - Phone number
  courier?: string;              // Optional - Courier name (JNE, TIKI, etc)
  shipping_cost: number;         // Required - Shipping cost
  payment_method: string;        // Required - Payment method
  coupon_code?: string;          // Optional - Coupon code for discount
  notes?: string;                // Optional - Order notes
  shipping_notes?: string;       // Optional - Shipping notes
}
```

---

## Request Example

```bash
curl -X POST "http://api-local.minimoda.id/api/orders" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "items": [
      {
        "variant_id": 5,
        "quantity": 2,
        "price": 150000
      },
      {
        "variant_id": 8,
        "quantity": 1,
        "price": 200000
      }
    ],
    "shipping_address": "Jl. Merdeka No. 123, RT 001/RW 002",
    "shipping_city": "Jakarta Selatan",
    "shipping_province": "DKI Jakarta",
    "shipping_postal_code": "12345",
    "shipping_phone": "081234567890",
    "courier": "JNE",
    "shipping_cost": 15000,
    "payment_method": "bank_transfer",
    "coupon_code": "DISC50",
    "notes": "Tolong kirim cepat",
    "shipping_notes": "Hubungi sebelum kirim"
  }'
```

---

## Response (Success - 201)

```json
{
  "status": true,
  "statusCode": "201",
  "message": "Order created successfully",
  "data": {
    "id": 15,
    "order_number": "ORD-20251014-A3F2B1",
    "customer_id": 1,
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "customer_phone": "081234567890",
    "status": "pending",
    "payment_method": "bank_transfer",
    "payment_status": "pending",
    "shipping_address": "Jl. Merdeka No. 123, RT 001/RW 002",
    "shipping_city": "Jakarta Selatan",
    "shipping_province": "DKI Jakarta",
    "shipping_postal_code": "12345",
    "shipping_phone": "081234567890",
    "courier": "JNE",
    "shipping_cost": 15000,
    "subtotal": 500000,
    "tax_amount": 0,
    "discount_amount": 250000,
    "total_amount": 265000,
    "notes": "Tolong kirim cepat",
    "shipping_notes": "Hubungi sebelum kirim",
    "items": [
      {
        "id": 25,
        "order_id": 15,
        "variant_id": 5,
        "product_name": "Mainan Edukasi Puzzle",
        "sku": "PZL-001-M-RED",
        "size": "M",
        "color": "Red",
        "quantity": 2,
        "price": 150000,
        "subtotal": 300000,
        "discount_amount": 0,
        "total": 300000
      },
      {
        "id": 26,
        "order_id": 15,
        "variant_id": 8,
        "product_name": "Boneka Unicorn",
        "sku": "UNI-001-L-PINK",
        "size": "L",
        "color": "Pink",
        "quantity": 1,
        "price": 200000,
        "subtotal": 200000,
        "discount_amount": 0,
        "total": 200000
      }
    ],
    "created_at": "2025-10-14T00:45:30.000000Z",
    "updated_at": "2025-10-14T00:45:30.000000Z"
  }
}
```

---

## How It Works

### 1. **Order Number Generation**
```
Format: ORD-YYYYMMDD-XXXXXX
Example: ORD-20251014-A3F2B1
```

### 2. **Price Calculation**
```typescript
// Step 1: Calculate subtotal
subtotal = sum(item.price * item.quantity)

// Step 2: Apply coupon discount (if valid)
if (coupon_code) {
  if (coupon.type === 'percentage') {
    discount = (subtotal * coupon.value) / 100
    if (coupon.max_discount && discount > coupon.max_discount) {
      discount = coupon.max_discount
    }
  } else if (coupon.type === 'fixed') {
    discount = coupon.value
  }
}

// Step 3: Calculate total
total = subtotal - discount + tax + shipping_cost
```

### 3. **Coupon Validation**
- Check if coupon is active
- Check if coupon is within date range
- Check if minimum purchase met
- Check if usage limit not exceeded
- Check if customer usage limit not exceeded

### 4. **Stock Management**
- Automatically decrements stock for each variant
- Uses `DB::decrement()` for atomic operation

### 5. **Coupon Usage Recording**
- Records coupon usage in `coupon_usage` table
- Increments coupon usage count

---

## Validation Rules

| Field | Rules |
|-------|-------|
| customer_id | Required, must exist in customers table |
| items | Required, array with minimum 1 item |
| items.*.variant_id | Required, must exist in product_variants table |
| items.*.quantity | Required, integer, minimum 1 |
| items.*.price | Required, numeric, minimum 0 |
| shipping_address | Required, string |
| shipping_city | Required, string |
| shipping_province | Required, string |
| shipping_postal_code | Optional, string |
| shipping_phone | Required, string |
| courier | Optional, string |
| shipping_cost | Required, numeric, minimum 0 |
| payment_method | Required, string |
| coupon_code | Optional, string |
| notes | Optional, string |
| shipping_notes | Optional, string |

---

## Error Responses

### 422 - Validation Error
```json
{
  "status": false,
  "statusCode": "422",
  "message": "The customer id field is required.",
  "data": {
    "errors": {
      "customer_id": ["The customer id field is required."],
      "items": ["The items field is required."]
    }
  }
}
```

### 500 - Server Error
```json
{
  "status": false,
  "statusCode": "500",
  "message": "Failed to create order: Error message here",
  "data": {}
}
```

---

## Payment Methods

Common payment methods:
- `bank_transfer` - Bank Transfer
- `credit_card` - Credit Card
- `e_wallet` - E-Wallet (OVO, GoPay, Dana, etc)
- `cod` - Cash on Delivery
- `installment` - Installment

---

## Order Status Flow

```
pending -> processing -> shipped -> completed
   |
   +----> cancelled
```

**Status Values:**
- `pending` - Order created, awaiting payment
- `processing` - Payment confirmed, preparing order
- `shipped` - Order shipped
- `completed` - Order delivered and completed
- `cancelled` - Order cancelled

---

## Payment Status

- `pending` - Awaiting payment
- `paid` - Payment confirmed
- `failed` - Payment failed
- `refunded` - Payment refunded

---

## Example Scenarios

### Scenario 1: Order Without Coupon
```bash
curl -X POST "http://api-local.minimoda.id/api/orders" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "items": [
      {"variant_id": 5, "quantity": 1, "price": 150000}
    ],
    "shipping_address": "Jl. Merdeka No. 123",
    "shipping_city": "Jakarta",
    "shipping_province": "DKI Jakarta",
    "shipping_phone": "081234567890",
    "shipping_cost": 15000,
    "payment_method": "bank_transfer"
  }'
```

**Result:**
- Subtotal: 150,000
- Discount: 0
- Shipping: 15,000
- **Total: 165,000**

---

### Scenario 2: Order With 50% Coupon
```bash
curl -X POST "http://api-local.minimoda.id/api/orders" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "items": [
      {"variant_id": 5, "quantity": 2, "price": 150000}
    ],
    "shipping_address": "Jl. Merdeka No. 123",
    "shipping_city": "Jakarta",
    "shipping_province": "DKI Jakarta",
    "shipping_phone": "081234567890",
    "shipping_cost": 15000,
    "payment_method": "bank_transfer",
    "coupon_code": "DISC50"
  }'
```

**Result:**
- Subtotal: 300,000
- Discount: 150,000 (50%)
- Shipping: 15,000
- **Total: 165,000**

---

### Scenario 3: Order With Fixed Amount Coupon
```bash
curl -X POST "http://api-local.minimoda.id/api/orders" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "items": [
      {"variant_id": 5, "quantity": 1, "price": 200000}
    ],
    "shipping_address": "Jl. Merdeka No. 123",
    "shipping_city": "Jakarta",
    "shipping_province": "DKI Jakarta",
    "shipping_phone": "081234567890",
    "shipping_cost": 15000,
    "payment_method": "bank_transfer",
    "coupon_code": "SAVE50K"
  }'
```

**Result:**
- Subtotal: 200,000
- Discount: 50,000 (fixed)
- Shipping: 15,000
- **Total: 165,000**

---

## Features

✅ **Automatic order number generation** (ORD-YYYYMMDD-XXXXXX)
✅ **Multiple items support** (cart items)
✅ **Coupon discount** (percentage, fixed, free_shipping)
✅ **Automatic stock management** (decrements variant stock)
✅ **Coupon usage tracking** (records usage, prevents over-use)
✅ **Transaction rollback** (on error)
✅ **Activity logging** (audit trail)
✅ **Complete validation** (customer, variants, stock, coupon)

---

## Notes

1. **Stock Check**: Ensure variants have sufficient stock before creating order
2. **Price Validation**: Validate prices match current product prices
3. **Customer Exists**: Customer must exist in database
4. **Coupon Validity**: Coupon will be validated automatically
5. **Transaction Safety**: Uses DB transaction with rollback on error

---

## Frontend Integration

```typescript
// composables/useOrders.ts
export const useOrders = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const createOrder = async (orderData: any) => {
    return await $fetch('/api/orders', {
      method: 'POST',
      baseURL: config.public.apiBase,
      headers: {
        Authorization: `Bearer ${authStore.token}`
      },
      body: orderData
    })
  }

  return { createOrder }
}

// Usage in component
const { createOrder } = useOrders()

const handleCheckout = async () => {
  try {
    const order = await createOrder({
      customer_id: customer.value.id,
      items: cartItems.value.map(item => ({
        variant_id: item.variant_id,
        quantity: item.quantity,
        price: item.price
      })),
      shipping_address: shippingAddress.value,
      shipping_city: shippingCity.value,
      shipping_province: shippingProvince.value,
      shipping_phone: shippingPhone.value,
      shipping_cost: shippingCost.value,
      payment_method: paymentMethod.value,
      coupon_code: couponCode.value || null
    })

    // Redirect to order confirmation
    router.push(`/orders/${order.data.id}`)
  } catch (error) {
    console.error('Failed to create order:', error)
  }
}
```

---

**Last Updated:** 2025-10-14
