<?php


namespace MBober35\Fileable\Facades;


use App\Models\File;
use Illuminate\Support\Facades\Facade;
use MBober35\Fileable\Helpers\ThumbnailActionsManager;

/**
 * @method static File findByName(string $fileName)
 * @method static mixed|string getFilteredContent(string $template, File $file)
 *
 * @see ThumbnailActionsManager
 */
class ThumbnailActions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "thumbnail-actions";
    }
}