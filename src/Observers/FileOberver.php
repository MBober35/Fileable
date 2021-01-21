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
        $this->clearThumbs($file);
        Storage::delete($file->path);
    }

    /**
     * Очистить миниатюры.
     *
     * @param File $file
     */
    protected function clearThumbs(File $file)
    {
        foreach ($file->thumbnails as $thumbnail) {
            $thumbnail->delete();
        }
    }
}
