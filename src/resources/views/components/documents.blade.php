<document-group get-url="{{ route("ajax.documents.index", ["model" => $model, "id" => $id]) }}"
               upload-url="{{ route("ajax.documents.store", ["model" => $model, "id" => $id]) }}">
</document-group>
