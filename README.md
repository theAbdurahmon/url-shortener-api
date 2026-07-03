<div align="center">

# 🔗 URL Shortener

**A high-performance Laravel URL shortener with analytics, Redis caching, and async click tracking.**

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-17-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-latest-DC382D?style=for-the-badge&logo=redis&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)

</div>

---

## ✨ Features

- 🔗 Create short links with custom or auto-generated slugs (nanoid)
- 🛡️ Blacklist protection for reserved route words
- 🔒 Password-protected links with bcrypt hashing
- ⏳ Link expiration (`expires_at`) and click limits (`click_limit`)
- ⚡ Redis caching for high-performance redirects
- 📊 Async click tracking via queued jobs
- 🌍 Geolocation and device/browser detection per click
- 📈 Analytics: total/unique clicks, daily timeline, top countries, top devices
- 🏗️ Clean architecture: Repository + Service + Controller layers

---

## 🏛️ Architecture

```
Request → Controller → Service → Repository → Database
                                      ↓
                                    Redis Cache
```

| Layer | Responsibility |
|---|---|
| `Controller` | HTTP request/response only |
| `Service` | Business logic, validation, orchestration |
| `Repository` | All Eloquent queries and Redis cache writes |
| `Job` | Async background work (click recording) |
| `SlugGenerator`, `GeoLocation`, `UserAgent` | Single-purpose utilities |

---

## 🛠️ Tech Stack

| Tool | Version |
|---|---|
| Laravel | 13 |
| PHP | 8.4 |
| PostgreSQL | 17 |
| Redis | latest |
| Nginx | alpine |
| nanoid | hidehalo/nanoid-php |

---

## 🚀 Quick Start with Docker

### Prerequisites

Make sure you have installed:
- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### 1️⃣ Clone the repository

```bash
git clone <your-repo-url>
cd url-shortener
```

### 2️⃣ Configure environment

```bash
cp .env.example .env
```

Open `.env` and set the following:

```env
APP_NAME="URL Shortener"
APP_ENV=local
APP_KEY=         # will be generated in step 4
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=url_shortener      # your database name
DB_USERNAME=your_db_username   # your PostgreSQL username
DB_PASSWORD=your_db_password   # your PostgreSQL password

CACHE_STORE=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

> ⚠️ Note: `DB_HOST=db` and `REDIS_HOST=redis` are Docker service names — do not change them to `127.0.0.1`.

### 3️⃣ Start all containers

```bash
docker compose up -d --build
```

This will start 4 containers:
- `app-url-shortener` — PHP 8.4 FPM
- `nginx-url-shortener` — Nginx web server (port 8000)
- `db-url-shortener` — PostgreSQL
- `redis-for-url-shortener` — Redis

### 4️⃣ Set up the application

```bash
# Generate app key
docker compose exec app php artisan key:generate

# Run database migrations
docker compose exec app php artisan migrate

# Fix storage permissions
docker compose exec app chmod -R 777 storage bootstrap/cache
```

### 5️⃣ Verify everything works

```bash
docker compose ps
```

All containers should show `running`. Then open:

```
http://localhost:8000
```

Or test the API:

```bash
curl -X POST http://localhost:8000/api/links \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"original_url": "https://github.com"}'
```

---

## 📡 API Endpoints

### 🔐 Authentication

Authentication is handled via **Laravel Sanctum** (token-based). All `/api/links` endpoints require a Bearer token.

| Method | Endpoint | Description | Auth |
|---|---|---|---|
| `POST` | `/api/register` | Register a new user | ❌ Public |
| `POST` | `/api/login` | Login and get token | ❌ Public |
| `POST` | `/api/logout` | Logout and revoke token | ✅ Required |

**Register:**
```http
POST /api/register
Content-Type: application/json
Accept: application/json

{
  "name": "John",
  "email": "john@example.com",
  "password": "password",
}
```

**Login:**
```http
POST /api/login
Content-Type: application/json
Accept: application/json

