<?php

return [
    "models" => [],
    "imageResource" => \MBober35\Fileable\Http\Resources\ImageResource::class,
    "templates" => [
        "small" => \MBober35\Fileable\Templates\Small::class,
        "medium" => \MBober35\Fileable\Templates\Medium::class,
        "large" => \MBober35\Fileable\Templates\Large::class,
    ],
    "driver" => "gd",
];