<?php

namespace MBober35\Fileable\Helpers;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use MBober35\Helpers\Exceptions\PreventActionException;

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
        if (empty($models[$model])) throw new PreventActionException("Empty config", 404);
        $class = $models[$model];
        if (! class_exists($class)) throw new PreventActionException("Clas not found", 404);
        try {
            return $class::findOrFail($id);
        }
        catch (\Exception $exception) {
            throw new PreventActionException("Model not found", 404);
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

    /**
     * @param Model $object
     * @return mixed
     */
    public function getGalleryResource(Model $object)
    {
        $collection = $object->images()->orderBy("priority")->get();
        $class = config("gallery.imageResource");
        return $class::collection($collection);
    }
}