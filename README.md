<p align="center">
  <img src="docs/readme-banner.png" alt="WhoisScope" width="900">
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License: MIT"></a>
  <img src="https://img.shields.io/badge/PHP-8.6-777BB4?logo=php&logoColor=white" alt="PHP 8.6">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Vue-3-4FC08D?logo=vuedotjs&logoColor=white" alt="Vue 3">
</p>

<p align="center">
  Self-hosted WHOIS lookup service with a JSON API and a web interface.
</p>

---

WhoisScope queries WHOIS servers directly, parses the response, and reports whether a domain is registered, available, or unknown, along with its registrar, dates, status codes, and name servers. Results are cached and requests are rate-limited per IP.

<p align="center">
  <img src="docs/screenshots/home.png" alt="WhoisScope web interface" width="800">
</p>

## Features

- Single and bulk lookups (up to 50 domains per request, processed concurrently)
- `summary` and `full` response formats, including raw WHOIS output
- Registration status detection: `registered`, `available`, `unknown`
- Response caching and per-IP rate limiting
- Web interface in 7 languages with CSV export for bulk results
- Built-in API reference at `/docs`

## Quick start

### Docker

```bash
docker compose up -d --build
```

The app is served at http://localhost:8000.

### Local development

Requirements: PHP 8.4+, Composer 2, Node.js 22+.

```bash
git clone https://github.com/eyupfidan/whois-scope.git
cd whois-scope
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
composer dev
```

`composer dev` starts the PHP server, queue listener, log viewer, and Vite together.

## API

Base URL: `/api/v1/whois`

| Method | Path | Rate limit | Description |
|--------|------|------------|-------------|
| `GET` | `/{domain}` | 60/min | Look up one domain |
| `POST` | `/bulk` | 10/min | Look up up to 50 domains |

Both endpoints accept `format=summary` (default) or `format=full`.

```bash
curl "http://localhost:8000/api/v1/whois/google.com"
```

```json
{
  "data": {
    "domain": "google.com",
    "registration_status": "registered",
    "registrar": "MarkMonitor Inc.",
    "created_at": "1997-09-15T04:00:00+00:00",
    "expires_at": "2028-09-14T04:00:00+00:00",
    "states": ["client delete prohibited", "client transfer prohibited"]
  }
}
```

```bash
curl -X POST "http://localhost:8000/api/v1/whois/bulk" \
  -H "Content-Type: application/json" \
  -d '{"domains": ["google.com", "example.org"]}'
```

Errors return `{"message": "...", "code": "..."}` with one of these codes:

| Status | Code | Cause |
|--------|------|-------|
| 422 | `invalid_domain` | Malformed domain name |
| 429 | `too_many_requests` | Rate limit exceeded |
| 502 | `lookup_failed` | WHOIS server unreachable or TLD unsupported |

The full reference is available in the running app at `/docs`.

## Configuration

| Variable | Default | Description |
|----------|---------|-------------|
| `WHOIS_TIMEOUT` | `8` | Seconds to wait for a WHOIS response |
| `WHOIS_CONNECT_TIMEOUT` | `3` | Seconds to open the connection |
| `WHOIS_BULK_LIMIT` | `50` | Maximum domains per bulk request |
| `WHOIS_BULK_CONCURRENCY` | `5` | Parallel lookups in a bulk request |
| `WHOIS_CACHE_ENABLED` | `true` | Cache lookup results |
| `WHOIS_CACHE_TTL` | `3600` | Cache lifetime in seconds |
| `WHOIS_RATE_LIMIT` | `60` | Single lookups per minute per IP |
| `WHOIS_BULK_RATE_LIMIT` | `10` | Bulk requests per minute per IP |

Custom WHOIS servers per TLD can be set in `config/whois.php` under `custom_servers`.

## Deployment

The Dockerfile builds a single Alpine-based image running Nginx and PHP-FPM. On start, the container generates `APP_KEY` if it is missing, caches config and routes, and runs migrations when `RUN_MIGRATIONS=true`.

**Database.** SQLite is used by default; mount `/var/www/html/database` and `/var/www/html/storage` as volumes to keep data. For PostgreSQL, set:

```env
DB_CONNECTION=pgsql
DB_URL=postgresql://user:password@host:5432/database
```

`DATABASE_URL` is accepted in place of `DB_URL`. The image includes the PostgreSQL driver by default; to build for MySQL instead, pass `--build-arg PHP_DB_EXTENSIONS=pdo_mysql`, or pass an empty value for SQLite only.

**Dokploy.** Create an application from this repository with the Dockerfile build type, expose container port 80, and set the environment variables above along with `APP_URL` and a persistent `APP_KEY`.

## Testing

```bash
php artisan test
```

## License

Released under the [MIT License](LICENSE).
