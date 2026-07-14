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

    'timeout' => (int) env('WHOIS_TIMEOUT', 8),

    /*
    |--------------------------------------------------------------------------
    | WHOIS Connect Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds to wait while opening a WHOIS socket connection.
    |
    */

    'connect_timeout' => (int) env('WHOIS_CONNECT_TIMEOUT', 3),

    /*
    |--------------------------------------------------------------------------
    | Bulk Concurrency
    |--------------------------------------------------------------------------
    |
    | Number of domains to look up in parallel during bulk requests.
    |
    */

    'bulk_concurrency' => (int) env('WHOIS_BULK_CONCURRENCY', 5),

    /*
    |--------------------------------------------------------------------------
    | Bulk Request Time Budget
    |--------------------------------------------------------------------------
    |
    | Maximum PHP execution time (seconds) allowed for a bulk WHOIS request.
    |
    */

    'bulk_max_execution' => (int) env('WHOIS_BULK_MAX_EXECUTION', 300),

    /*
    |--------------------------------------------------------------------------
    | Custom TLD Servers
    |--------------------------------------------------------------------------
    |
    | Additional or override WHOIS servers for specific TLD zones.
    | Format: ['zone' => '.example', 'host' => 'whois.nic.example']
    |
    */

    'custom_servers' => [
        ['zone' => '.com.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.net.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.org.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.info.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.biz.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.web.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.gen.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.av.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.dr.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.name.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.tv.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.bbs.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.tel.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.k12.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.edu.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.gov.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.bel.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.pol.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.tsk.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.mil.tr', 'host' => 'whois.trabis.gov.tr'],
        ['zone' => '.nc.tr', 'host' => 'whois.trabis.gov.tr'],
    ],

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
