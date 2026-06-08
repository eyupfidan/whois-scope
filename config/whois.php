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

];
