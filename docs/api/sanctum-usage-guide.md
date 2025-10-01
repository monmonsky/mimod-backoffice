# Laravel Sanctum Usage Guide - Settings API

Panduan lengkap cara menggunakan Laravel Sanctum untuk autentikasi Settings API.

---

## 📋 Daftar Isi

1. [Setup Sanctum](#setup-sanctum)
2. [Cara Generate Token](#cara-generate-token)
3. [Cara Menggunakan Token](#cara-menggunakan-token)
4. [Testing dengan Tools](#testing-dengan-tools)
5. [Troubleshooting](#troubleshooting)

---

## 🔧 Setup Sanctum

### 1. Migrasi Database

Pastikan tabel `personal_access_tokens` sudah ada:

```bash
cd /Users/rizafpermana/playground/mimod-backoffice
php artisan migrate
```

Jika belum, publish migration Sanctum:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 2. Verifikasi User Model

Model User sudah menggunakan trait `HasApiTokens`:

```php
// app/Models/User.php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}
```

### 3. Verifikasi Routes

**⚠️ Catatan Penting tentang Authentication:**

Aplikasi ini menggunakan **2 jenis authentication** yang berbeda:

1. **Custom Auth Token** (`auth.token` middleware) untuk Auth routes
2. **Laravel Sanctum** (`auth:sanctum` middleware) untuk Settings routes

```php
// routes/api.php

// Auth routes menggunakan custom auth.token middleware
Route::middleware('auth.token')->prefix('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('sessions', [AuthController::class, 'sessions']);
});

// Settings routes menggunakan Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('settings')->group(function () {
        // Settings API routes...
    });
});
```

**Konsekuensi:**
- Token dari `/api/auth/login` **tidak bisa** digunakan untuk `/api/settings/*`
- Anda perlu generate Sanctum token terpisah untuk Settings API

---

## 🔑 Cara Generate Token

### Method 1: Via Login API

**⚠️ Catatan Penting:**
- Endpoint `/api/auth/login` menggunakan custom auth system (bukan Sanctum)
- Untuk Settings API, Anda perlu generate token via Tinker atau manual
- Auth routes (`/api/auth/*`) menggunakan middleware `auth.token` (custom)
- Settings routes (`/api/settings/*`) menggunakan middleware `auth:sanctum`

**Login API (untuk custom auth):**
```bash
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

**Response akan return custom token**, bukan Sanctum token. Token ini hanya bisa digunakan untuk auth routes, tidak untuk settings routes.

---

### Method 2: Via Tinker dengan API User (Recommended)

**Ini adalah cara yang direkomendasikan** untuk generate Sanctum token untuk Settings API.

**Step 1: Jalankan Seeder untuk Create API User**

```bash
# Fresh migration + seed (untuk development)
php artisan migrate:fresh --seed

# Atau hanya run UserSeeder jika database sudah ada
php artisan db:seed --class=UserSeeder
```

Seeder akan membuat user dengan credentials:
- **Name:** API
- **Email:** api@mimod.com
- **Password:** api-secure-password-2024

**Step 2: Generate Sanctum Token**

```bash
php artisan tinker
```

```php
// Cari API user
$apiUser = App\Models\User::where('email', 'api@mimod.com')->first();

// Generate token untuk Settings API
$token = $apiUser->createToken('settings-api-token')->plainTextToken;

// Copy token ini untuk digunakan
echo $token;
// Output: 1|laravel_sanctum_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**⚠️ Catatan Penting:**
- User "API" **tidak bisa login via web** (form login akan reject)
- User ini **hanya untuk generate Sanctum token**
- Token yang di-generate bisa digunakan untuk semua Settings API endpoints

**Token Abilities (Optional):**

Bisa tambahkan abilities untuk membatasi akses:

```php
// Token dengan abilities terbatas (read-only)
$token = $apiUser->createToken('settings-readonly', ['settings:read'])->plainTextToken;

// Token dengan multiple abilities
$token = $apiUser->createToken('settings-full', [
    'settings:read',
    'settings:write'
])->plainTextToken;

// Token dengan akses penuh (default)
$token = $apiUser->createToken('settings-api', ['*'])->plainTextToken;
```

---

### Method 3: Create Endpoint untuk Generate Sanctum Token

Jika ingin generate token via API, buat endpoint baru terpisah dari auth login:

```php
// app/Http/Controllers/Api/AuthController.php

/**
 * Generate Sanctum token untuk Settings API
 * Endpoint ini terpisah dari login biasa
 */
public function generateSettingsToken(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    $user = Auth::user();

    // Generate Sanctum token
    $token = $user->createToken('settings-api-token')->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'Sanctum token generated successfully',
        'data' => [
            'user' => $user,
            'token' => $token,
            'token_type' => 'sanctum'
        ]
    ], 200);
}
```

Tambahkan route di `routes/api.php`:

```php
// Generate Sanctum token untuk Settings API
Route::post('auth/generate-settings-token', [AuthController::class, 'generateSettingsToken']);
```

**Request:**
```bash
curl -X POST "http://localhost:8000/api/auth/generate-settings-token" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

---

## 🚀 Cara Menggunakan Token

### 1. Menggunakan Token di HTTP Request

Kirim token di header `Authorization` dengan prefix `Bearer`:

```
Authorization: Bearer 1|laravel_sanctum_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 2. Contoh Request dengan cURL

**Get All General Settings:**
```bash
curl -X GET "http://localhost:8000/api/settings/general" \
  -H "Authorization: Bearer 1|laravel_sanctum_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json"
```

**Get Store Info:**
```bash
curl -X GET "http://localhost:8000/api/settings/general/store/info" \
  -H "Authorization: Bearer 1|laravel_sanctum_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json"
```

**Update Setting:**
```bash
curl -X PUT "http://localhost:8000/api/settings/general/store.info" \
  -H "Authorization: Bearer 1|laravel_sanctum_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "value": {
      "name": "My Store",
      "tagline": "Best Store Ever"
    }
  }'
```

---

### 3. Contoh Request dengan JavaScript (Fetch)

```javascript
const API_URL = 'http://localhost:8000/api';
const TOKEN = '1|laravel_sanctum_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

// GET request
async function getStoreInfo() {
  const response = await fetch(`${API_URL}/settings/general/store/info`, {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${TOKEN}`,
      'Accept': 'application/json'
    }
  });

  const data = await response.json();
  console.log(data);
}

// PUT request
async function updateStoreName(name, tagline) {
  const response = await fetch(`${API_URL}/settings/general/store.info`, {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${TOKEN}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      value: {
        name: name,
        tagline: tagline
      }
    })
  });

  const data = await response.json();
  console.log(data);
}

