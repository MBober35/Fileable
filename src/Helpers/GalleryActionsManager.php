<?php

namespace MBober35\Fileable\Helpers;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;

class GalleryActionsManager
{
    /**
     * Получить модель.
     *
     * @param string $model
     * @param int $id
     * @return bool|Model
     */
    public function getGalleryModel(string $model, int $id)
    {
        $models = config("gallery.models");
        if (empty($models[$model])) return false;
        $class = $models[$model];
        if (! class_exists($class)) return false;
        try {
            return $class::findOrFail($id);
        }
        catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Задать приоритет.
     *
     * @param File $file
     * @return File
     */
    public function setPriority(File $file)
    {
        $max = File::query()
            ->where("fileable_type", $file->fileable_type)
            ->where("fileable_id", $file->fileable_id)
            ->max("priority");
        $file->priority = $max + 1;
        $file->save();
        return $file;
    }
}