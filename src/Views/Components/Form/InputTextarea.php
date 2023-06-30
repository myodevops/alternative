<?php

namespace myodevops\ALTErnative\Views\Components\Form;

class InputTextarea extends Input
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, 
                                $label, 
                                $placeholder, 
                                $fieldname="", 
                                $disabled=null)
    {
        parent::__construct($name, 
                            $label, 
                            $placeholder, 
                            $fieldname, 
                            $disabled);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.input-textarea');
    }
}