// Login untuk get token
async function login(email, password) {
  const response = await fetch(`${API_URL}/auth/login`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      email: email,
      password: password
    })
  });

  const data = await response.json();

  if (data.status) {
    // Simpan token di localStorage
    localStorage.setItem('api_token', data.data.token);
    return data.data.token;
  }

  throw new Error(data.message);
}

// Penggunaan
login('admin@example.com', 'password')
  .then(token => {
    console.log('Token:', token);
    getStoreInfo();
  });
```

---

### 4. Contoh Request dengan Axios

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// Interceptor untuk auto attach token
api.interceptors.request.use(config => {
  const token = localStorage.getItem('api_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Login
async function login(email, password) {
  try {
    const response = await api.post('/auth/login', {
      email,
      password
    });

    if (response.data.status) {
      localStorage.setItem('api_token', response.data.data.token);
      return response.data.data;
    }
  } catch (error) {
    console.error('Login failed:', error.response.data);
    throw error;
  }
}

// Get settings
async function getSettings() {
  try {
    const response = await api.get('/settings/general');
    return response.data.data;
  } catch (error) {
    console.error('Get settings failed:', error.response.data);
    throw error;
  }
}

// Update setting
async function updateSetting(key, value) {
  try {
    const response = await api.put(`/settings/general/${key}`, {
      value: value
    });
    return response.data.data;
  } catch (error) {
    console.error('Update failed:', error.response.data);
    throw error;
  }
}

// Logout
async function logout() {
  try {
    await api.post('/auth/logout');
    localStorage.removeItem('api_token');
  } catch (error) {
    console.error('Logout failed:', error.response.data);
    throw error;
  }
}
```

---

### 5. Contoh Request dengan PHP (Guzzle)

