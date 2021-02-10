<?php

namespace MBober35\Fileable\Facades;


use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;
use MBober35\Fileable\Helpers\GalleryActionsManager;

/**
 * @method static string getValidation(string $model)
 * @method static bool|Model getGalleryModel(string $model, int $id)
 * @method static File setPriority(File $file)
 * @method static getGalleryResource(Model $object)
 *
 * @see GalleryActionsManager
 */
class GalleryActions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "gallery-actions";
    }
}