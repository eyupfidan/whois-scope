<?php

namespace App\Providers;

use App\Contracts\WhoisClient;
use App\Services\Whois\PhpWhoisClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WhoisClient::class, PhpWhoisClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
