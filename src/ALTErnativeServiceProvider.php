<?php

namespace myodevops\ALTErnative;

use Illuminate\Support\ServiceProvider as ServiceProvider;
use Illuminate\Contracts\View\Factory;
use myodevops\ALTErnative\Views\Components\Form;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class ALTErnativeServiceProvider extends ServiceProvider
{
    protected $pkgPrefix = 'alternative';

    protected $formComponents = [
        'datatable' => Form\Datatable::class,
        'errlog-datatable' => Form\ErrLogDatatable::class,
        'laravellog-datatable' => Form\LaravelLogDatatable::class,
        'datatable-actions' => Form\DatatableActions::class,
        'input-date' => Form\InputDate::class,
        'input-datetime' => Form\InputDateTime::class,
        'input-email' => Form\InputEmail::class,
        'input-number' => Form\InputNumber::class,
        'input-textarea' => Form\InputTextarea::class,
        'input-time' => Form\InputTime::class,
        'input-url' => Form\InputUrl::class,
        'input' => Form\Input::class,
        'input-key' => Form\InputKey::class,
        'input-checkbox' => Form\InputCheckbox::class,
        'modal-confirm' => Form\ModalConfirm::class,
        'modal-message' => Form\ModalMessage::class,
        'modal-form' => Form\ModalForm::class,
        'modeless-form' => Form\ModelessForm::class,
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
        $this->copyPublics();

        $altesqlitefile = 'altesqlite.sq3';
        Config::set('database.connections.altesqlite', [
            'driver' => 'sqlite',
            'database' => database_path($altesqlitefile),
            'prefix' => '',
        ]);
        
        if (!File::exists($altesqlitefile)) {
            File::put($altesqlitefile, '');
        }

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
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

    private function copyPublics()
    {
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/myodevops/alternative'),
        ], 'laravel-assets');
    }
}
