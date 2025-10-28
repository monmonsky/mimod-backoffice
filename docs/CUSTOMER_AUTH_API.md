# Customer Authentication API Documentation

API untuk autentikasi dan manajemen profil customer (pelanggan e-commerce).

## Base URL
```
http://api-local.minimoda.id/api/customer
```

## Authentication
Gunakan Bearer Token di header:
```
Authorization: Bearer {token}
```

---

## 📝 Table of Contents

1. [Authentication Endpoints](#authentication-endpoints)
2. [Profile Management](#profile-management)
3. [Address Management](#address-management)
4. [Loyalty Points](#loyalty-points)
5. [Dashboard](#dashboard)
6. [Orders](#orders)
7. [Error Responses](#error-responses)

---

## Authentication Endpoints

### 1. Register Customer

**Endpoint:** `POST /auth/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "08123456789",
    "password": "password123",
    "password_confirmation": "password123",
    "date_of_birth": "1990-01-01",
    "gender": "male"
}
```

**Response (201):**
```json
{
    "status": true,
    "statusCode": "201",
    "message": "Registration successful. Please verify your email.",
    "data": {
        "token": "abc123...",
        "token_type": "Bearer",
        "customer": {
            "id": 1,
            "customer_code": "CUST000001",
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "08123456789",
            "email_verified_at": null,
            "status": "active"
        }
    }
}
```

---

### 2. Login

**Endpoint:** `POST /auth/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Login successful.",
    "data": {
        "token": "abc123...",
        "token_type": "Bearer",
        "customer": {
            "id": 1,
            "customer_code": "CUST000001",
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "08123456789",
            "date_of_birth": "1990-01-01",
            "gender": "male",
            "segment": "regular",
            "is_vip": false,
            "loyalty_points": 500,
            "total_orders": 10,
            "total_spent": "5000000.00",
            "email_verified_at": "2024-01-01 00:00:00",
            "status": "active"
        }
    }
}
```

---

### 3. Logout

**Endpoint:** `POST /auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Logout successful.",
    "data": []
}
```

---

### 4. Logout from All Devices

**Endpoint:** `POST /auth/logout-all`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Logged out from all devices successfully.",
    "data": []
}
```

---

### 5. Get Current Customer

**Endpoint:** `GET /auth/me`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Customer data retrieved successfully.",
    "data": {
        "id": 1,
        "customer_code": "CUST000001",
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "08123456789",
        "date_of_birth": "1990-01-01",
        "gender": "male",
        "segment": "regular",
        "is_vip": false,
        "loyalty_points": 500,
        "total_orders": 10,
        "total_spent": "5000000.00",
        "average_order_value": "500000.00",
        "last_order_at": "2024-01-01 00:00:00",
        "last_login_at": "2024-01-01 00:00:00",
        "email_verified_at": "2024-01-01 00:00:00",
        "status": "active"
    }
}
```

---

### 6. Refresh Token

**Endpoint:** `POST /auth/refresh`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Token refreshed successfully.",
    "data": {
        "token": "new_token_123...",
        "token_type": "Bearer"
    }
}
```

---

### 7. Forgot Password

**Endpoint:** `POST /auth/forgot-password`

**Request Body:**
```json
{
    "email": "john@example.com"
}
```

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Password reset code sent to your email.",
    "data": []
}
```

---

### 8. Reset Password

**Endpoint:** `POST /auth/reset-password`

**Request Body:**
```json
{
    "email": "john@example.com",
    "code": "123456",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Password reset successfully. Please login with your new password.",
    "data": []
}
```

---

### 9. Verify Email

**Endpoint:** `POST /auth/verify-email`

**Request Body:**
```json
{
    "email": "john@example.com",
    "code": "123456"
}
```

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Email verified successfully.",
    "data": []
}
```

---

### 10. Resend Verification Email

**Endpoint:** `POST /auth/resend-verification`

**Request Body:**
```json
{
    "email": "john@example.com"
}
```

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Verification email sent successfully.",
    "data": []
}
```

---

## Profile Management

### 1. Get Profile

**Endpoint:** `GET /profile`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Profile retrieved successfully.",
    "data": {
        "id": 1,
        "customer_code": "CUST000001",
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "08123456789",
        "date_of_birth": "1990-01-01",
        "gender": "male",
        "segment": "regular",
        "is_vip": false,
        "loyalty_points": 500,
        "total_orders": 10,
        "total_spent": "5000000.00",
        "average_order_value": "500000.00",
        "last_order_at": "2024-01-01 00:00:00",
        "last_login_at": "2024-01-01 00:00:00",
        "email_verified_at": "2024-01-01 00:00:00",
        "preferences": {
            "newsletter": true,
            "sms_notifications": false
        },
        "status": "active"
    }
}
```

---

### 2. Update Profile

**Endpoint:** `PUT /profile`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "phone": "08123456789",
    "date_of_birth": "1990-01-01",
    "gender": "male",
    "preferences": {
        "newsletter": true,
        "sms_notifications": false
    }
}
```

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Profile updated successfully.",
    "data": {
        "id": 1,
        "customer_code": "CUST000001",
        "name": "John Doe Updated",
        "email": "john@example.com",
        "phone": "08123456789",
        "date_of_birth": "1990-01-01",
        "gender": "male",
        "preferences": {
            "newsletter": true,
            "sms_notifications": false
        }
    }
}
```

---

### 3. Change Password

**Endpoint:** `PUT /profile/change-password`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "current_password": "oldpassword123",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Password changed successfully.",
    "data": []
}
```

---

### 4. Upload Avatar

**Endpoint:** `POST /profile/upload-avatar`

**Headers:** `Authorization: Bearer {token}`

**Content-Type:** `multipart/form-data`

**Request Body:**
```
avatar: [file] (max 2MB, jpg/png)
```

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Avatar uploaded successfully.",
    "data": {
        "avatar_url": "http://ftp.example.com/customers/avatars/customer_1_123456.jpg"
    }
}
```

---

### 5. Delete Avatar

**Endpoint:** `DELETE /profile/avatar`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Avatar deleted successfully.",
    "data": []
}
```

---

### 6. Delete Account

**Endpoint:** `DELETE /profile/account`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "password": "currentpassword123"
}
```

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Account deleted successfully.",
    "data": []
}
```

