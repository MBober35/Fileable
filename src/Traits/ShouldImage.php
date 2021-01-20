<?php

namespace MBober35\Fileable\Traits;

use App\Models\File;

trait ShouldImage
{
    protected static function bootShouldImage()
    {
        static::deleted(function ($model) {
            $model->clearImage(true);
        });
    }

    /**
     * В какой столбец записать изображение.
     *
     * @return string
     */
    protected function getImageKey()
    {
        return ! empty($this->imageKey) ? $this->imageKey : "image_id";
    }

    /**
     * Изображение.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo(File::class, $this->getImageKey());
    }

    /**
     * Загрузить изображение.
     *
     * @param false $path
     * @param string $inputName
     * @param string $field
     */
    public function uploadImage($path = false, $inputName = "image", $field = "title")
    {
        if (! request()->hasFile($inputName)) return;
        if (! $path) $path = $this->getTable();
        // Удалить старое изображение.
        $this->clearImage();
        // Сохранить файл изображения.
        $path = request()->file($inputName)->store($path);
        // Получить расширение файла.
        $mime = request()->file($inputName)->getClientOriginalExtension();
        // Получить имя файла.
        if (! empty($this->{$field})) {
            $name = $this->{$field};
        }
        else {
            $name = request()->file($inputName)->getClientOriginalName();
            $name = str_replace(".{$mime}", "", $name);
        }
        // Тип файла изображение.
        $type = "image";
        // Создание файла.
        $image = File::create(
            compact("path", "name", "mime", "type")
        );
        $this->image()->associate($image);
        $this->save();
    }

    /**
     * Удалить изображение.
     */
    public function clearImage($deleted = false)
    {
        $image = $this->image;
        if (! empty($image)) {
            $image->delete();
        }
        if (! $deleted) {
            $this->image()->disassociate();
            $this->save();
        }
    }
}