<?php

namespace MBober35\Fileable\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use MBober35\Fileable\Events\ChangeImageOrder;
use MBober35\Fileable\Events\ImageChanged;
use MBober35\Fileable\Facades\GalleryActions;

class GalleryController extends Controller
{
    protected $modelObj;
    protected $validationFile;

    public function __construct()
    {
        $route = \request()->route();
        if (! empty($route)) {
            $model = $route->parameter("model", false);
            $id = $route->parameter("id", false);
            if ($model && $id) {
                $this->modelObj = GalleryActions::getGalleryModel($model, $id);
            }
            if ($model) {
                $this->validationFile = GalleryActions::getValidation($model);
            }
        }
    }

    /**
     * Получить изображения.
     *
     * @param string $model
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(string $model, int $id)
    {
        return response()
            ->json([
                "success" => true,
                "images" => GalleryActions::getGalleryResource($this->modelObj),
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

        $mime = $request->file("image")->getClientOriginalExtension();
        $fileName = Str::random(40) . "." . $mime;
        $path = $request->file("image")->storeAs("gallery/$model", $fileName);
        $name = $request->get("name");
        $type = "image";
        $image = File::create(
            compact("path", "name", "mime", "type")
        );
        $this->modelObj->images()->save($image);
        $image = GalleryActions::setPriority($image);
        ImageChanged::dispatch($image, "created", $this->modelObj);

        return response()
            ->json([
                "success" => true,
                "images" => GalleryActions::getGalleryResource($this->modelObj),
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
        $this->extValiadtion();
    }

    /**
     * Валидация расширения.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function extValiadtion()
    {
        if (empty($this->validationFile)) return;
        $data = [
            "image" => strtolower(\request()->file("image")->getClientOriginalExtension()),
        ];
        Validator::make($data, [
            "image" => ["required", "in:{$this->validationFile}"],
        ], [
            "image.in" => "Файл должен быть расширения: {$this->validationFile}",
        ], [
            "image" => "Изображение",
        ])->validate();
    }

    /**
     * Обновить название.
     *
     * @param Request $request
     * @param string $model
     * @param int $id
     * @param File $file
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, string $model, int $id, File $file)
    {
        $this->updateValidator($request->all());
        $file->name = $request->get("name");
        $file->save();
        ImageChanged::dispatch($file, "updated", $this->modelObj);
        return response()
            ->json([
                "success" => true,
                "images" => GalleryActions::getGalleryResource($this->modelObj),
            ]);
    }

    /**
     * @param array $data
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function updateValidator(array $data)
    {
        Validator::make($data, [
            "name" => ["required", "string", "max:100"],
        ], [], [
            "name" => "Имя",
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
        ChangeImageOrder::dispatch($this->modelObj);

        return response()
            ->json([
                "success" => true,
                "images" => GalleryActions::getGalleryResource($this->modelObj),
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

    /**
     * Удаление изображения.
     *
     * @param string $model
     * @param int $id
     * @param File $file
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(string $model, int $id, File $file)
    {
        $file->delete();
        ImageChanged::dispatch($file, "deleted", $this->modelObj);
        return response()
            ->json([
                "success" => true,
                "images" => GalleryActions::getGalleryResource($this->modelObj)
            ]);
    }
}
