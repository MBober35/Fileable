<?php

namespace MBober35\Fileable\Templates;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Medium implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(240, 180);
    }
}