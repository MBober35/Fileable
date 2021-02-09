<?php

namespace MBober35\Fileable\Facades;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use MBober35\Fileable\Helpers\DocumentActionsManager;

/**
 * @method static bool|Model getModel(string $model, int $id)
 * @method static File setPriority(File $file)
 * @method static getResource(Model $object)
 *
 * @see DocumentActionsManager
 */
class DocumentActions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "document-actions";
    }
}