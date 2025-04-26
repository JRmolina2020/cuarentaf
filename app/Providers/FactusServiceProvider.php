<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FactusApiService;

class FactusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FactusApiService::class, function ($app) {
            return new FactusApiService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