---

## Address Management

### 1. Get All Addresses

**Endpoint:** `GET /addresses`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Addresses retrieved successfully.",
    "data": {
        "addresses": [
            {
                "id": 1,
                "customer_id": 1,
                "label": "Rumah",
                "recipient_name": "John Doe",
                "phone": "08123456789",
                "address": "Jl. Contoh No. 123",
                "city": "Jakarta",
                "province": "DKI Jakarta",
                "postal_code": "12345",
                "is_default": true,
                "created_at": "2024-01-01 00:00:00",
                "updated_at": "2024-01-01 00:00:00"
            }
        ],
        "total": 1
    }
}
```

---

### 2. Get Single Address

**Endpoint:** `GET /addresses/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Address retrieved successfully.",
    "data": {
        "id": 1,
        "customer_id": 1,
        "label": "Rumah",
        "recipient_name": "John Doe",
        "phone": "08123456789",
        "address": "Jl. Contoh No. 123",
        "city": "Jakarta",
        "province": "DKI Jakarta",
        "postal_code": "12345",
        "is_default": true,
        "created_at": "2024-01-01 00:00:00",
        "updated_at": "2024-01-01 00:00:00"
    }
}
```

---

### 3. Create Address

**Endpoint:** `POST /addresses`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "label": "Kantor",
    "recipient_name": "John Doe",
    "phone": "08123456789",
    "address": "Jl. Sudirman No. 456",
    "city": "Jakarta",
    "province": "DKI Jakarta",
    "postal_code": "12345",
    "is_default": false
}
```

**Response (201):**
```json
{
    "status": true,
    "statusCode": "201",
    "message": "Address created successfully.",
    "data": {
        "id": 2,
        "customer_id": 1,
        "label": "Kantor",
        "recipient_name": "John Doe",
        "phone": "08123456789",
        "address": "Jl. Sudirman No. 456",
        "city": "Jakarta",
        "province": "DKI Jakarta",
        "postal_code": "12345",
        "is_default": false,
        "created_at": "2024-01-01 00:00:00",
        "updated_at": "2024-01-01 00:00:00"
    }
}
```

