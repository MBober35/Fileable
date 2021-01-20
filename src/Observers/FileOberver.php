<?php

namespace MBober35\Fileable\Observers;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileOberver
{
    /**
     * Handle the File "deleted" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function deleted(File $file)
    {
        // TODO: clear cut
        Storage::delete($file->path);
        // TODO: make event
    }
}
