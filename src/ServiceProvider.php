<?php

namespace MBober35\Fileable;

use Illuminate\Support\ServiceProvider as BaseProvider;
use MBober35\Fileable\Commands\FilesCommand;

class ServiceProvider extends BaseProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Команды.
        if ($this->app->runningInConsole()) {
            $this->commands([
                FilesCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Миграции.
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}
