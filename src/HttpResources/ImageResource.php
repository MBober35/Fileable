<?php

namespace MBober35\Fileable\HttpResources;

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
            "src" => $this->storage,
            "priority" => $this->priority,
            "name" => $this->name,
            "nameChanged" => $this->name,
            "nameInput" => false,
        ];
    }
}
