<?php

namespace MBober35\Fileable\Commands;

use Illuminate\Console\Command;
use MBober35\Helpers\Traits\CopyStubs;
use MBober35\Helpers\Traits\CopyVue;

class FilesCommand extends Command
{
    use CopyStubs, CopyVue;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fileable
                    { --no-replace : Without replace any files }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initial command for files package';

    protected $prefix;
    protected $noReplace;

    protected $vueIncludes = [
        "admin" => [
            "image-gallery" => "GalleryComponent",
            "document-group" => "DocumentsComponent",
        ],
    ];
    protected $vueFolder = "Fileable";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->prefix = __DIR__ . "/Stubs/";
        $this->noReplace = false;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->noReplace = $this->option("no-replace");

        $this->exports();

        $this->makeVueIncludes("admin");
    }

    /**
     * Создать необходимые файлы.
     */
    protected function exports()
    {
        // Export models.
        $this->copyStubs($this->prefix . "models", "Models", $this->noReplace);

        // Export observers.
        $this->copyStubs($this->prefix . "observers", "Observers", $this->noReplace);

        // Export controllers.
        $this->copyStubs($this->prefix . "controllers", "Http/Controllers/Fileable", $this->noReplace);
    }
}
