<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fileable\GalleryController;

Route::group([
    "prefix" => "ajax/vue/gallery",
    "middleware" => ["web", "management"],
    "as" => "ajax.gallery."
], function () {
    Route::group([
        "prefix" => "/{model}/{id}",
    ], function () {
        Route::get("/", [GalleryController::class, "index"])
            ->name("index");
        Route::post("/", [GalleryController::class, "store"])
            ->name("store");
        Route::put("/", [GalleryController::class, "order"])
            ->name("order");
    });
});
