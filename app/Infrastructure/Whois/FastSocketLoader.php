<?php

namespace App\Infrastructure\Whois;

use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Helpers\TextHelper;
use Iodev\Whois\Loaders\ILoader;

class FastSocketLoader implements ILoader
{
    public function __construct(
        private readonly int $connectTimeout,
        private readonly int $readTimeout,
    ) {}

    /**
     * @throws ConnectionException
     * @throws WhoisException
     */
    public function loadText($whoisHost, $query): string
    {
        $errno = 0;
        $errstr = '';
        $handle = @fsockopen($whoisHost, 43, $errno, $errstr, $this->connectTimeout);

        if (! $handle) {
            throw new ConnectionException($errstr ?: "Could not connect to {$whoisHost}", $errno);
        }

        stream_set_timeout($handle, $this->readTimeout);

        if (fwrite($handle, $query) === false) {
            fclose($handle);

            throw new ConnectionException('Query cannot be written');
        }

        $text = '';

        while (! feof($handle)) {
            $chunk = fread($handle, 8192);

            if ($chunk === false || stream_get_meta_data($handle)['timed_out']) {
                fclose($handle);

                throw new ConnectionException('Response chunk cannot be read');
            }

            $text .= $chunk;
        }

        fclose($handle);

        if (preg_match('~^WHOIS\s+.*?LIMIT\s+EXCEEDED~ui', $text, $match)) {
            throw new WhoisException($match[0]);
        }

        return TextHelper::toUtf8($text);
    }
}