```php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ApiClient
{
    private $client;
    private $token;

    public function __construct($baseUrl, $token = null)
    {
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->token = $token;
    }

    // Login untuk get token
    public function login($email, $password)
    {
        try {
            $response = $this->client->post('/api/auth/login', [
                'json' => [
                    'email' => $email,
                    'password' => $password
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status']) {
                $this->token = $data['data']['token'];
                return $data['data'];
            }

            throw new Exception($data['message']);
        } catch (RequestException $e) {
            throw new Exception('Login failed: ' . $e->getMessage());
        }
    }

    // Get settings
    public function getSettings()
    {
        try {
            $response = $this->client->get('/api/settings/general', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['data'];
        } catch (RequestException $e) {
            throw new Exception('Get settings failed: ' . $e->getMessage());
        }
    }

    // Update setting
    public function updateSetting($key, $value)
    {
        try {
            $response = $this->client->put("/api/settings/general/{$key}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token
                ],
                'json' => [
                    'value' => $value
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['data'];
        } catch (RequestException $e) {
            throw new Exception('Update failed: ' . $e->getMessage());
        }
    }
}

// Penggunaan
$api = new ApiClient('http://localhost:8000');

// Login
$user = $api->login('admin@example.com', 'password');
echo "Token: " . $user['token'] . "\n";

// Get settings
$settings = $api->getSettings();
print_r($settings);

// Update setting
$updated = $api->updateSetting('store.info', [
    'name' => 'New Store Name',
    'tagline' => 'New Tagline'
]);
print_r($updated);
```

---

## 🧪 Testing dengan Tools

### 1. Postman

**Setup:**
1. Buka Postman
2. Create New Collection: "Settings API"
3. Add Environment Variable:
   - `base_url`: `http://localhost:8000/api`
   - `token`: (akan diisi setelah login)

**Login Request:**
- Method: `POST`
- URL: `{{base_url}}/auth/login`
- Headers:
  - `Content-Type`: `application/json`
  - `Accept`: `application/json`
- Body (raw JSON):
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```
- Tests (auto save token):
```javascript
if (pm.response.code === 200) {
    const data = pm.response.json();
    pm.environment.set("token", data.data.token);
}
```

**Get Settings Request:**
- Method: `GET`
- URL: `{{base_url}}/settings/general`
- Headers:
  - `Authorization`: `Bearer {{token}}`
  - `Accept`: `application/json`

**Update Setting Request:**
- Method: `PUT`
- URL: `{{base_url}}/settings/general/store.info`
- Headers:
  - `Authorization`: `Bearer {{token}}`
  - `Content-Type`: `application/json`
  - `Accept`: `application/json`
- Body (raw JSON):
```json
{
  "value": {
    "name": "My Store",
    "tagline": "Best Store"
  }
}
```

---

### 2. Insomnia

**Setup:**
1. Create New Request Collection
2. Add Environment:
```json
{
  "base_url": "http://localhost:8000/api",
  "token": ""
}
```

**Login Request:**
- Method: `POST`
- URL: `{{ _.base_url }}/auth/login`
- Body JSON:
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```
- Copy token dari response, paste ke environment variable `token`

**Get Settings Request:**
- Method: `GET`
- URL: `{{ _.base_url }}/settings/general`
- Auth: Bearer Token
- Token: `{{ _.token }}`

---

### 3. Thunder Client (VS Code Extension)

**Setup:**
1. Install Thunder Client extension
2. Create New Request
3. Add Environment Variable:
   - `base_url`: `http://localhost:8000/api`
   - `token`: (dari login response)

**Login:**
- Method: `POST`
- URL: `{{base_url}}/auth/login`
- Body: JSON
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

**Get Settings:**
- Method: `GET`
- URL: `{{base_url}}/settings/general`
- Auth: Bearer
- Token: `{{token}}`

---

## 🔐 Token Management

### Melihat Token User

```bash
php artisan tinker
```

```php
// Lihat semua token user
$user = App\Models\User::find(1);
$user->tokens;

// Lihat token terakhir
$user->tokens()->latest()->first();

// Hitung jumlah token
$user->tokens()->count();
```

### Revoke (Hapus) Token

**Method 1: Via API Logout**

```bash
curl -X POST "http://localhost:8000/api/auth/logout" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Method 2: Via Tinker**

```php
$user = App\Models\User::find(1);

// Hapus current token (yang sedang digunakan)
$user->currentAccessToken()->delete();

// Hapus semua token user
$user->tokens()->delete();

// Hapus token spesifik by ID
$user->tokens()->where('id', 123)->delete();
```

**Method 3: Programmatically di Controller**

```php
// Logout (hapus current token)
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'status' => true,
        'message' => 'Logged out successfully'
    ]);
}

