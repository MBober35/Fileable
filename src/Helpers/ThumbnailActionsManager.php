<?php


namespace MBober35\Fileable\Helpers;


use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class ThumbnailActionsManager
{
    /**
     * Применить фильтр.
     *
     * @param string $template
     * @param File $file
     * @return mixed|string
     */
    public function getFilteredContent(string $template, File $file)
    {
        $filtered = $this->getFilteredImage($template, $file->id);
        if (! empty($filtered)) {
            return Storage::get($filtered->path);
        }
        else {
            return $this->makeImage($template, $file);
        }
    }

    /**
     * Найти файл миниатюры.
     *
     * @param string $template
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    protected function getFilteredImage(string $template, int $id)
    {
        return File::query()
            ->where("parent_id", $id)
            ->where("template", $template)
            ->first();
    }

    /**
     * Создать изображение.
     *
     * @param string $template
     * @param File $file
     * @return mixed
     */
    protected function makeImage(string $template, File $file)
    {
        $class = $this->getTemplate($template);
        $manager = new ImageManager(config("image"));
        $intImage = $manager->make($file->storage);
        $newImage = $intImage->filter($class);
        $content = $newImage->response()->getContent();

        $name = $file->name;
        $mime = $file->mime;
        $type = "image";
        $parent_id = $file->id;
        $path = "filters/{$template}-{$file->id}-" . Str::uuid();
        Storage::put($path, $content);
        $image = File::create(
            compact("path", "name", "mime", "type", "template", "parent_id")
        );
        return $content;
    }

    /**
     * Получить шаблон.
     *
     * @param string $name
     * @return callable|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    protected function getTemplate(string $name)
    {
        $template = config("gallery.templates.{$name}");
        switch (true) {
            // closure template found
            case is_callable($template):
                return $template;

            // filter template found
            case class_exists($template):
                return new $template;

            default:
                // template not found
                abort(404);
                break;
        }
    }
}