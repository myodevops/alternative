<?php

namespace myodevops\ALTErnative\Views\Components\Form;

class InputDate extends Input
{
    public $config = [
        "showOn" => "button",
        "singleDatePicker" => true,
        "startDate" => "js:moment()",
        "cancelButtonClasses" => "btn-danger",
        "locale" => ["format" => "DD-MM-YYYY"],
    ];
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, $label, $placeholder, $disabled=null, $prependSlotClass="", $appendSlotClass="")
    {
        parent::__construct($name, $label, $placeholder, $disabled, $prependSlotClass, $appendSlotClass);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.input-date');
    }
}
