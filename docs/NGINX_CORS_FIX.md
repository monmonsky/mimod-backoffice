# Fix CORS Error - Nginx Configuration

## Problem
Error dari client:
```
Access to fetch at 'https://api-dev.minimoda.id/api/auth/login' from origin 'https://stg-backend-vue.minimoda.id'
has been blocked by CORS policy: Response to preflight request doesn't pass access control check:
No 'Access-Control-Allow-Origin' header is present on the requested resource.
```

## Root Cause
File `/etc/nginx/sites-enabled/api-dev.minimoda.id` menggunakan variabel `$cors_origin` tetapi variabel ini belum didefinisikan di nginx.conf.

## Solution

### Step 1: Edit `/etc/nginx/nginx.conf`

Tambahkan CORS map di dalam block `http {` setelah baris 12, sebelum `# Basic Settings`:

```nginx
http {

	##
	# CORS Origin Mapping
	##
	map $http_origin $cors_origin {
		default "";
		"~^https://stg-backend-vue\.minimoda\.id$" $http_origin;
		"~^https://backend-vue\.minimoda\.id$" $http_origin;
		"~^https://www\.minimoda\.id$" $http_origin;
		"~^https://minimoda\.id$" $http_origin;
		"~^http://localhost:3000$" $http_origin;
		"~^http://localhost:5173$" $http_origin;
		"~^http://localhost:8080$" $http_origin;
	}

	##
	# Basic Settings
	##

	# ... rest of config
}
```

### Step 2: Test Nginx Configuration

```bash
sudo nginx -t
```

Expected output:
```
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
```

### Step 3: Reload Nginx

```bash
sudo systemctl reload nginx
```

### Step 4: Verify CORS Headers

Test dengan curl:

```bash
curl -I -X OPTIONS https://api-dev.minimoda.id/api/auth/login \
  -H "Origin: https://stg-backend-vue.minimoda.id" \
  -H "Access-Control-Request-Method: POST" \
  -H "Access-Control-Request-Headers: Content-Type, Authorization"
```

Expected headers:
```
HTTP/2 204
access-control-allow-origin: https://stg-backend-vue.minimoda.id
access-control-allow-credentials: true
access-control-allow-methods: GET, POST, PUT, PATCH, DELETE, OPTIONS
access-control-allow-headers: Authorization, Content-Type, X-Requested-With
access-control-max-age: 86400
```

## Commands to Execute

```bash
# 1. Backup current nginx.conf
sudo cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.backup

# 2. Edit nginx.conf
sudo nano /etc/nginx/nginx.conf

# Tambahkan CORS map setelah baris "http {" (lihat Step 1 di atas)

# 3. Test configuration
sudo nginx -t

# 4. Reload nginx
sudo systemctl reload nginx

# 5. Check nginx status
sudo systemctl status nginx

# 6. Test CORS headers
curl -I -X OPTIONS https://api-dev.minimoda.id/api/auth/login \
  -H "Origin: https://stg-backend-vue.minimoda.id" \
  -H "Access-Control-Request-Method: POST"
```

## Alternative: Using Include File

Jika ingin lebih modular, bisa buat file terpisah:

### 1. Create CORS config file:

```bash
sudo nano /etc/nginx/conf.d/cors-mapping.conf
```

Content:
```nginx
# CORS Origin Mapping
map $http_origin $cors_origin {
	default "";
	"~^https://stg-backend-vue\.minimoda\.id$" $http_origin;
	"~^https://backend-vue\.minimoda\.id$" $http_origin;
	"~^https://www\.minimoda\.id$" $http_origin;
	"~^https://minimoda\.id$" $http_origin;
	"~^http://localhost:3000$" $http_origin;
	"~^http://localhost:5173$" $http_origin;
	"~^http://localhost:8080$" $http_origin;
}
```

### 2. Test and reload:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

File di `/etc/nginx/conf.d/` otomatis di-include karena ada baris ini di nginx.conf:
```nginx
include /etc/nginx/conf.d/*.conf;
```

## How the CORS Map Works

```nginx
map $http_origin $cors_origin {
	default "";  # Jika origin tidak cocok, set ke empty string
	"~^https://stg-backend-vue\.minimoda\.id$" $http_origin;  # Jika cocok, set ke nilai $http_origin
}
```

- `$http_origin`: Variable dari header `Origin` request
- `$cors_origin`: Variable yang akan digunakan di `add_header Access-Control-Allow-Origin`
- `~^....$`: Regex pattern untuk match origin
- `\.`: Escape dot di domain name

## Adding More Origins

Untuk menambah origin baru:

```nginx
map $http_origin $cors_origin {
	default "";
	"~^https://stg-backend-vue\.minimoda\.id$" $http_origin;
	"~^https://backend-vue\.minimoda\.id$" $http_origin;
	"~^https://new-frontend\.example\.com$" $http_origin;  # Tambahkan di sini
}
```

## Security Notes

1. **Development only**: `localhost` origins hanya untuk development
2. **Specific domains**: Gunakan regex spesifik, jangan `.*` yang allow semua
3. **HTTPS only**: Production sebaiknya hanya https origins
4. **Credentials**: `Access-Control-Allow-Credentials: true` hanya untuk trusted origins

## Troubleshooting

### If CORS still not working:

1. **Check nginx error log:**
   ```bash
   sudo tail -f /var/log/nginx/error.log
   ```

2. **Check if map is loaded:**
   ```bash
   sudo nginx -T | grep -A 10 "map \$http_origin"
   ```

3. **Check response headers:**
   ```bash
   curl -v -X OPTIONS https://api-dev.minimoda.id/api/auth/login \
     -H "Origin: https://stg-backend-vue.minimoda.id"
   ```

4. **Clear browser cache** atau test di incognito mode

### Common Issues:

1. **Syntax error di regex**: Pastikan escape `\.` untuk dot
2. **Missing semicolon**: Setiap line dalam map harus diakhiri `;`
3. **Map outside http block**: Map harus di dalam `http { ... }`
4. **Nginx not reloaded**: Harus reload setelah edit config

## References

- [Nginx map module](http://nginx.org/en/docs/http/ngx_http_map_module.html)
- [CORS specification](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)
- [Nginx CORS configuration](https://enable-cors.org/server_nginx.html)
