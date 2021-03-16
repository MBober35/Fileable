<?php

namespace MBober35\Fileable\View\Components;

use App\Models\File;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class Picture extends Component
{
    /**
     * @var object
     */
    public $image;

    /**
     * @var string
     */
    public $template;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $routeName;

    /**
     * @var array
     */
    public $grid;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($image, string $template, string $class = "img-fluid", array $grid = [])
    {
        if (is_array($image)) $image = (object) $image;
        $this->image = $image;
        $this->template = $template;
        $this->class = $class;
        $this->grid = $grid;
        $this->makeRoute();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('fileable::components.picture');
    }

    protected function makeRoute()
    {
        if ($this->image instanceof File) {
            $this->routeName = "thumb-img";
        } elseif (Route::has("imagecache")) {
            $this->routeName = "imagecache";
        } else {
            $this->routeName = null;
        }
    }
}