---

### 4. Update Address

**Endpoint:** `PUT /addresses/{id}`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "label": "Rumah (Updated)",
    "recipient_name": "John Doe",
    "phone": "08123456789",
    "address": "Jl. Contoh No. 123 Updated",
    "city": "Jakarta",
    "province": "DKI Jakarta",
    "postal_code": "12345"
}
```

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Address updated successfully.",
    "data": {
        "id": 1,
        "customer_id": 1,
        "label": "Rumah (Updated)",
        "recipient_name": "John Doe",
        "phone": "08123456789",
        "address": "Jl. Contoh No. 123 Updated",
        "city": "Jakarta",
        "province": "DKI Jakarta",
        "postal_code": "12345",
        "is_default": true,
        "created_at": "2024-01-01 00:00:00",
        "updated_at": "2024-01-01 00:00:00"
    }
}
```

---

### 5. Delete Address

**Endpoint:** `DELETE /addresses/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Address deleted successfully.",
    "data": []
}
```

---

### 6. Set Default Address

**Endpoint:** `POST /addresses/{id}/set-default`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Default address updated successfully.",
    "data": []
}
```

---

## Loyalty Points

### 1. Get Loyalty Points

**Endpoint:** `GET /loyalty/points`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Loyalty points retrieved successfully.",
    "data": {
        "loyalty_points": 500,
        "customer_code": "CUST000001"
    }
}
```

---

### 2. Get Loyalty History

**Endpoint:** `GET /loyalty/history?limit=20`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Loyalty history retrieved successfully.",
    "data": {
        "current_points": 500,
        "history": [
            {
                "id": 1,
                "customer_id": 1,
                "points": 100,
                "type": "earned",
                "description": "Purchase order #12345",
                "order_id": 12345,
                "created_at": "2024-01-01 00:00:00"
            },
            {
                "id": 2,
                "customer_id": 1,
                "points": -50,
                "type": "redeemed",
                "description": "Redeemed for discount",
                "order_id": null,
                "created_at": "2024-01-02 00:00:00"
            }
        ],
        "total_records": 2
    }
}
```

---

## Dashboard

### 1. Get Dashboard Overview

**Endpoint:** `GET /dashboard`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Dashboard data retrieved successfully.",
    "data": {
        "stats": {
            "total_orders": 10,
            "total_spent": "5000000.00",
            "average_order_value": "500000.00",
            "loyalty_points": 500,
            "last_order_at": "2024-01-01 00:00:00",
            "pending_orders": 2,
            "processing_orders": 3,
            "shipped_orders": 1,
            "wishlist_count": 5,
            "addresses_count": 2
        },
        "recent_orders": [
            {
                "id": 1,
                "order_number": "ORD-20240101-000001",
                "status": "delivered",
                "total": "500000.00",
                "items_count": 3,
                "first_item": {
                    "name": "Product Name",
                    "slug": "product-slug",
                    "quantity": 2
                },
                "created_at": "2024-01-01 00:00:00"
            }
        ],
        "spending_by_month": [
            {
                "month": "2024-01",
                "total_spent": "1000000.00",
                "order_count": 2
            }
        ],
        "favorite_products": [
            {
                "id": 1,
                "name": "Product Name",
                "slug": "product-slug",
                "total_ordered": 10,
                "order_count": 3,
                "primary_image": {
                    "url": "http://...",
                    "alt_text": "Product image"
                }
            }
        ]
    }
}
```

---

### 2. Get Order Statistics

