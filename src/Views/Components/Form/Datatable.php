<?php

namespace myodevops\ALTErnative\Views\Components\Form;

use Illuminate\View\Component;

class Datatable extends Component
{
    public $name = '';
    public $heads = '';

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, $heads)
    {
        $this->name = $name;
        $this->heads = $heads;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.datatable');
    }
}
