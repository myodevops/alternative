<?php

namespace myodevops\ALTErnative\Views\Components\Form;

use Illuminate\View\Component;

class DatatableActions extends Component
{
    public $id = '';
    public $method = '';
    public $action = '';

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, 
                                $action = '#', 
                                $method = 'get')
    {
        $this->id = $id;
        $this->action = $action;
        $this->method = $method;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.datatable-actions');
    }
}
