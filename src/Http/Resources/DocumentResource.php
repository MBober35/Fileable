<?php

namespace MBober35\Fileable\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
        foreach (config("documents.models") as $name => $class) {
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
            "updateUrl" => route("ajax.documents.update", [
                "file" => $this,
                "model" => $modelName,
                "id" => $this->fileable_id,
            ]),
            "destroyUrl" => route("ajax.documents.destroy", [
                "file" => $this,
                "model" => $modelName,
                "id" => $this->fileable_id,
            ]),
        ];
    }
}
