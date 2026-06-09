<?php

namespace App\Providers;

use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Infrastructure\Whois\CachedWhoisRepository;
use App\Infrastructure\Whois\PhpWhoisRepository;
use Illuminate\Support\ServiceProvider;

class WhoisServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PhpWhoisRepository::class);

        $this->app->singleton(WhoisRepositoryInterface::class, function ($app) {
            return new CachedWhoisRepository(
                $app->make(PhpWhoisRepository::class),
            );
        });
    }
}
