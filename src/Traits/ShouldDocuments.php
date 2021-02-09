<?php


namespace MBober35\Fileable\Traits;


use App\Models\File;

trait ShouldDocuments
{
    protected static function bootShouldDocuments()
    {
        static::deleted(function($model) {
            $model->clearDocuments();
        });
    }

    /**
     * Файлы.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function documents()
    {
        return $this->morphMany(File::class, "fileable")
            ->where("type", "document");
    }

    /**
     * Удалить все документы.
     */
    public function clearDocuments()
    {
        foreach ($this->documents as $document) {
            $document->delete();
        }
    }
}