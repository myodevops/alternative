<?php

namespace myodevops\ALTErnative\Views\Components\Form;

use Illuminate\View\Component;

class InputKey extends Component
{
    public $fieldname = "";         // Name of the field of the key

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($fieldname)
    {
        $this->fieldname = $fieldname;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.input-key');
    }
}
