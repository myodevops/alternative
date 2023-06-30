<?php

namespace myodevops\ALTErnative\Views\Components\Form;

use Illuminate\View\Component;

class ErrLogDatatable extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.errlog-datatable');
    }
}
