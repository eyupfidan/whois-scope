# WHOIS API

Laravel 13 tabanlı WHOIS sorgulama platformu. **REST API** olarak veya **full-stack web uygulaması** olarak kullanılabilir. Domain Driven Design (DDD) mimarisi ile yapılandırılmıştır.

## Gereksinimler

- PHP 8.3+
- Composer
- Node.js 20+ (frontend için)

## Kurulum

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

npm install
npm run build   # production
# veya geliştirme için: npm run dev
```

## Kullanım modları

### Full-stack (web arayüzü)

```bash
php artisan serve
# Ayrı terminalde: npm run dev
```

Tarayıcıda `http://localhost:8000` — [WhoisTR.net](https://whoistr.net/) tarzında Vue.js arayüzü.

### Sadece API

API uç noktaları `/api/v1/whois/*` altında bağımsız çalışır. Frontend olmadan doğrudan entegre edilebilir.

## API uç noktaları

| Method | Endpoint | Rate limit |
|--------|----------|------------|
| GET | `/api/v1/whois/{domain}?format=summary\|full` | 60/dk (IP) |
| POST | `/api/v1/whois/bulk` | 10/dk (IP) |

### Format

| Değer | Alanlar |
|-------|---------|
| `summary` (varsayılan) | domain, registrar, created_at, expires_at, states |
| `full` | Tüm alanlar + ham WHOIS (`raw`) |

### Örnekler

```bash
curl "http://localhost:8000/api/v1/whois/google.com?format=summary"

curl -X POST http://localhost:8000/api/v1/whois/bulk \
  -H "Content-Type: application/json" \
  -d '{"domains":["google.com","example.com"],"format":"full"}'
```

## Mimari (DDD)

```
app/
├── Domain/Whois/           # İş kuralları
├── Application/Whois/      # Use case'ler
├── Infrastructure/Whois/   # php-whois + cache decorator
└── Http/                   # API + Vue sunumu
resources/js/               # Vue 3 frontend
```

## Yapılandırma

```env
WHOIS_TIMEOUT=20
WHOIS_BULK_LIMIT=50
WHOIS_CACHE_ENABLED=true
WHOIS_CACHE_TTL=3600
WHOIS_RATE_LIMIT=60
WHOIS_BULK_RATE_LIMIT=10
CACHE_STORE=database
```

WHOIS sonuçları varsayılan olarak **1 saat** önbellekte tutulur (`WHOIS_CACHE_TTL`).

## Testler

```bash
php artisan test
```
