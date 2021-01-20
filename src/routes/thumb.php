<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fileable\ThumbnailController;

Route::group([
    "middleware" => ["web"],
], function () {
    Route::get("/thumbnail/{template}/{file}", [ThumbnailController::class, "show"])
        ->name("thumb-img");
});
