<image-gallery get-url="{{ route("ajax.gallery.index", ["model" => $model, "id" => $id]) }}"
               upload-url="{{ route("ajax.gallery.store", ["model" => $model, "id" => $id]) }}">
</image-gallery>
