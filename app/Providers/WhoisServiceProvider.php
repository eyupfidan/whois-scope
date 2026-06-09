<?php

namespace App\Providers;

use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Infrastructure\Whois\CachedWhoisRepository;
use App\Infrastructure\Whois\PhpWhoisRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

    public function boot(): void
    {
        RateLimiter::for('whois', function (Request $request) {
            return Limit::perMinute((int) config('whois.rate_limit', 60))
                ->by($request->ip());
        });

        RateLimiter::for('whois-bulk', function (Request $request) {
            return Limit::perMinute((int) config('whois.bulk_rate_limit', 10))
                ->by($request->ip());
        });
    }
}
