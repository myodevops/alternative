<?php

namespace myodevops\ALTErnative\Views\Components\Form;

class InputCheckbox extends Input
{
    public $dataoncolor = "success";
    public $dataoffcolor = "danger";
    public $dataontext = "";
    public $dataofftext = "";

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
                                $dataoncolor="success", 
                                $dataoffcolor="danger",
                                $dataontext="", 
                                $dataofftext="")
    {
        parent::__construct($name, 
                            $label, 
                            $placeholder, 
                            $fieldname,
                            $disabled);

        $this->dataoncolor = $dataoncolor;
        $this->dataoffcolor = $dataoffcolor;
        $this->dataontext = $dataontext;
        $this->dataofftext = $dataofftext;
                        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.input-checkbox');
    }
}