<?php


namespace MBober35\Fileable\Http\Controllers;


use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use MBober35\Fileable\Events\ChangeDocumentOrder;
use MBober35\Fileable\Events\DocumentChanged;
use MBober35\Fileable\Facades\DocumentActions;

class DocumentsController
{
    protected $modelObj;

    public function __construct()
    {
        $route = request()->route();
        if (! empty($route)) {
            $model = $route->parameter("model", false);
            $id = $route->parameter("id", false);
            if ($model && $id) {
                $this->modelObj = DocumentActions::getModel($model, $id);
            }
        }
    }

    /**
     * Получить документы.
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
                "documents" => DocumentActions::getResource($this->modelObj),
            ]);
    }

    public function store(Request $request, string $model, int $id)
    {
        $this->storeValidator($request->all());
        // Получить расширение.
        $mime = $request->file("document")->getClientOriginalExtension();
        // Сохранить файл.
        $fileName = Str::random(40) . "." . $mime;
        $path = $request->file("document")->storeAs("documents/{$model}", $fileName);
        $name = $request->get("name");
        $type = "document";
        $doc = File::create(
            compact("path", "name", "mime", "type")
        );
        $this->modelObj->documents()->save($doc);
        $doc = DocumentActions::setPriority($doc);
        DocumentChanged::dispatch($doc, "created", $this->modelObj);

        return response()
            ->json([
                "success" => true,
                "documents" => DocumentActions::getResource($this->modelObj),
            ]);
    }

    protected function storeValidator(array $data)
    {
        Validator::make($data, [
            "document" => ["required", "file"],
            "name" => ["required", "string", "max:100"]
        ], [
            "document.required" => "Файл не найден !!",
            "document.file" => "Не удалось загрузить документ",
        ], [
            "name" => "Имя",
            "image" => "Файл",
        ])->validate();
    }

    public function update(Request $request, string $model, int $id, File $file)
    {
        $this->updateValidator($request->all());
        $file->name = $request->get("name");
        $file->save();
        DocumentChanged::dispatch($file, "updated", $this->modelObj);
        return response()
            ->json([
                "success" => true,
                "documents" => DocumentActions::getResource($this->modelObj),
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

    public function order(Request $request, string $model, int $id)
    {
        $this->orderValidator($request->all());
        $ids = $request->get("documents");
        foreach ($ids as $priority => $id) {
            try {
                $file = File::find($id);
                $file->priority = $priority;
                $file->save();
            } catch (\Exception $exception) {
                continue;
            }
        }
        ChangeDocumentOrder::dispatch($this->modelObj);
        return response()
            ->json([
                "success" => true,
                "documents" => DocumentActions::getResource($this->modelObj),
            ]);
    }

    /**
     * @param array $data
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function orderValidator(array $data)
    {
        Validator::make($data, [
            "documents" => ["required", "array"]
        ], [], [
            "documents" => "Документы",
        ])->validate();
    }

    public function destroy(string $model, int $id, File $file)
    {
        $file->delete();
        DocumentChanged::dispatch($file, "deleted", $this->modelObj);
        return response()
            ->json([
                "success" => true,
                "documents" => DocumentActions::getResource($this->modelObj),
            ]);
    }
}