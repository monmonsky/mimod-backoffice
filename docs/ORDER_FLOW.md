# Order Flow & Invoice Documentation

## 1. Order Status Flow

### Status Progression
```
pending → processing → shipped → completed
   ↓
cancelled (dapat dilakukan di status apapun kecuali completed)
```

### Status Definitions

| Status | Description | Actions Available |
|--------|-------------|-------------------|
| **pending** | Order baru dibuat, menunggu konfirmasi pembayaran | Confirm payment, Cancel |
| **processing** | Pembayaran sudah dikonfirmasi, order sedang diproses | Ship order, Cancel |
| **shipped** | Order sudah dikirim | Mark as completed |
| **completed** | Order selesai, barang sudah diterima customer | - (final state) |
| **cancelled** | Order dibatalkan | - (final state) |

---

## 2. Order History (order_status_history)

### Purpose
Mencatat setiap perubahan status order untuk audit trail dan tracking.

### Table Structure
```sql
order_status_history:
- id
- order_id (FK to orders)
- status (pending, processing, shipped, completed, cancelled)
- notes (catatan perubahan, opsional)
- user_id (admin yang melakukan perubahan, nullable untuk system)
- created_at
```

### Flow Implementation

#### Ketika Order Dibuat (Store API)
```php
// 1. Create order with status 'pending'
$orderId = DB::table('orders')->insertGetId([
    'status' => 'pending',
    'payment_status' => 'unpaid',
    // ... other fields
]);

// 2. Create history record
DB::table('order_status_history')->insert([
    'order_id' => $orderId,
    'status' => 'pending',
    'notes' => 'Order created via WhatsApp checkout',
    'user_id' => null, // system created
    'created_at' => now()
]);
```

#### Ketika Status Diupdate (Backoffice API)
```php
// Endpoint: PUT /api/orders/{id}/status
// Body: { "status": "processing", "notes": "Payment confirmed" }

DB::beginTransaction();

// 1. Update order status
DB::table('orders')->where('id', $orderId)->update([
    'status' => $newStatus,
    'paid_at' => ($newStatus === 'processing') ? now() : null,
    'shipped_at' => ($newStatus === 'shipped') ? now() : null,
    'completed_at' => ($newStatus === 'completed') ? now() : null,
    'cancelled_at' => ($newStatus === 'cancelled') ? now() : null,
    'updated_at' => now()
]);

// 2. Create history record
DB::table('order_status_history')->insert([
    'order_id' => $orderId,
    'status' => $newStatus,
    'notes' => $notes,
    'user_id' => auth()->id(), // admin yang melakukan
    'created_at' => now()
]);

DB::commit();
```

### API Endpoints untuk Order History

#### Get Order History
```
GET /api/orders/{id}/history
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Order history retrieved successfully",
  "data": [
    {
      "id": 1,
      "order_id": 123,
      "status": "pending",
      "notes": "Order created via WhatsApp checkout",
      "user_id": null,
      "user_name": "System",
      "created_at": "2025-10-26 10:00:00"
    },
    {
      "id": 2,
      "order_id": 123,
      "status": "processing",
      "notes": "Payment confirmed",
      "user_id": 5,
      "user_name": "Admin User",
      "created_at": "2025-10-26 11:30:00"
    },
    {
      "id": 3,
      "order_id": 123,
      "status": "shipped",
      "notes": "Shipped via JNE REG - AWB: 1234567890",
      "user_id": 5,
      "user_name": "Admin User",
      "created_at": "2025-10-26 14:00:00"
    }
  ]
}
```

---

## 3. Invoice Flow

### Invoice Structure

Invoice adalah tampilan detail order yang dapat dicetak atau dikirim ke customer.

### Invoice Components

1. **Invoice Header**
   - Invoice Number (sama dengan order_number)
   - Invoice Date (created_at)
   - Status badge

2. **Store Information**
   - Store name
   - Store address
   - Phone number
   - Email

3. **Customer Information**
   - Customer name
   - Shipping address
   - Phone number
   - Email (if available)

4. **Order Items**
   - Product name
   - Variant (size, color)
   - Quantity
   - Unit price
   - Subtotal

5. **Order Summary**
   - Subtotal
   - Shipping cost
   - Tax amount
   - Discount amount
   - **Total Amount**

6. **Payment Information**
   - Payment method
   - Payment status
   - Paid at (if paid)

7. **Shipping Information**
   - Courier
   - Tracking number (if shipped)
   - Shipped at (if shipped)

### Invoice API Endpoints

#### Get Invoice Data
```
GET /api/orders/{id}/invoice
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Invoice retrieved successfully",
  "data": {
    "invoice": {
      "invoice_number": "ORD-20251026-0001",
      "invoice_date": "2025-10-26 10:00:00",
      "status": "processing",
      "payment_status": "paid"
    },
    "store": {
      "name": "Minimoda Store",
      "address": "Jl. Example No. 123, Jakarta",
      "phone": "021-12345678",
      "email": "info@minimoda.id"
    },
    "customer": {
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890",
      "address": "Jl. Customer No. 456, Bandung, Jawa Barat 40123"
    },
    "items": [
      {
        "product_name": "Kemeja Formal",
        "variant": "Size: L, Color: Blue",
        "sku": "KMJ-001-L-BLU",
        "quantity": 2,
        "price": 150000,
        "subtotal": 300000
      }
    ],
    "summary": {
      "subtotal": 300000,
      "shipping_cost": 25000,
      "tax_amount": 0,
      "discount_amount": 0,
      "total_amount": 325000
    },
    "payment": {
      "method": "Transfer Bank",
      "status": "paid",
      "paid_at": "2025-10-26 11:30:00"
    },
    "shipping": {
      "courier": "JNE REG",
      "tracking_number": "1234567890",
      "shipped_at": "2025-10-26 14:00:00",
      "address": "Jl. Customer No. 456",
      "city": "Bandung",
      "province": "Jawa Barat",
      "postal_code": "40123",
      "phone": "081234567890"
    }
  }
}
```