{
  "email": "john@example.com",
  "password": "password"
}
```

Response:
```json
{
  "email": "john@exmaple.com",
  ....
  "token": "1|abc123xyz..."
}
```

Use the token in all protected requests:
```http
Authorization: Bearer 1|abc123xyz...
```

---

### 🔗 Links
> 🔒 All endpoints require authentication

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/links` | Create a new short link |
| `GET` | `/api/links` | List all your links |
| `GET` | `/api/links/{slug}` | Get a single link |
| `PUT` | `/api/links/{slug}` | Update a link |
| `DELETE` | `/api/links/{slug}` | Delete a link |

**Create a link:**
```http
POST /api/links
Authorization: Bearer your-token
Content-Type: application/json

{
  "original_url": "https://laravel.com/docs", (required)
  "title": "Laravel Docs", 
  "slug": "laravel",
  "password": "secret",
  "expires_at": "2026-12-31",
  "click_limit": 1000
}
```

---

### 🚦 Redirect
> 🌐 Public — no authentication required

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/{slug}` | Redirect to original URL |
| `POST` | `/{slug}/unlock` | Unlock password-protected link |
| `GET` | `/preview/{slug}` | Preview link without redirecting |

---

### 📊 Analytics
> 🔒 Requires authentication

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/links/{id}/stats` | Total/unique clicks, today/week counts |
| `GET` | `/api/links/{id}/stats/timeline` | Daily clicks for a date range |

**Get timeline:**
```http
GET /api/links/1/stats/timeline?from=2026-06-01&to=2026-06-30
Authorization: Bearer your-token
```

```json
{
  "data": [
    { 
        "date": "2026-06-20", 
        "clicks": 5,
        "unique_clicks": 2,
        "top_country": "UZ",
        "top_device": "mobile" 
    },
    { 
        "date": "2026-06-21", 
        "clicks": 3,
        "unique_clicks": 2,
        "top_country": "UZ",
        "top_device": "mobile"
    }
  ],
}
```

---

## ⚙️ How It Works

### Redirect Flow

```
GET /{slug}
    ↓
Check Redis cache (link:{slug})
    ↓ miss
Load from PostgreSQL → Cache for 24h
    ↓
Validate: is_active, expires_at, click_limit, password
    ↓
Dispatch RecordClick Job (async)
    ↓
302 Redirect → original_url
```

### Click Recording (async)

```
RecordClick Job
    ↓
GeoLocation lookup (IP → country, city)
UserAgent parse (browser, OS, device)
    ↓
DB Transaction:
  INSERT into clicks
  INCREMENT links.clicks_count
```

---

## 🧪 Testing

### Setup test environment

Create a `phpunit.xml` or `.env.testing` with a separate test database:

```xml
<env name="DB_CONNECTION" value="pgsql"/>
<env name="DB_DATABASE" value="url_shortener_test"/>
<env name="CACHE_STORE" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
```

### Run tests

```bash
# All tests
docker compose exec app php artisan test

# Specific test file
docker compose exec app php artisan test tests/Feature/Repositories/ClickRepositoryTest.php
docker compose exec app php artisan test tests/Unit/Services/RedirectServiceTest.php
docker compose exec app php artisan test tests/Unit/Jobs/RecordClickJobTest.php
```

The test suite covers:
- ✅ Link CRUD and slug blacklist validation
- ✅ Redirect: active/inactive, expired, password-protected, click-limit
- ✅ Click recording with graceful geolocation failure handling
- ✅ Analytics aggregation: timeline, top country/device

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── LinkController.php
│   │   ├── RedirectController.php
│   │   └── AnalyticsController.php
│   └── Requests/
│       ├── StoreLinkRequest.php
│       ├── UpdateLinkRequest.php
│       ├── UnlockLinkRequest.php
│       └── TimelineRequest.php
├── Services/
│   ├── LinkService.php
│   ├── RedirectService.php
│   ├── SlugGenerator.php
│   ├── GeoLocation.php
│   └── UserAgent.php
├── Repositories/
│   ├── LinkRepository.php
│   └── ClickRepository.php
├── Jobs/
│   └── RecordClick.php
├── Models/
│   ├── Link.php
│   └── Click.php
└── Exceptions/
    ├── InvalidSlugException.php
    ├── LinkInactiveException.php
    ├── LinkExpiredException.php
    ├── InvalidPasswordException.php
    └── RequiredPasswordLinkException.php
docker/
└── Dockerfile
nginx/
└── nginx.conf
docker-compose.yml
```