<?php

namespace MBober35\Fileable\Controllers;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use MBober35\Fileable\Facades\GalleryActions;
use Illuminate\Http\Response as IlluminateResponse;
use MBober35\Fileable\Facades\ThumbnailActions;

class ThumbnailController extends Controller
{
    /**
     * Получить изображение.
     *
     * @param string $template
     * @param File $file
     * @return IlluminateResponse
     */
    public function show(string $template, string $fileName)
    {
        $file = ThumbnailActions::findByName($fileName);
        if ($file->type != "image") abort(404);

        switch ($template) {
            case "original":
                return $this->buildResponse(Storage::get($file->path));

            default:
                return $this->makeImage($template, $file);
        }
    }

    /**
     * Вернуть картинку.
     *
     * @param $content
     * @return IlluminateResponse
     */
    protected function buildResponse($content)
    {
        // define mime type
        $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $content);

        // respond with 304 not modified if browser has the image cached
        $etag = md5($content);
        $not_modified = isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag;
        $content = $not_modified ? NULL : $content;
        $status_code = $not_modified ? 304 : 200;

        // return http response
        return new IlluminateResponse($content, $status_code, array(
            'Content-Type' => $mime,
            'Cache-Control' => 'max-age='.(config('imagecache.lifetime')*60).', public',
            'Content-Length' => strlen($content),
            'Etag' => $etag
        ));
    }

    /**
     * Сформировать изображение.
     *
     * @param string $template
     * @param File $image
     * @return IlluminateResponse
     */
    protected function makeImage(string $template, File $image)
    {
        $content = ThumbnailActions::getFilteredContent($template, $image);
        return $this->buildResponse($content);
    }
}
