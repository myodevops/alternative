<?php

namespace myodevops\ALTErnative\Views\Components\Form;

class InputUrl extends Input
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
                                $disabled=null, 
                                $prependSlotClass="", 
                                $appendSlotClass="")
    {
        parent::__construct($name, 
                            $label, 
                            $placeholder, 
                            $fieldname,
                            $disabled, 
                            $prependSlotClass, 
                            $appendSlotClass);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.input-url');
    }
}
