<?php


namespace MBober35\Fileable\Traits;


use App\Models\File;
use Illuminate\Support\Str;

trait ShouldDocument
{
    protected static function bootShouldDocument()
    {
        static::deleted(function ($model) {
            $model->clearDocument(true);
        });
    }

    /**
     * В какой сталбец записать файл.
     *
     * @return string
     */
    protected function getDocKey()
    {
        return ! empty($this->docKey) ? $this->docKey : "document_id";
    }

    /**
     * Документ.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document()
    {
        return $this->belongsTo(File::class, $this->getDocKey());
    }

    /**
     * Загрузить документ.
     *
     * @param false $path
     * @param string $inputName
     * @param string $field
     */
    public function uploadDocument($path = false, $inputName = "document", $field = "title")
    {
        if (! request()->hasFile($inputName)) return;
        if (! $path) $path = $this->getTable();
        // Удалить старый файл.
        $this->clearDocument();
        // Получить расширение файла.
        $mime = request()->file($inputName)->getClientOriginalExtension();
        // Сохранить файл.
        $fileName = Str::random(40) . "." . $mime;
        $path = request()->file($inputName)->storeAs($path, $fileName);
        // Получить имя файла.
        if (! empty($this->{$field})) {
            $name = $this->{$field};
        }
        else {
            $name = request()->file($inputName)->getClientOriginalName();
            $name = str_replace(".{$mime}", "", $name);
        }
        // Тип файла документ.
        $type = "document";
        // Создание файла.
        $doc = File::create(
            compact("path", "name", "mime", "type")
        );
        $this->document()->associate($doc);
        $this->save();
    }

    /**
     * Удалить файл.
     *
     * @param false $deleted
     */
    public function clearDocument($deleted = false)
    {
        $doc = $this->document;
        if (! empty($doc)) {
            $doc->delete();
        }
        if (! $deleted) {
            $this->document()->dissociate();
            $this->save();
        }
    }
}