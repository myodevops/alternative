<?php

namespace myodevops\ALTErnative\Views\Components\Form;

use Illuminate\View\Component;

class ModalMessage extends Component
{
    public $id = '';     // The id of the modal
    public $theme = '';  // light | danger | info | success | warning 

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $theme='light')
    {
        $this->id = $id;
        $this->theme = $theme;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.modal-message');
    }
}