**Endpoint:** `GET /dashboard/order-stats?period=all`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `period`: `all`, `year`, `month`, `week`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Order statistics retrieved successfully.",
    "data": {
        "period": "all",
        "total_orders": 10,
        "total_spent": "5000000.00",
        "average_order_value": "500000.00",
        "orders_by_status": {
            "pending": 2,
            "processing": 3,
            "shipped": 1,
            "delivered": 4,
            "cancelled": 0
        }
    }
}
```

---

### 3. Get Activities

**Endpoint:** `GET /dashboard/activities?limit=20`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Activities retrieved successfully.",
    "data": {
        "activities": [
            {
                "type": "order",
                "action": "created",
                "description": "Order ORD-20240101-000001 created",
                "status": "pending",
                "amount": "500000.00",
                "created_at": "2024-01-01 00:00:00"
            },
            {
                "type": "loyalty",
                "action": "earned",
                "description": "Earned from order ORD-20240101-000001",
                "points": 50,
                "created_at": "2024-01-01 00:00:00"
            }
        ],
        "total": 20
    }
}
```

---

### 4. Get Notifications

**Endpoint:** `GET /dashboard/notifications?limit=10`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Notifications retrieved successfully.",
    "data": {
        "notifications": [
            {
                "type": "order_update",
                "title": "Order Status Updated",
                "message": "Your order ORD-20240101-000001 is now shipped",
                "data": {
                    "order_number": "ORD-20240101-000001",
                    "status": "shipped"
                },
                "read": false,
                "created_at": "2024-01-01 00:00:00"
            }
        ],
        "unread_count": 5,
        "total": 10
    }
}
```

---

## Orders

### 1. Get All Orders

**Endpoint:** `GET /orders?page=1&per_page=10&status=pending`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10)
- `status`: Filter by status (optional): `pending`, `processing`, `shipped`, `delivered`, `cancelled`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Orders retrieved successfully.",
    "data": {
        "orders": [
            {
                "id": 1,
                "order_number": "ORD-20240101-000001",
                "customer_id": 1,
                "status": "pending",
                "payment_status": "pending",
                "payment_method": "bank_transfer",
                "subtotal": "450000.00",
                "shipping_cost": "50000.00",
                "discount": "0.00",
                "total": "500000.00",
                "created_at": "2024-01-01 00:00:00",
                "items": [
                    {
                        "id": 1,
                        "product_id": 1,
                        "variant_id": 1,
                        "quantity": 2,
                        "price": "225000.00",
                        "total": "450000.00",
                        "product": {
                            "id": 1,
                            "name": "Product Name",
                            "slug": "product-slug"
                        },
                        "variant": {
                            "id": 1,
                            "sku": "SKU-001",
                            "size": "M",
                            "color": "Red"
                        }
                    }
                ]
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 10,
            "total": 100,
            "last_page": 10
        }
    }
}
```

---

### 2. Get Order Detail

**Endpoint:** `GET /orders/{id}`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Order retrieved successfully.",
    "data": {
        "id": 1,
        "order_number": "ORD-20240101-000001",
        "customer_id": 1,
        "status": "pending",
        "payment_status": "pending",
        "payment_method": "bank_transfer",
        "subtotal": "450000.00",
        "shipping_cost": "50000.00",
        "discount": "0.00",
        "total": "500000.00",
        "notes": "Customer notes",
        "created_at": "2024-01-01 00:00:00",
        "items": [
            {
                "id": 1,
                "product_id": 1,
                "variant_id": 1,
                "quantity": 2,
                "price": "225000.00",
                "total": "450000.00",
                "product": {
                    "id": 1,
                    "name": "Product Name",
                    "slug": "product-slug",
                    "description": "Product description"
                },
                "variant": {
                    "id": 1,
                    "sku": "SKU-001",
                    "size": "M",
                    "color": "Red",
                    "images": [
                        {
                            "url": "http://...",
                            "is_primary": true
                        }
                    ]
                }
            }
        ],
        "shipping_address": {
            "id": 1,
            "label": "Rumah",
            "recipient_name": "John Doe",
            "phone": "08123456789",
            "address": "Jl. Contoh No. 123",
            "city": "Jakarta",
            "province": "DKI Jakarta",
            "postal_code": "12345"
        },
        "status_history": [
            {
                "id": 1,
                "order_id": 1,
                "status": "pending",
                "notes": "Order created",
                "created_at": "2024-01-01 00:00:00"
            }
        ]
    }
}
```

---

### 3. Create Order (Checkout)

**Endpoint:** `POST /orders`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
    "items": [
        {
            "variant_id": 1,
            "quantity": 2
        },
        {
            "variant_id": 2,
            "quantity": 1
        }
    ],
    "shipping_address_id": 1,
    "payment_method": "bank_transfer",
    "shipping_method": "JNE REG",
    "shipping_cost": 50000,
    "notes": "Please pack carefully",
    "coupon_code": "DISCOUNT10"
}
```

