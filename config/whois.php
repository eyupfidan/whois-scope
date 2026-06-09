<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WHOIS Query Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds to wait for a WHOIS server response.
    |
    */

    'timeout' => (int) env('WHOIS_TIMEOUT', 20),

    /*
    |--------------------------------------------------------------------------
    | Custom TLD Servers
    |--------------------------------------------------------------------------
    |
    | Additional or override WHOIS servers for specific TLD zones.
    | Format: ['zone' => '.example', 'host' => 'whois.nic.example']
    |
    */

    'custom_servers' => [],

    /*
    |--------------------------------------------------------------------------
    | Bulk Lookup Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of domains allowed in a single bulk request.
    |
    */

    'bulk_limit' => (int) env('WHOIS_BULK_LIMIT', 50),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */

    'cache_enabled' => (bool) env('WHOIS_CACHE_ENABLED', true),

    'cache_ttl' => (int) env('WHOIS_CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting (requests per minute, per IP)
    |--------------------------------------------------------------------------
    */

    'rate_limit' => (int) env('WHOIS_RATE_LIMIT', 60),

    'bulk_rate_limit' => (int) env('WHOIS_BULK_RATE_LIMIT', 10),

];
