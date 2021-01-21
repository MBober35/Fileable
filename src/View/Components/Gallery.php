<?php

namespace MBober35\Fileable\View\Components;

use Illuminate\View\Component;

class Gallery extends Component
{
    /**
     * Модель для галереи.
     *
     * @var string
     */
    public $model;

    /**
     * Id модели.
     *
     * @var int
     */
    public $id;

    /**
     * Create a new component instance.
     *
     * Gallery constructor.
     * @param string $model
     * @param int $id
     */
    public function __construct(string $model, int $id)
    {
        $this->model = $model;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('fileable::components.gallery');
    }
}
