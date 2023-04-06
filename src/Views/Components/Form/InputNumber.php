<?php

namespace myodevops\ALTErnative\Views\Components\Form;

class InputNumber extends Input
{
    public $min = 0;
    public $max = 0;

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
                                $appendSlotClass="", 
                                $min=0, 
                                $max=PHP_FLOAT_MAX)
    {
        parent::__construct($name, 
                            $label, 
                            $placeholder, 
                            $fieldname,
                            $disabled, 
                            $prependSlotClass, 
                            $appendSlotClass);
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.input-number');
    }
}
