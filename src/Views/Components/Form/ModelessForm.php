<?php

namespace myodevops\ALTErnative\Views\Components\Form;

use Illuminate\View\Component;
use JeroenNoten\LaravelAdminLte\Helpers\UtilsHelper;

/**
 * Undocumented class
 */
class ModelessForm extends Component
{
    public $id = '';
    public $title = '';
    public $actionread = '';
    public $actionwrite = '';
    public $method = '';
    public $theme = '';
    public $bodyclass = ''; 
    public $headerclass = ''; 
    public $footerclass = ''; 
    public $icon = ''; 
    public $collapsible = null;  
    public $removable = null;  
    public $maximizable = null; 

    /**
     * Constructor of the class
     *
     * @param string $id The id of the modal
     * @param string $action The URL of the WS action
     * @param string $method The method of the WS call:
     *                       get | post | delete
     * @param string $theme Select a theme for the modal:
     *                      light | danger | info | success | warning
     */
    public function __construct($id, 
                                $title, 
                                $actionread = '#', 
                                $actionwrite = '#', 
                                $method = 'get', 
                                $theme='light',
                                $bodyclass = '',
                                $headerclass = 'bg-success',
                                $footerclass = 'bg-light', 
                                $icon = '',
                                $collapsible = null,
                                $removable = null,
                                $maximizable = null)
    {
        $this->id = $id;
        $this->title = UtilsHelper::applyHtmlEntityDecoder($title);
        $this->actionread = $actionread;
        $this->actionwrite = $actionwrite;
        $this->method = $method;
        $this->theme = $theme;
        $this->bodyclass = $bodyclass;
        $this->headerclass = $headerclass;
        $this->footerclass = $footerclass;
        $this->icon = $icon;
        $this->collapsible = $collapsible;
        $this->removable = $removable;
        $this->maximizable = $maximizable;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('alternative::components.form.modeless-form');
    }
}