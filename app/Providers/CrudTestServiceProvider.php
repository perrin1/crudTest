<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\InstallCrudTest;
use App\Console\Commands\UninstallCrudTest;

class CrudTestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCrudTest::class, // Enregistrez votre commande artisan
            ]);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                UninstallCrudTest::class,
            ]);
        }
    }
}
