<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "prefix" => "ajax/vue/gallery",
    "middleware" => ["web", "management"],
    "as" => "ajax.gallery."
], function () {
    Route::post("/{model}/{id}", [\App\Http\Controllers\Fileable\GalleryController::class, "store"])
        ->name("store");
});
