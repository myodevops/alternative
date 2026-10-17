# Logging

[← Back to the README](../README.md)

ALTErnative provides an application error log and a viewer for Laravel's own
`laravel.log`. Both store their data in the `altesqlite` SQLite database
(`database/altesqlite.sq3`, see [Installation](installation.md#2-the-altesqlite-database)),
so they don't touch your main database.

## Application error log

### Writing

```php
use myodevops\ALTErnative\Tools\Log;

Log::Error(__METHOD__, 'Unable to send the invoice ' . $invoice->id);
Log::Warning(__METHOD__, 'Customer without e-mail');
Log::Fatal(__METHOD__, $e->getMessage());
Log::Notify(__METHOD__, 'Monthly report sent');
Log::Debug(__METHOD__, 'Payload: ' . json_encode($payload));
```

Each method takes:

| Parameter   | Description |
| ----------- | ----------- |
| `$function` | Where the event happened, typically `__METHOD__`. It is prepended to the message (`"function: message"`). |
| `$message`  | Text of the event. |

Each record also stores the type, the date and time, the user id and an `info`
string with the request URI, the file and line that called the log, and the
client IP address.

### The `errorlogs` table

| Column     | Type      | Content |
| ---------- | --------- | ------- |
| `id`       | integer   | Primary key. |
| `message`  | text      | `"function: message"`. |
| `userid`   | integer   | Id of the user. |
| `type`     | enum      | `warning`, `error`, `fatal`, `notify`, `debug`. |
| `info`     | string    | `<request URI> <calling file:line> <client IP>`, e.g. `</api/users> <UserController.php:42> <127.0.0.1>`. |
| `datetime` | timestamp | Unix time of the event. |

The model is `myodevops\ALTErnative\Models\Errorlog`.

## `laravel.log` viewer

`myodevops\ALTErnative\Tools\ManageLaravelLog` parses `laravel.log` (or the
file set in `LARAVEL_LOG_PATH`) into the `laravellogs` table: one record per
log entry, with its date and time, the message and the stack trace.

The file is parsed again only when it has changed since the previous run (the
modification time is saved in the `settings` table under
`LastLaravelLogProcessDateTime`). The parsing happens automatically when the
viewer requests the list.

## The viewers

The package contains the controllers and the pages for both logs. You only have
to register the routes and create a page for each viewer.

### Routes

The viewers call `/api/errlogs` and `/api/laravellogs`. Register the routes in
`routes/web.php` (and restrict them, since the logs can contain sensitive data):

```php
use myodevops\ALTErnative\Controllers\ErrLogsController;
use myodevops\ALTErnative\Controllers\LaravelLogsController;

Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('errlogs', [ErrLogsController::class, 'index']);
    Route::get('errlogs/{id}', [ErrLogsController::class, 'show']);
    Route::get('laravellogs', [LaravelLogsController::class, 'index']);
    Route::get('laravellogs/{id}', [LaravelLogsController::class, 'show']);
});
```

### Pages

`<x-alternative-errlog-datatable />` and `<x-alternative-laravellog-datatable />`
define the `content` and `js` sections of the page by themselves, so the page
needs only:

```blade
{{-- resources/views/errlogs.blade.php --}}
@extends('adminlte::page')
@section('title', 'Error log')

<x-alternative-errlog-datatable />
```

```blade
{{-- resources/views/laravellogs.blade.php --}}
@extends('adminlte::page')
@section('title', 'Laravel log')

<x-alternative-laravellog-datatable />
```

```php
Route::middleware('auth')->group(function () {
    Route::view('/errlogs', 'errlogs');
    Route::view('/laravellogs', 'laravellogs');
});
```

Serve these pages from a top-level URL (like `/errlogs`): the detail form loads
`api/errlogs/{id}` with a path relative to the page.

| Viewer | Columns | Detail (info button) |
| ------ | ------- | -------------------- |
| Error log | id, type, date/time, message, user id | full message |
| Laravel log | date/time, message | stack trace |

Both are read-only (no insert, edit or delete) and are ordered by date,
newest first.
