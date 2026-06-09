# WhoisScope

A Laravel 13 WHOIS lookup platform with a Vue.js frontend. Use it as a **REST API** or a **full-stack web application**. Built with Domain Driven Design (DDD).

**API Documentation:** [http://localhost:8000/docs](http://localhost:8000/docs) (when running locally)

## Languages

The web UI supports **7 languages** (default: English):

| Code | Language |
|------|----------|
| `en` | English (default) |
| `es` | Español |
| `zh` | 中文 |
| `ar` | العربية |
| `pt` | Português |
| `fr` | Français |
| `tr` | Türkçe |

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+ (frontend)

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

npm install
npm run build   # production
# or for development: npm run dev
```

## Usage modes

### Full-stack (web UI)

```bash
php artisan serve
# separate terminal: npm run dev
```

Open `http://localhost:8000` — Whois lookup interface inspired by [WhoisTR.net](https://whoistr.net/).

- **Home:** `/`
- **API Docs:** `/docs`

### API only

Endpoints under `/api/v1/whois/*` work independently without the frontend.

## API endpoints

| Method | Endpoint | Rate limit |
|--------|----------|------------|
| GET | `/api/v1/whois/{domain}?format=summary\|full` | 60/min (IP) |
| POST | `/api/v1/whois/bulk` | 10/min (IP) |

See full documentation at **`/docs`** or below.

### Format parameter

| Value | Fields |
|-------|--------|
| `summary` (default) | domain, registrar, created_at, expires_at, states |
| `full` | All fields + raw WHOIS text |

### Examples

```bash
curl "http://localhost:8000/api/v1/whois/google.com?format=summary"

curl -X POST http://localhost:8000/api/v1/whois/bulk \
  -H "Content-Type: application/json" \
  -d '{"domains":["google.com","example.com"],"format":"full"}'
```

## Architecture (DDD)

```
app/
├── Domain/Whois/           # Business rules
├── Application/Whois/      # Use cases
├── Infrastructure/Whois/   # php-whois + cache decorator
└── Http/                   # API layer
resources/js/               # Vue 3 frontend + i18n
```

## Configuration

```env
WHOIS_TIMEOUT=20
WHOIS_BULK_LIMIT=50
WHOIS_CACHE_ENABLED=true
WHOIS_CACHE_TTL=3600
WHOIS_RATE_LIMIT=60
WHOIS_BULK_RATE_LIMIT=10
CACHE_STORE=database
```

WHOIS results are cached for **1 hour** by default (`WHOIS_CACHE_TTL`).

## Tests

```bash
php artisan test
```
