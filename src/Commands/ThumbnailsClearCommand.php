<?php

namespace MBober35\Fileable\Commands;

use App\Models\File;
use Illuminate\Console\Command;

class ThumbnailsClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumb:clear
                    { --template= : clear only for template }
                    { --all : clear all }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear generated thumbs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option("all")) {
            $thumbs = $this->getAll();
        } elseif ($template = $this->option("template")) {
            $thumbs = $this->getTemplate($template);
        }
        else {
            $thumbs = [];
        }
        foreach ($thumbs as $thumb) {
            $thumb->delete();
        }
        return 0;
    }

    protected function getTemplate(string $template)
    {
        return File::query()
            ->select("id")
            ->where("template", $template)
            ->get();
    }

    protected function getAll()
    {
        return File::query()
            ->select("id")
            ->whereNotNull("template")
            ->get();
    }
}
