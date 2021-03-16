<?php

namespace MBober35\Fileable\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $modelName = "";
        foreach (config("gallery.models") as $name => $class) {
            if ($this->fileable_type == $class) {
                $modelName = $name;
                break;
            }
        }
        return [
            "path" => $this->path,
            "id" => $this->id,
            "src" => route("thumb-img", [
                "template" => "small",
                "filename" => $this->file_name,
            ]),
            "priority" => $this->priority,
            "name" => $this->name,
            "nameChanged" => $this->name,
            "updateUrl" => route("ajax.gallery.update", [
                "file" => $this,
                "model" => $modelName,
                "id" => $this->fileable_id,
            ]),
            "destroyUrl" => route("ajax.gallery.destroy", [
                "file" => $this,
                "model" => $modelName,
                "id" => $this->fileable_id,
            ]),
        ];
    }
}