// Logout all devices (hapus semua token)
public function logoutAll(Request $request)
{
    $request->user()->tokens()->delete();

    return response()->json([
        'status' => true,
        'message' => 'Logged out from all devices'
    ]);
}
```

---

## 🛡️ Token Expiration

### Set Expiration di Config

Edit `config/sanctum.php`:

```php
'expiration' => 60 * 24, // 24 jam (dalam menit)
// atau
'expiration' => null, // Tidak expire (default)
```

### Set Expiration per Token

```php
$token = $user->createToken('api-token', ['*'], now()->addHours(24));
```

### Check Token Expiration

```php
$user = $request->user();
$token = $user->currentAccessToken();

if ($token->expires_at && $token->expires_at->isPast()) {
    // Token expired
    $token->delete();
    return response()->json(['message' => 'Token expired'], 401);
}
```

---

## 🐛 Troubleshooting

### Error: "Unauthenticated"

**Penyebab:**
- Token tidak dikirim di header
- Token tidak valid atau sudah expired
- Format header salah

**Solusi:**
```bash
# Pastikan format header benar:
Authorization: Bearer TOKEN_ANDA

# Bukan:
Authorization: TOKEN_ANDA
Bearer TOKEN_ANDA
Token TOKEN_ANDA
```

---

### Error: "Token not found"

**Penyebab:**
- Tabel `personal_access_tokens` belum ada
- Token belum di-generate

**Solusi:**
```bash
# Jalankan migration
php artisan migrate

# Atau publish dan migrate
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

---

### Error: "Token expired"

**Penyebab:**
- Token sudah melewati expiration time

**Solusi:**
```bash
# Generate token baru dengan login
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

---

### Error: CORS

**Penyebab:**
- Frontend dari domain berbeda
- CORS belum dikonfigurasi

**Solusi:**

Edit `config/cors.php`:
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3000', 'http://127.0.0.1:3000'],
'allowed_headers' => ['*'],
'allowed_methods' => ['*'],
'supports_credentials' => true,
```

Edit `config/sanctum.php`:
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000'
)),
```

---

## 📚 Best Practices

### 1. Simpan Token dengan Aman

**❌ Jangan:**
- Hardcode token di code
- Simpan di Git
- Share token via chat/email

**✅ Lakukan:**
- Simpan di environment variable
- Simpan di secure storage (KeyChain, Keystore)
- Simpan di localStorage/sessionStorage (untuk web, dengan HTTPS)

### 2. Token Naming

```php
// ❌ Bad
$token = $user->createToken('token')->plainTextToken;

// ✅ Good - dengan nama yang jelas
$token = $user->createToken('mobile-app-' . $request->device_name)->plainTextToken;
$token = $user->createToken('web-dashboard')->plainTextToken;
$token = $user->createToken('api-integration-' . $clientName)->plainTextToken;
```

### 3. Token Abilities

```php
// Admin dengan full access
$token = $admin->createToken('admin-token', ['*'])->plainTextToken;

// Editor dengan limited access
$token = $editor->createToken('editor-token', [
    'settings:read',
    'settings:write'
])->plainTextToken;

// Viewer dengan read-only
$token = $viewer->createToken('viewer-token', [
    'settings:read'
])->plainTextToken;
```

Lalu di controller check abilities:

```php
if ($request->user()->tokenCan('settings:write')) {
    // Allow update
} else {
    return response()->json(['message' => 'Unauthorized'], 403);
}
```

### 4. Rate Limiting

Edit `app/Http/Kernel.php` atau `bootstrap/app.php`:

```php
// Add rate limiter
$middleware->throttleApi([
    'api' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1',
]);
```

Atau per route:

```php
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // API routes
});
```

---

## 📖 Resources

- [Laravel Sanctum Documentation](https://laravel.com/docs/11.x/sanctum)
- [API Settings Documentation](./settings-api.md)
- [Postman Collection](./postman-collection.json) (jika ada)

---

## 💡 Tips

1. **Development:** Set `'expiration' => null` untuk tidak perlu generate token terus-menerus
2. **Production:** Set `'expiration' => 60 * 24 * 7` (7 hari) dan implement refresh token
3. **Testing:** Generate token via Tinker untuk quick testing
4. **Security:** Gunakan HTTPS di production
5. **Monitoring:** Log semua API requests untuk audit trail

---

Selesai! Sekarang API Settings sudah dilindungi dengan Laravel Sanctum dan siap digunakan. 🚀
