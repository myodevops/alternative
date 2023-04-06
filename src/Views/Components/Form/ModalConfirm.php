<?php

namespace myodevops\ALTErnative\Views\Components\Form;

use Illuminate\View\Component;
use JeroenNoten\LaravelAdminLte\Helpers\UtilsHelper;

/**
 * Undocumented class
 */
class ModalConfirm extends Component
{
    public $id = '';
    public $action = '';
    public $method = '';
    public $theme = '';
    public $oklabel = '';  
    public $cancellabel = '';

    /**
     * Constructor of the class
     *
     * @param string $id The id of the modal
     * @param string $action The URL of the WS action
     * @param string $method The method of the WS call:
     *                       get | post | delete
     * @param string $theme Select a theme for the modal:
     *                      light | danger | info | success | warning
     * @param string $oklabel Label of the confirm button (default is Ok)
     * @param string $cancellabel Label of the abort button (default is Cancel)
     */
    public function __construct($id, 
                                $action = '#', 
                                $method = 'get', 
                                $theme='light', 
                                $oklabel='', 
                                $cancellabel = '')
    {
        $this->id = $id;
        $this->action = $action;
        $this->method = $method;
        $this->theme = $theme;
        $this->oklabel = UtilsHelper::applyHtmlEntityDecoder($oklabel == '' ? __('Ok') : $oklabel);
        $this->cancellabel = UtilsHelper::applyHtmlEntityDecoder($cancellabel == '' ? __('Cancel') : $cancellabel);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.modal-confirm');
    }
}
