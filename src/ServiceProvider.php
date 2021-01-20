<?php

namespace MBober35\Fileable;

use App\Models\File;
use Illuminate\Support\ServiceProvider as BaseProvider;
use MBober35\Fileable\Commands\FilesCommand;
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
