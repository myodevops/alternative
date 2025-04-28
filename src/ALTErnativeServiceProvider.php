<?php

namespace myodevops\ALTErnative;

use Illuminate\Support\ServiceProvider as ServiceProvider;
use Illuminate\Contracts\View\Factory;
use myodevops\ALTErnative\Views\Components\Form;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;

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

        // Add automatically the key "auth.method" if not already present
        if (!config()->has('auth.method')) {
            config(['auth.method' => env('AUTH_METHOD', 'sanctum')]);
        }        

        // Add the configuration of the Sanctum URLs in AdminLTE  
        if (!config()->has('adminlte.password_reset_url')) {
            config([
                'adminlte.use_route_url' => true,
                'adminlte.password_reset_url' => 'password.request',
                'adminlte.password_email_url' => 'password.email',
                'adminlte.password_update_url' => 'password.update',
                'adminlte.login_url' => 'login',
                'adminlte.logout_url' => 'logout',
                'adminlte.register_url' => 'register',
            ]);
        }

        // Update the config file
        $this->mergeConfigFrom(
            __DIR__.'/../config/alternative.php', 'alternative'
        );
        
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
        $this->registerPublishes();
        $this->registerBladeDirectives();

        $altesqlitefile = 'altesqlite.sq3';
        Config::set('database.connections.altesqlite', [
            'driver' => 'sqlite',
            'database' => database_path($altesqlitefile),
            'prefix' => '',
        ]);
        
        if (!File::exists($altesqlitefile)) {
            File::put($altesqlitefile, '');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                \myodevops\ALTErnative\Console\Commands\InstallAlternative::class,
            ]);
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

    private function registerPublishes()
    {
        // Publication of the configuration file alternative.php
        $this->publishes([
            __DIR__.'/../config/alternative.php' => config_path('alternative.php'),
        ], 'alternative-config');

        // Publication of the asset files (JS, CSS, immagini ecc.)
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/myodevops/alternative'),
        ], 'laravel-assets');
    }

    private function registerBladeDirectives()
    {
        Blade::directive('alternativeAsset', function ($expression) {
            return "<?php echo '<script src=\"' . asset('vendor/myodevops/alternative/dist/js/' . {$expression} . '.js') . '\"></script>'; ?>";
        });
    
        Blade::directive('alternativeCss', function ($expression) {
            return "<?php echo '<link rel=\"stylesheet\" href=\"' . asset('vendor/myodevops/alternative/dist/css/' . {$expression} . '.css') . '\">'; ?>";
        });
    
        Blade::directive('webpassCdn', function () {
            return "<?php echo '<script src=\"https://cdn.jsdelivr.net/npm/@laragear/webpass@2/dist/webpass.js\" defer></script>'; ?>";
        });                
    }    
}
