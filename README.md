# ALTErnative

**ALTErnative** is a Laravel package that builds on top of
[jeroennoten/laravel-adminlte](https://github.com/jeroennoten/Laravel-AdminLTE).
It adds ready-to-use Blade components, a small JavaScript runtime and a few PHP
helpers so that the typical back-office CRUD page (a server-side DataTable with a
modal form to insert, edit, inspect and delete records) can be written with very
little code.

It also ships with:

- an **application error log**, stored in a dedicated SQLite database, with a
  ready-made viewer;
- a viewer for the **`laravel.log`** file.

## Requirements

| Dependency                      | Version     |
| ------------------------------- | ----------- |
| PHP                             | >= 8.2      |
| Laravel (`illuminate/support`)  | ^11.0       |
| `jeroennoten/laravel-adminlte`  | >= 3.8.5    |
| PHP extension `pdo_sqlite`      | any         |

On the browser side, the components rely on the AdminLTE plugins for
DataTables (with the Buttons extension), Select2, Date Range Picker,
Bootstrap Switch and SweetAlert2. See [Installation](docs/installation.md).

## Quick start

```bash
composer require myodevops/alternative
php artisan vendor:publish --tag=laravel-assets
php artisan migrate
```

Then load `alternative.js` in your AdminLTE pages (see
[Installation › Front-end assets](docs/installation.md#3-front-end-assets)) and
build your first CRUD page:

```blade
@extends('adminlte::page')

@section('content')
    <x-alternative-modal-form id="userForm" title="User"
                              actionread="/api/users/{id}" actionwrite="/api/users"
                              method="modify">
        <x-alternative-input-key fieldname="id" />
        <x-alternative-input name="iName" label="Name" placeholder="Name" fieldname="name" />
        <x-alternative-input-email name="iEmail" label="E-mail" placeholder="E-mail" fieldname="email" />
    </x-alternative-modal-form>

    <x-alternative-modal-confirm id="userDelete" action="/api/users/{id}" method="delete">
        Delete the selected user?
    </x-alternative-modal-confirm>

    <x-alternative-datatable name="users_table" :heads="['ID', 'Name', 'E-mail', 'Actions']" />
@endsection

@section('js')
@parent
<script>
$(document).ready(function () {
    myo.UI.initializeDataTable('users_table');
    $('#users_table').DataTable({
        processing: true,
        serverSide: true,
        bDestroy: true,
        ajax: '/api/users',
        createdRow: myo.UI.OnCreatedRow,
        onModifyActionForm: 'userForm',
        onDeleteActionForm: 'userDelete',
        insertAllowed: true,
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'actions', searchable: false, orderable: false },
        ],
    });
});
</script>
@endsection
```

The full walkthrough, including the server side, is in
[DataTables and CRUD pages](docs/datatables.md).

## Documentation

| Topic | Content |
| ----- | ------- |
| [Installation](docs/installation.md) | Composer, the `altesqlite` database, assets, AdminLTE plugins |
| [Form components](docs/form-components.md) | Reference of every `<x-alternative-*>` Blade component |
| [DataTables and CRUD pages](docs/datatables.md) | Server-side DataTables, action buttons, modal forms, the request/response contract |
| [JavaScript API](docs/javascript.md) | The `myo.UI` and `myo.WS` objects in `alternative.js` |
| [Logging](docs/logging.md) | Application error log, `laravel.log` viewer, the `altesqlite` database |
| [PHP helpers](docs/php-helpers.md) | `MessageHandler`, `Utils`, `Setting`, Blade directives |

## Changelog

See [changelog.txt](changelog.txt).

## License

ALTErnative is open-source software licensed under the [MIT license](LICENSE).
