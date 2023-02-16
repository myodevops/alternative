<?php

namespace myodevops\ALTErnative;

use Illuminate\Support\ServiceProvider as ServiceProvider;
use Illuminate\Contracts\View\Factory;
use myodevops\ALTErnative\Views\Components\Form;

class ALTErnativeServiceProvider extends ServiceProvider
{
    protected $pkgPrefix = 'alternative';

    protected $formComponents = [
        'datatable' => Form\Datatable::class,
        'datatable-actions' => Form\DatatableActions::class,
        'input-date' => Form\InputDate::class,
        'input-datetime' => Form\InputDateTime::class,
        'input-email' => Form\InputEmail::class,
        'input-number' => Form\InputNumber::class,
        'input-time' => Form\InputTime::class,
        'input-url' => Form\InputUrl::class,
        'input' => Form\Input::class,
        'modal-confirm' => Form\ModalConfirm::class,
        'modal-form' => Form\ModalForm::class,
        'modal-message' => Form\ModalMessage::class,
        'option' => Form\Option::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Form\Traits\AdminLteDataTableManageable::class, Form\Traits\AdminLteDataTableManage::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Factory $view)
    {
        $this->loadViews();
        $this->loadComponents();
        $this->copyPubblics();
    }

    /**
     * Load the package views.
     *
     * @return void
     */
    private function loadViews()
    {
        $viewsPath = $this->packagePath('resources/views');
        $this->loadViewsFrom($viewsPath, $this->pkgPrefix);
    }

    /**
     * Get the absolute path to some package resource.
     *
     * @param  string  $path  The relative path to the resource
     * @return string
     */
    private function packagePath($path)
    {
        return __DIR__."/../$path";
    }

    /**
     * Load the blade view components.
     *
     * @return void
     */
    private function loadComponents()
    {
        // Support of x-components is only available for Laravel >= 7.x
        // versions. So, we check if we can load components.

        $canLoadComponents = method_exists(
            'Illuminate\Support\ServiceProvider',
            'loadViewComponentsAs'
        );

        if (! $canLoadComponents) {
            return;
        }

        // Load all the blade-x components.

        $components = array_merge(
            $this->formComponents,
        );

        $this->loadViewComponentsAs($this->pkgPrefix, $components);
    }

    private function copyPubblics()
    {
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/myodevops/alternative'),
        ], 'public');
    }
}
