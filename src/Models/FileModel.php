<?php

namespace MBober35\Fileable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileModel extends Model
{
    use HasFactory;

    protected $fillable = [
        "path",
        "name",
        "mime",
        "priority",
        "type",
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
}