#### Send Invoice via Email
```
POST /api/orders/{id}/send-invoice
```

**Request:**
```json
{
  "email": "customer@example.com",
  "message": "Terima kasih atas pesanan Anda. Berikut invoice untuk order Anda."
}
```

**Response:**
```json
{
  "status": true,
  "statusCode": "200",
  "message": "Invoice sent successfully",
  "data": {
    "email": "customer@example.com",
    "sent_at": "2025-10-26 15:00:00"
  }
}
```

#### Download Invoice PDF
```
GET /api/orders/{id}/invoice/download
```

Returns PDF file with invoice.

---

## 4. Implementation Checklist

### Backend API

- [x] Order creation with initial history (StoreOrderController)
- [ ] Update order status with history tracking
- [ ] Get order history endpoint
- [ ] Get invoice data endpoint
- [ ] Send invoice via email endpoint
- [ ] Download invoice PDF endpoint

### Frontend Backoffice

- [ ] Order detail page with status update form
- [ ] Order history timeline display
- [ ] Invoice preview/print page
- [ ] Send invoice button
- [ ] Download invoice button

### Frontend Store

- [ ] Order tracking page (customer dapat lihat history)
- [ ] Invoice view page

---

## 5. Database Indexes (Recommended)

Untuk performance yang lebih baik:

```sql
-- Order status history
CREATE INDEX idx_order_status_history_order_id ON order_status_history(order_id);
CREATE INDEX idx_order_status_history_created_at ON order_status_history(created_at);

-- Orders
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_payment_status ON orders(payment_status);
CREATE INDEX idx_orders_customer_id ON orders(customer_id);
CREATE INDEX idx_orders_created_at ON orders(created_at);
```

---

## 6. Business Rules

### Status Update Rules

1. **pending → processing**
   - Required: Payment confirmation
   - Action: Set `paid_at` timestamp
   - Update: `payment_status` = 'paid'

2. **processing → shipped**
   - Required: Tracking number, courier
   - Action: Set `shipped_at` timestamp
   - Validation: All items must be packed

3. **shipped → completed**
   - Required: Customer confirmation OR auto after 7 days
   - Action: Set `completed_at` timestamp
   - Business logic: Cannot be cancelled after this

4. **any → cancelled**
   - Allowed: Before completed status
   - Required: Cancellation reason
   - Action: Set `cancelled_at` timestamp, restore stock
   - Business logic: Refund process if already paid

### Payment Status Rules

- `unpaid` → `paid`: When payment confirmed (pending → processing)
- `paid` → `refunded`: When order cancelled after payment

### Stock Management

- **Order created**: Reduce stock immediately
- **Order cancelled**: Restore stock
- **Order completed**: No action (stock already reduced)

---

## 7. Email Templates

### Invoice Email Template

**Subject:** Invoice untuk Order #{order_number}

**Body:**
```
Halo {customer_name},

Terima kasih atas pesanan Anda di Minimoda Store.

Order Details:
- Order Number: {order_number}
- Order Date: {order_date}
- Total Amount: Rp {total_amount}
- Status: {status}

Lihat invoice lengkap di attachment atau klik link berikut:
{invoice_url}

Jika Anda memiliki pertanyaan, silakan hubungi kami di:
Email: info@minimoda.id
Phone: 021-12345678

Terima kasih,
Minimoda Store
```

---

## 8. Frontend UI Recommendations

### Order History Timeline (Backoffice)

```
○ Completed
  2025-10-27 09:00
  by: Admin User

○ Shipped
  2025-10-26 14:00
  JNE REG - AWB: 1234567890
  by: Admin User

○ Processing
  2025-10-26 11:30
  Payment confirmed
  by: Admin User

● Pending
  2025-10-26 10:00
  Order created via WhatsApp checkout
  by: System
```

### Invoice Print Layout

```
┌─────────────────────────────────────────┐
│ MINIMODA STORE                          │
│ Jl. Example No. 123, Jakarta            │
│ Phone: 021-12345678                     │
│                                         │
│ INVOICE                                 │
│ No: ORD-20251026-0001                   │
│ Date: 26 Oct 2025                       │
├─────────────────────────────────────────┤
│ BILL TO:                                │
│ John Doe                                │
│ 081234567890                            │
│ Jl. Customer No. 456, Bandung           │
├─────────────────────────────────────────┤
│ ITEMS                                   │
│ ─────────────────────────────────────── │
│ Kemeja Formal (L, Blue)    2x  300,000  │
│                                         │
│ Subtotal:                     300,000   │
│ Shipping (JNE REG):            25,000   │
│ Total:                        325,000   │
├─────────────────────────────────────────┤
│ Payment: Transfer Bank                  │
│ Status: PAID                            │
│ Paid at: 26 Oct 2025 11:30              │
└─────────────────────────────────────────┘
```
