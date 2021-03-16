@isset($routeName)
<picture>
    @foreach ($grid as $width => $gridTemplate)
        <source srcset="{{ route($routeName, ["template" => $gridTemplate, "filename" => $image->file_name]) }}"
                media="(min-width: {{ $width }}px)">
    @endforeach
    <img src="{{ route($routeName, ["template" => $template, "filename" => $image->file_name]) }}"
         class="{{ $class }}"
         alt="{{ $image->name }}">
</picture>
@endisset