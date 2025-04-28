<?php
namespace myodevops\ALTErnative\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use JeroenNoten\LaravelAdminLte\Helpers\CommandHelper;

class InstallAlternative extends Command
{
    protected $signature = 'alternative:install 
                        {--force : Force the overwrite of the file} 
                        {--with-migration : Create the migration file for adding the password column if missing}';
    protected $description = 'Installa ALTErnative: routes, login blade and (optional) migration password';
    protected $authViewsContent = [
        'login.blade.php' => '@extends(\'alternative::auth.login\')',
        'sanctum.blade.php' => '@extends(\'adminlte::auth.login\')',
        'passkey.blade.php' => '@extends(\'alternative::auth.passkey\')',
    ];

    public function handle()
    {      
        $this->runDependencySetup();
        $this->installViews();
        $this->packageJsonUpdate();
        $this->installRoutes();
        $this->updateAuthConfig();
        $this->publishAlternativeConfig();
        $this->updateVerifyCsrfToken();
        
        if ($this->option('with-migration')) {
            $this->createOptionalMigrations();
        }

        $this->linkAlternativeRoutes();
    }

    private function runDependencySetup()
    {
        $this->info('🔧 Running setup for third-party dependencies...');

        // Webauthn (Laragear)
        $this->callSilent('webauthn:install');
        $this->info('✔️ WebAuthn (laragear/webauthn) installed successfully.');
    }

    private function installViews()
    {
        // Publish the authentication views. We actually need to replace the
        // content of any existing authentication view that were originally
        // provided by the legacy Laravel/UI package or AdminLTE package.

        $targetpath = CommandHelper::getViewPath('auth');
   
        foreach ($this->authViewsContent as $file => $content) {
            $target = $targetpath . DIRECTORY_SEPARATOR . $file;

            if (File::exists($target) && !$this->option('force')) {
                $this->warn("⚠️ View {$file} already exists. Use --force to overwrite.");
                continue;
            }

            File::ensureDirectoryExists(File::dirname($target));
            File::put($target, $content);
            $this->info("✔️ View {$file} installed successfully.");
        }
    }

