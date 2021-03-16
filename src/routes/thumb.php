<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fileable\ThumbnailController;

Route::group([
    "middleware" => ["web"],
], function () {
    Route::get("/thumbnail/{template}/{filename}", [ThumbnailController::class, "show"])
        ->name("thumb-img");
});
