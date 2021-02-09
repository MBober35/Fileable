<?php


namespace MBober35\Fileable\Helpers;


use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use MBober35\Helpers\Exceptions\PreventActionException;

class DocumentActionsManager
{
    /**
     * Получить модель.
     *
     * @param string $model
     * @param int $id
     * @return false
     * @throws PreventActionException
     */
    public function getModel(string $model, int $id)
    {
        $models = config("documents.models");
        if (empty($models[$model])) throw new PreventActionException("Empty config", 404);
        $class = $models[$model];
        if (! class_exists($class)) throw new PreventActionException("Class not found", 404);
        try {
            return $class::findOrFail($id);
        } catch (\Exception $exception) {
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
    public function getResource(Model $object)
    {
        $collection = $object->documents()->orderBy("priority")->get();
        $class = config("documents.resource");
        return $class::collection($collection);
    }
}