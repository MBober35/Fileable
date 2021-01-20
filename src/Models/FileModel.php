<?php

namespace MBober35\Fileable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileModel extends Model
{
    use HasFactory;

    protected $fillable = [
        "path",
        "name",
        "mime",
        "priority",
        "type",
        "parent_id",
        "template",
    ];

    /**
     * Может относится к любой модели.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function fileable()
    {
        return $this->morphTo();
    }

    public function getStorageAttribute()
    {
        return Storage::url($this->path);
    }

    public function getFileNameAttribute()
    {
        $exploded = explode("/", $this->path);
        return $exploded[count($exploded) - 1];
    }
}
