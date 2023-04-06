<?php

namespace myodevops\ALTErnative\Views\Components\Form;

use Illuminate\View\Component;
use JeroenNoten\LaravelAdminLte\Helpers\UtilsHelper;

class Input extends Component
{
    public $name = '';              // The name of the component
    public $label = '';             // The label of the component
    public $placeholder = '';       // The placeholder of the component
    public $fieldname = "";         // Name of the field of the record
    public $disabled = false;       // If thrue, the control is disabled
    public $prependSlotClass = "";  // Class for the image before the component
    public $appendSlotClass = "";   // Class for the image before the component

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
        $this->name = $name; 
        $this->label = UtilsHelper::applyHtmlEntityDecoder($label);
        $this->placeholder = $placeholder;
        $this->fieldname = $fieldname;
        $this->prependSlotClass = $prependSlotClass;
        $this->appendSlotClass = $appendSlotClass;
        isset($disabled) ? $this->disabled = true : $this->disabled = false;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.input');
    }
}
