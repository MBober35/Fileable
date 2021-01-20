<?php

namespace MBober35\Fileable;

use App\Models\File;
use Illuminate\Support\ServiceProvider as BaseProvider;
use MBober35\Fileable\Commands\FilesCommand;
use MBober35\Fileable\Helpers\GalleryActionsManager;
use MBober35\Fileable\Observers\FileOberver;

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

        // Facades.
        $this->app->singleton("gallery-actions", function () {
            return new GalleryActionsManager;
        });

        // Конфигурация.
        $this->mergeConfigFrom(
            __DIR__ . "/config/gallery.php", "gallery"
        );
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

        // Наблюдатели.
        $this->addObservers();

        // Assets.
        $this->publishes([
            __DIR__ . '/resources/js/components' => resource_path('js/components/Fileable'),
        ], 'public');

        // Адреса.
        $this->loadRoutesFrom(__DIR__ . '/routes/gallery.php');

        // Конфигурация.
        $this->publishes([
            __DIR__ . "/config/gallery.php" => config_path("gallery.php"),
        ], "config");
    }

    /**
     * Наблюдатели.
     */
    protected function addObservers()
    {
        if (
            file_exists(app_path("Observers\FileObserver.php")) &&
            file_exists(app_path("Models\File.php"))
        ) {
            File::observe(FileOberver::class);
        }
    }
}
