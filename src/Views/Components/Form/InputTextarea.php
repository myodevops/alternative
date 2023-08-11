<?php

namespace myodevops\ALTErnative\Views\Components\Form;

class InputTextarea extends Input
{
    public $rows;         // The number of rows in the controll
    public $wrap;         // If is set to "on" (default), the controll wrap the text. No wrap with "off"

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, 
                                $label, 
                                $placeholder, 
                                $fieldname="", 
                                $rows=2,
                                $disabled=null,
                                $wrap="on")
    {
        parent::__construct($name, 
                            $label, 
                            $placeholder, 
                            $fieldname, 
                            $disabled);
        $this->rows = $rows;
        $this->wrap = $wrap;
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