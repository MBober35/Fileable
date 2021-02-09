<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fileable\DocumentsController;

Route::group([
    "prefix" => "ajax/vue/documents",
    "middleware" => ["web", "management"],
    "as" => "ajax.documents."
], function () {
    Route::group([
        "prefix" => "/{model}/{id}",
    ], function () {
        Route::get("/", [DocumentsController::class, "index"])
            ->name("index");
        Route::post("/", [DocumentsController::class, "store"])
            ->name("store");
        Route::put("/", [DocumentsController::class, "order"])
            ->name("order");
        Route::put("/{file}", [DocumentsController::class, "update"])
            ->name("update");
        Route::delete("/{file}", [DocumentsController::class, "destroy"])
            ->name("destroy");
    });
});
