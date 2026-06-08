# WHOIS API

Laravel 13 tabanlı, geniş TLD desteği hedefleyen WHOIS sorgulama API'si.

## Gereksinimler

- PHP 8.3+
- Composer

## Kurulum

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Geliştirme sunucusu

```bash
php artisan serve
```

## API uç noktaları

| Method | Endpoint | Açıklama |
|--------|----------|----------|
| GET | `/api/v1/whois/{domain}` | Parse edilmiş WHOIS bilgisi |
| GET | `/api/v1/whois/{domain}/raw` | Ham WHOIS metni |

### Örnek

```bash
curl http://localhost:8000/api/v1/whois/google.com
curl http://localhost:8000/api/v1/whois/google.com/raw
```

## Mimari

```
app/
├── Contracts/WhoisClient.php      # WHOIS istemci arayüzü
├── DTOs/WhoisLookupResult.php     # Sorgu sonucu veri modeli
├── Exceptions/Whois/              # Domain ve sorgu hataları
├── Http/Controllers/Api/V1/       # API controller'ları
├── Http/Resources/                # JSON response dönüşümleri
└── Services/Whois/
    ├── DomainValidator.php        # Domain doğrulama ve normalizasyon
    ├── PhpWhoisClient.php         # io-developer/php-whois entegrasyonu
    └── WhoisService.php           # İş mantığı katmanı
```

WHOIS sorguları [io-developer/php-whois](https://github.com/io-developer/php-whois) kütüphanesi üzerinden yapılır. Kütüphane yüzlerce TLD için yerleşik WHOIS sunucu eşlemesi sunar; `config/whois.php` dosyasından özel sunucular eklenebilir.

## Yapılandırma

`.env` dosyasına eklenebilecek değişkenler:

```
WHOIS_TIMEOUT=20
```

## Testler

```bash
php artisan test
```
