<?php


namespace MBober35\Fileable\Traits;


use App\Models\File;

trait ShouldGallery
{
    protected static function bootShouldGallery()
    {
        static::deleted(function($model) {
            $model->clearImages();
        });
    }

    /**
     * Изображения.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(File::class, "fileable")
            ->where("type", "image");
    }

    /**
     * Обложка.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function cover()
    {
        return $this->morphOne(File::class, "fileable")
            ->where("type", "image")
            ->oldest("priority");
    }

    /**
     * Удалить все изображения.
     */
    public function clearImages()
    {
        foreach ($this->images as $image) {
            $image->delete();
        }
    }
}