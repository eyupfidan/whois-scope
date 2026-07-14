<?php

namespace Tests\Unit;

use App\Domain\Whois\Services\DomainNameParser;
use PHPUnit\Framework\TestCase;

class DomainNameParserTest extends TestCase
{
    public function test_parses_local_second_level_domains(): void
    {
        $domain = (new DomainNameParser)->parse('https://www.example.com.tr/path');

        $this->assertSame('example.com.tr', $domain->toString());
    }

    public function test_rejects_domains_without_a_public_suffix_separator(): void
    {
        $this->expectException(\App\Domain\Whois\Exceptions\InvalidDomainException::class);

        (new DomainNameParser)->parse('not-a-valid-domain');
    }

    public function test_whois_config_defines_turkish_local_second_level_servers(): void
    {
        $servers = require __DIR__.'/../../config/whois.php';

        $this->assertContains(
            ['zone' => '.com.tr', 'host' => 'whois.trabis.gov.tr'],
            $servers['custom_servers'],
        );
    }
}
