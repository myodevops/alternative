<?php

namespace myodevops\ALTErnative\Views\Components\Form;

class Option extends Input
{
    public $options = [];
    public $multiple = false;       // If thrue, the control is disabled
    public $labelclass = "";

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
                                $options, 
                                $labelclass="text-black", 
                                $multiple=null)
    {
        $this->options = $options;
        $this->labelclass = $labelclass;
        isset($multiple) ? $this->multiple = true : $this->multiple = false;
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
        return view('alternative::components.form.option');
    }
}
