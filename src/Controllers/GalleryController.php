<?php

namespace MBober35\Fileable\Controllers;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MBober35\Fileable\Facades\GalleryActions;

class GalleryController extends Controller
{
    /**
     * Получить изображения.
     *
     * @param string $model
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(string $model, int $id)
    {
        if (! $modelObj = GalleryActions::getGalleryModel($model, $id)) {
            return response()
                ->json([
                    "success" => false,
                    "message" => "Model not found"
                ]);
        }
        return response()
            ->json([
                "success" => true,
                "images" => GalleryActions::getGalleryResource($modelObj),
            ]);
    }

    /**
     * Загрузить изображения.
     *
     * @param Request $request
     * @param string $model
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, string $model, int $id)
    {
        $this->storeValidator($request->all());
        if (! $modelObj = GalleryActions::getGalleryModel($model, $id)) {
            return response()
                ->json([
                    "success" => false,
                    "message" => "Model not found"
                ]);
        }

        $path = $request->file("image")->store("gallery/$model");
        $name = $request->get("name");
        $type = "image";
        $mime = $request->file("image")->getClientOriginalExtension();
        $image = File::create(
            compact("path", "name", "mime", "type")
        );
        $modelObj->images()->save($image);
        $image = GalleryActions::setPriority($image);
        // TODO: fire event.

        return response()
            ->json([
                "success" => true,
                "images" => GalleryActions::getGalleryResource($modelObj),
            ]);
    }

    /**
     * @param array $data
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function storeValidator(array $data)
    {
        Validator::make($data, [
            "image" => ["required", "image"],
            "name" => ["required", "string", "max:100"]
        ], [
            "image.required" => "Файл не найден",
            "image.image" => "Файл должен быть изображением",
        ], [
            "name" => "Имя",
            "image" => "Файл",
        ])->validate();
    }

    /**
     * Изменить порядок вывода.
     *
     * @param Request $request
     * @param string $model
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function order(Request $request, string $model, int $id)
    {
        $this->orderValidator($request->all());

        if (! $modelObj = GalleryActions::getGalleryModel($model, $id)) {
            return response()
                ->json([
                    "success" => false,
                    "message" => "Model not found"
                ]);
        }

        $ids = $request->get("images");
        foreach ($ids as $priority => $id) {
            try {
                $file = File::find($id);
                $file->priority = $priority;
                $file->save();
            }
            catch (\Exception $exception) {
                continue;
            }
        }

        return response()
            ->json([
                "success" => true,
                "images" => GalleryActions::getGalleryResource($modelObj),
            ]);
    }

    /**
     * @param array $data
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function orderValidator(array $data)
    {
        Validator::make($data, [
            "images" => ["required", "array"]
        ], [], [
            "images" => "Изображения",
        ])->validate();
    }
}
