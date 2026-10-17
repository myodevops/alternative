# Installation

[← Back to the README](../README.md)

## 1. Install the package

```bash
composer require myodevops/alternative
```

The service provider `myodevops\ALTErnative\ALTErnativeServiceProvider` is
registered automatically through Laravel package discovery.

ALTErnative requires `jeroennoten/laravel-adminlte`. If AdminLTE is not set up
in your application yet, install it first (`php artisan adminlte:install`) and
follow its own documentation.

## 2. The `altesqlite` database

ALTErnative keeps its own data (error log, `laravel.log` index, internal
settings) in a separate SQLite database. The service provider registers a
connection named **`altesqlite`** that points to `database/altesqlite.sq3`, and
loads the package migrations automatically, so `php artisan migrate` creates
the `errorlogs`, `laravellogs` and `settings` tables in it.

The file is created automatically (empty) the first time the application
boots, so you only need to run the migrations:

```bash
php artisan migrate
```

Add `database/altesqlite.sq3` to `.gitignore` if you don't want to commit it.

## 3. Front-end assets

### Publish the assets

```bash
php artisan vendor:publish --tag=laravel-assets
```

This copies the package `public/` folder to `public/vendor/myodevops/alternative/`:

```
public/vendor/myodevops/alternative/dist/
├── css/alternative.css        # small UI tweaks, Comfortaa font, Bootstrap Icons
├── css/daterangepicker.css
├── fonts/Comfortaa.woff
├── js/alternative.js          # the myo.UI / myo.WS runtime
├── js/alternative.min.js      # minified version of alternative.js
├── js/daterangepicker.js
└── js/daterangepicker.min.js
```

Republish (`--force`) after every package update.

### Configure the AdminLTE plugins

The components render AdminLTE components, which need the matching AdminLTE
plugins to be active. In `config/adminlte.php`, under `plugins`, enable (or add)
these plugins, either globally (`'active' => true`) or per page with
`@section('plugins.<Name>', true)`:

| AdminLTE plugin       | Used by |
| --------------------- | ------- |
| `Datatables` + Buttons extension (`DatatablesPlugins`) | `datatable`, `errlog-datatable`, `laravellog-datatable` |
| `Select2`             | `option` |
| `DateRangePicker` (needs moment.js) | `input-date`, `input-datetime` |
| `BootstrapSwitch`     | `input-checkbox` |
| `Sweetalert2`         | every alert raised by `alternative.js` |

ALTErnative bundles its own copy of Date Range Picker, so you can point the
`DateRangePicker` plugin at the published files:

```php
'DateRangePicker' => [
    'active' => true,
    'files' => [
        ['type' => 'js',  'asset' => false, 'location' => '//cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js'],
        ['type' => 'js',  'asset' => true,  'location' => 'vendor/myodevops/alternative/dist/js/daterangepicker.js'],
        ['type' => 'css', 'asset' => true,  'location' => 'vendor/myodevops/alternative/dist/css/daterangepicker.css'],
    ],
],
```

### Load `alternative.js`

The simplest way is to declare ALTErnative itself as an AdminLTE plugin:

```php
'ALTErnative' => [
    'active' => true,
    'files' => [
        ['type' => 'js',  'asset' => true, 'location' => 'vendor/myodevops/alternative/dist/js/alternative.js'],
        ['type' => 'css', 'asset' => true, 'location' => 'vendor/myodevops/alternative/dist/css/alternative.css'],
    ],
],
```

Alternatively, inside a page you can use the Blade directives provided by the
package (see [PHP helpers › Blade directives](php-helpers.md#blade-directives)):

```blade
@push('js')
    @alternativeAsset('alternative')
@endpush

@push('css')
    @alternativeCss('alternative')
@endpush
```

`alternative.js` must load **after** jQuery, DataTables, Select2 and SweetAlert2.

### CSRF meta tag

`alternative.js` reads the CSRF token from `<meta name="csrf-token">` when it
loads records. Make sure your layout outputs it, for example:

```blade
@section('meta_tags')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
```

## 4. Environment variables

| Variable            | Default                        | Description |
| ------------------- | ------------------------------ | ----------- |
| `LARAVEL_LOG_PATH`  | `storage/logs/laravel.log`     | Path of the log file read by the `laravel.log` viewer. |
