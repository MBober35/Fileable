<?php

namespace MBober35\Fileable\Commands;

use Illuminate\Console\Command;
use MBober35\Helpers\Traits\CopyStubs;

class FilesCommand extends Command
{
    use CopyStubs;

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
    }
}