**Response (201):**
```json
{
    "status": true,
    "statusCode": "201",
    "message": "Order created successfully.",
    "data": {
        "id": 1,
        "order_number": "ORD-20240101-000001",
        "customer_id": 1,
        "status": "pending",
        "payment_status": "pending",
        "payment_method": "bank_transfer",
        "subtotal": "450000.00",
        "shipping_cost": "50000.00",
        "discount": "50000.00",
        "total": "450000.00",
        "created_at": "2024-01-01 00:00:00",
        "items": [...]
    }
}
```

---

### 4. Cancel Order

**Endpoint:** `POST /orders/{id}/cancel`

**Headers:** `Authorization: Bearer {token}`

**Note:** Only pending orders can be cancelled by customer

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Order cancelled successfully.",
    "data": []
}
```

---

### 5. Track Order

**Endpoint:** `GET /orders/track/{orderNumber}`

**Headers:** `Authorization: Bearer {token}`

**Response (200):**
```json
{
    "status": true,
    "statusCode": "200",
    "message": "Order tracking retrieved successfully.",
    "data": {
        "order_number": "ORD-20240101-000001",
        "status": "shipped",
        "created_at": "2024-01-01 00:00:00",
        "tracking_history": [
            {
                "id": 1,
                "order_id": 1,
                "status": "pending",
                "notes": "Order created",
                "created_at": "2024-01-01 00:00:00"
            },
            {
                "id": 2,
                "order_id": 1,
                "status": "processing",
                "notes": "Order is being processed",
                "created_at": "2024-01-01 10:00:00"
            },
            {
                "id": 3,
                "order_id": 1,
                "status": "shipped",
                "notes": "Order has been shipped",
                "created_at": "2024-01-02 00:00:00"
            }
        ]
    }
}
```

---

## Error Responses

### Validation Error (422)
```json
{
    "status": false,
    "statusCode": "422",
    "message": "Validation failed",
    "data": {
        "errors": {
            "email": [
                "The email field is required."
            ],
            "password": [
                "The password field is required."
            ]
        }
    }
}
```

### Unauthorized (401)
```json
{
    "status": false,
    "statusCode": "401",
    "message": "Unauthorized. Invalid or expired token.",
    "data": []
}
```

### Forbidden (403)
```json
{
    "status": false,
    "statusCode": "403",
    "message": "Your account has been blocked.",
    "data": []
}
```

### Not Found (404)
```json
{
    "status": false,
    "statusCode": "404",
    "message": "Customer not found.",
    "data": []
}
```

### Rate Limit (429)
```json
{
    "status": false,
    "statusCode": "429",
    "message": "Too many login attempts. Please try again later.",
    "data": []
}
```

### Server Error (500)
```json
{
    "status": false,
    "statusCode": "500",
    "message": "Internal server error",
    "data": []
}
```

---

## Notes

1. **Rate Limiting:**
   - Login: Max 10 attempts per minute per IP
   - Registration: Max 5 attempts per hour per IP

2. **Token Expiration:**
   - Customer tokens expire after 30 days
   - Use refresh endpoint to get a new token

3. **OTP Expiration:**
   - OTP codes expire after 10 minutes

4. **Password Requirements:**
   - Minimum 8 characters

5. **Avatar Upload:**
   - Max file size: 2MB
   - Allowed formats: JPG, PNG

---

## Testing

### Using cURL

**Register:**
```bash
curl -X POST http://api-local.minimoda.id/api/customer/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Login:**
```bash
curl -X POST http://api-local.minimoda.id/api/customer/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Get Profile:**
```bash
curl -X GET http://api-local.minimoda.id/api/customer/profile \
  -H "Authorization: Bearer YOUR_TOKEN"
```