    private function packageJsonUpdate()
    {
        $packageFile = base_path('package.json');
    
        if (File::exists($packageFile)) {
            $package = json_decode(File::get($packageFile), true);
    
            $updated = false;
    
            // Aggiungiamo @laragear/webpass se manca
            if (!isset($package['dependencies']['@laragear/webpass'])) {
                $package['dependencies']['@laragear/webpass'] = '^2.0.0';
                $updated = true;
                $this->info('✔️ @laragear/webpass added to package.json.');
            } else {
                $this->line('✔️ @laragear/webpass already present in package.json.');
            }
    
            // Se ci sono aggiornamenti, sovrascriviamo il file
            if ($updated) {
                File::put($packageFile, json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                $this->line('👉 Run "npm install" to complete the setup.');
            }
        } else {
            $this->warn('⚠️ package.json not found. Skipped frontend library update.');
        }
    }   

    private function installRoutes()
    {
        $stubPath = __DIR__ . '/../../../routes/auth.stub.php';
        $targetPath = base_path('routes/auth.php');

        if (File::exists($targetPath) && !$this->option('force')) {
            $this->warn('The file routes/auth.php already exists. Use --force for overwrite.');
        } else {
            File::copy($stubPath, $targetPath);
            $this->info('✔️ File routes/auth.php installed successfully.');
        }        

        $stubPath = __DIR__ . '/../../../routes/alternative.stub.php';
        $targetPath = base_path('routes/alternative.php');

        if (File::exists($targetPath) && !$this->option('force')) {
            $this->warn('The file routes/alternative.php already exists. Use --force for overwrite.');
        } else {
            File::copy($stubPath, $targetPath);
            $this->info('✔️ File routes/alternative.php installed successfully.');
        }
    }

    private function createOptionalMigrations() {
        if (!Schema::hasColumn('users', 'password')) {
            $timestamp = now()->format('Y_m_d_His');
            $sourceFile = __DIR__.'\..\..\Migrations\optional\add_password_to_users_table.php';
            $targetFile = database_path("migrations/{$timestamp}_add_password_to_users_table.php");

            File::copy($sourceFile, $targetFile);
            $this->info("🛠 Migration created: {$targetFile}");
        } else {
            $this->info('✔️ The column `password` already exists in the table `users`. No migration created.');
        }

        if (!Schema::hasColumn('users', 'remember_token')) {
            $timestamp = now()->format('Y_m_d_His');
            $sourceFile = __DIR__.'\..\..\Migrations\optional\add_remember_token_to_users_table.php';
            $targetFile = database_path("migrations/{$timestamp}_add_remember_token_to_users_table.php");

            File::copy($sourceFile, $targetFile);
            $this->info("🛠 Migration created: {$targetFile}");
        } else {
            $this->info('✔️ The column `remember_token` already exists in the table `users`. No migration created.');
        }
    }              

    private function linkAlternativeRoutes() {
        $webPath = base_path('routes/web.php');
        $includeLine = "require __DIR__.'/auth.php';";

        if (!str_contains(File::get($webPath), $includeLine)) {
            File::append($webPath, "\n\n$includeLine\n");
            $this->info('The route auth.php is added to web.php');
        } else {
            $this->warn('The route auth.php is already included in web.php');
        }

        $webPath = base_path('routes/web.php');
        $includeLine = "require __DIR__.'/alternative.php';";

        if (!str_contains(File::get($webPath), $includeLine)) {
            File::append($webPath, "\n\n$includeLine\n");
            $this->info('The route alternative.php is added to web.php');
        } else {
            $this->warn('The route alternative.php is already included in web.php');
        }
    }

    private function updateAuthConfig()
    {
        $authPath = config_path('auth.php');

        if (!File::exists($authPath)) {
            $this->warn('⚠️ config/auth.php not found. Skipping auth configuration update.');
            return;
        }

        $authContent = File::get($authPath);

        if (str_contains($authContent, "'driver' => 'eloquent-webauthn'")) {
            $this->line('✔️ Auth driver already set to eloquent-webauthn.');
            return;
        }

        // Substition of 'driver' => 'eloquent' with 'driver' => 'eloquent-webauthn'
        $authContent = preg_replace(
            "/('users'\s*=>\s*\[\s*'driver'\s*=>\s*)'eloquent'/",
            "$1'eloquent-webauthn'",
            $authContent
        );

        // Aggiungere 'password_fallback' => true, se manca
        if (!str_contains($authContent, "'password_fallback'")) {
            $authContent = preg_replace(
                "/('model'\s*=>\s*[^\n]+,)/",
                "$1\n            'password_fallback' => true,",
                $authContent
            );
        }

        File::put($authPath, $authContent);

        $this->info('✔️ Auth driver updated to eloquent-webauthn with password fallback.');
        $this->line('👉 Run "php artisan config:clear" to apply the changes.');
    }

    private function publishAlternativeConfig()
    {
        $this->callSilent('vendor:publish', [
            '--tag' => 'alternative-config',
            '--force' => true,
        ]);

        $this->info('✔️ Config alternative.php published successfully.');
    }

    private function updateVerifyCsrfToken()
    {
        $csrfPath = app_path('Http/Middleware/VerifyCsrfToken.php');

        if (!File::exists($csrfPath)) {
            $this->warn('⚠️ VerifyCsrfToken middleware not found. Skipping CSRF exception update.');
            return;
        }

        $content = File::get($csrfPath);

        if (str_contains($content, "'/webauthn/*'")) {
            $this->line('✔️ /webauthn/* already excepted from CSRF protection.');
            return;
        }

        // Inserisci '/webauthn/*' nell'array $except
        $content = preg_replace(
            '/protected \$except = \[([\s\S]*?)\];/',
            "protected \$except = [$1\n        '/webauthn/*',\n    ];",
            $content
        );

        File::put($csrfPath, $content);

        $this->info('✔️ /webauthn/* added to VerifyCsrfToken CSRF exceptions.');
    }
}
