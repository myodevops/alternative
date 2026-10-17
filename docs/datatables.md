# DataTables and CRUD pages

[← Back to the README](../README.md)

The main use of ALTErnative is the classic back-office page: a paginated table
of records loaded from the server, with buttons to insert, edit, inspect and
delete records through modal forms. This page shows how to build one from end
to end, using a `users` table as the example.

## How it works

```
Browser                                             Laravel
───────                                             ───────
DataTable ── GET /api/users?draw=…&start=…  ───────▶ UserController@index
          ◀──── { recordsTotal, recordsFiltered, data } ── $model->getData(...)

Edit button ─ GET /api/users/{id} ─────────────────▶ UserController@show
modal-form  ◀──── { id, name, email, … }

Submit ───── POST /api/users  (fields + _token) ───▶ UserController@store
          ◀──── MessageHandler::jsonMessage(...)

Delete ───── DELETE /api/users/{id} ───────────────▶ UserController@destroy
          ◀──── MessageHandler::jsonMessage(...)
```

## 1. Routes

The calls are AJAX calls from pages of the same application, so register
them in `routes/web.php` behind the `auth` middleware. This way they use the
session cookie and the CSRF token that `alternative.js` sends:

```php
use App\Http\Controllers\UserController;

Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::post('users', [UserController::class, 'store']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
});

Route::middleware('auth')->get('/users', fn () => view('users'));
```

## 2. The Blade page

```blade
@extends('adminlte::page')

@section('title', 'Users')

@section('content')
    {{-- Form used to insert and edit a record --}}
    <x-alternative-modal-form id="userForm" title="User" method="modify"
                              actionread="/api/users/{id}" actionwrite="/api/users">
        <x-alternative-input-key fieldname="id" />
        <div class="row">
            <div class="col"><x-alternative-input name="iName" label="Name" placeholder="Name" fieldname="name" /></div>
            <div class="col"><x-alternative-input-email name="iEmail" label="E-mail" placeholder="E-mail" fieldname="email" /></div>
        </div>
        <x-alternative-input-checkbox name="iActive" label="Active" placeholder="" fieldname="active" />
    </x-alternative-modal-form>

    {{-- Confirmation used by the delete button --}}
    <x-alternative-modal-confirm id="userDelete" action="/api/users/{id}" method="delete" theme="danger">
        Delete the selected user?
    </x-alternative-modal-confirm>

    {{-- The table --}}
    <x-alternative-datatable name="users_table" :heads="[
        ['label' => 'ID', 'no-export' => true],
        'Name',
        'E-mail',
        ['label' => 'Actions', 'no-export' => true],
    ]" />
@endsection

@section('js')
@parent
<script>
$(document).ready(function () {
    myo.UI.initializeDataTable('users_table');      // must be called BEFORE .DataTable()

    $('#users_table').DataTable({
        processing: true,
        serverSide: true,
        bDestroy: true,
        ajax: '/api/users',
        createdRow: myo.UI.OnCreatedRow,            // wires the row buttons
        order: [[1, 'asc']],

        // ALTErnative options
        onModifyActionForm: 'userForm',
        onDeleteActionForm: 'userDelete',
        insertAllowed: true,

        columns: [
            { data: 'id', visible: false, searchable: false },
            { data: 'name' },
            { data: 'email' },
            { data: 'actions', searchable: false, orderable: false },
        ],
        dom: '<"container-fluid"<"row"<"col"B><"col-md-2"l><"col"f>>>rtip',
    });
});
</script>
@endsection
```

Keep `@parent` in the `js` section: the modal components push their own
scripts into the same section.

### ALTErnative DataTable options

These options are passed to `.DataTable({...})` together with the standard
DataTables options.

| Option                    | Description |
| ------------------------- | ----------- |
| `createdRow`              | Must be `myo.UI.OnCreatedRow`: it connects the buttons of each row to the forms below. |
| `onModifyActionForm`      | Id of the `modal-form` opened by the **edit** (pencil) button. |
| `onDeleteActionForm`      | Id of the `modal-confirm` opened by the **delete** (trash) button. |
| `onInfoActionForm`        | Id of the `modal-form` opened by the **info** button. Usually a `readonly` form. |
| `onClickInfoActionCustom` | Name (string) of a global function to call instead of the default info behavior, also as a dotted path like `'myApp.showInfo'`. It receives `(formId, [keyValues])`. |
| `insertAllowed`           | `true` adds a **+** button to the toolbar. |
| `onInsertActionForm`      | Id of the form opened by the **+** button. If missing, `onModifyActionForm` is used. |

A row button whose form is not configured (or doesn't exist in the page) is hidden.

### What `initializeDataTable()` does

- removes the default DataTables buttons (copy, CSV, print…) and adds a **reload** button;
- adds the **+** (insert) button when `insertAllowed` is true;
- makes rows selectable with a click (one selected row at a time, class `selected`);
- stores the DataTable instance in the global variable `dttable`, which the
  modal forms use to reload the table after a write or a delete.

Because of `dttable`, use **one** ALTErnative DataTable per page.

### Keys and placeholders

URLs in `actionread` (modal forms) and `action` (modal confirm) can contain
placeholders in braces, like `/api/users/{id}`. When a row button is pressed,
each placeholder is replaced with the value of the **row data property with
the same name**, so `{id}` needs an `id` property in the JSON row, even if the
column is hidden. The values are URL-encoded, and a URL can have more than one
placeholder for composite keys, like `/api/prices/{list}/{item}`.

## 3. The model

The model implements `AdminLteDataTableManageable` and uses the
`AdminLteDataTableManage` trait, which adds the `getData()` method.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use myodevops\ALTErnative\Views\Components\Form\Traits\AdminLteDataTableManage;
use myodevops\ALTErnative\Views\Components\Form\Traits\AdminLteDataTableManageable;

class User extends Model implements AdminLteDataTableManageable
{
    use AdminLteDataTableManage;
    // ...
}
```

### `getData($get, $query, $jsonRes, $requerable = true)`

Implements the DataTables
[server-side processing](https://datatables.net/manual/server-side) protocol.

| Parameter     | Description |
| ------------- | ----------- |
| `$get`        | The request parameters sent by DataTables (`$request->query()`). |
| `$query`      | The query that selects the records. It can contain joins, selects with aliases, `where` clauses… |
| `$jsonRes`    | Fully-qualified class name of a `JsonResource` that shapes each row. |
| `$requerable` | `true` wraps `$query` in a sub-query (`SELECT * FROM (...) AS alternativepackagealias`), so the search and order can work on aliased columns of joins. Use `true` with a `DB::table()` query builder and `false` with a simple Eloquent query. |

What it does:

1. checks that `start`, `length`, `search[value]` and `columns` are present (otherwise it
   returns `{ "error": "..." }`, which DataTables shows to the user as a warning);
2. counts the records (`recordsTotal`);
3. if a search text is set, applies `LIKE %text%` (OR-ed) to every column whose
   `searchable` is not `false`, and counts again (`recordsFiltered`);
4. orders by the requested column;
5. applies `start` / `length` paging;
6. adds an `actions` property with the row buttons to every record;
7. returns `{ recordsTotal, recordsFiltered, data }`, with `data` built through `$jsonRes`.

Because of step 3, mark as `searchable: false` every column that is not a real
column of the query, at least `actions`.

## 4. The resource

The resource selects (and can transform) the fields sent to the browser. It
**must** include `actions`, and every field used as a key placeholder.

```php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'email'   => $this->email,
            'actions' => $this->actions,
        ];
    }
}
```

## 5. The controller

```php
namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UsersResource;
use Illuminate\Http\Request;
use myodevops\ALTErnative\Tools\MessageHandler;
use myodevops\ALTErnative\Tools\JsonMessageType;
use myodevops\ALTErnative\Tools\Utils;

class UserController extends Controller
{
    // List for the DataTable
    public function index(Request $request)
    {
        $user = new User();

        return response()->json(
            $user->getData($request->query(), $user->query(), UsersResource::class, false)
        );
    }

    // Record for the modal form: a flat object, one property per fieldname
    public function show($id)
    {
        return User::findOrFail($id);
    }

    // Insert (empty key) or update
    public function store(Request $request)
    {
        $missing = Utils::checkEmptyValues($request->all(), ['name', 'email']);
        if ($missing !== null) {
            return MessageHandler::jsonMessage(JsonMessageType::OPERATIVE_ERROR, "The field $missing is required.");
        }

        $user = $request->filled('id') ? User::findOrFail($request->id) : new User();
        $user->name   = $request->name;
        $user->email  = $request->email;
        $user->active = $request->active === '1';
        $user->save();

        return MessageHandler::jsonMessage(JsonMessageType::OPERATIVE_SUCCESS, 'User saved.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return MessageHandler::jsonMessage(JsonMessageType::OPERATIVE_SUCCESS, 'User deleted.');
    }
}
```

A query with joins and aliases uses the query builder and `$requerable = true`:

```php
$query = DB::table('orders')
    ->join('customers', 'customers.id', '=', 'orders.customer_id')
    ->select('orders.id', 'orders.number', 'customers.name as customer');

return response()->json(
    (new Order())->getData($request->query(), $query, OrdersResource::class, true)
);
```

Here the DataTable columns can use `data: 'customer'`, and search and order work
on the alias.

## Server messages

The responses of the write calls (and optionally of the read calls) can carry a
message for the user. Build it with
[`MessageHandler::jsonMessage()`](php-helpers.md#messagehandler):

```json
{ "_operativeSuccess": { "message": "User saved." } }
```

| Type (`JsonMessageType::…`) | JSON key             | Shown as                           | Blocks the operation |
| --------------------------- | -------------------- | ---------------------------------- | -------------------- |
| `OPERATIVE_SUCCESS`         | `_operativeSuccess`  | success toast, closes after 1.5 s  | no  |
| `OPERATIVE_INFO`            | `_operativeInfo`     | info dialog                        | no  |
| `OPERATIVE_WARNING`         | `_operativeWarning`  | warning dialog                     | no  |
| `OPERATIVE_ERROR`           | `_operativeError`    | error dialog                       | **yes** |
| `RUNTIME_ERROR`             | `_runtimeError`      | error dialog with `!` icon         | **yes** |

"Blocks the operation" means that the modal stays open and the table is not
reloaded, so the user can correct the data. A response without any of these
keys is treated as a success. An HTTP error response is shown as a runtime
error with the response text.

## Request and response contract

| Call | Method | Sent by the browser | Expected response |
| ---- | ------ | ------------------- | ----------------- |
| table `ajax` | `GET` | DataTables parameters | `getData()` result |
| `actionread` | `GET` | `_token` | Flat JSON object; property names = `fieldname`s. Optionally a message key. |
| `actionwrite` | `POST` | one parameter per bound control (`fieldname` → value) + `_token` | Message (see above) |
| `action` (delete) | `DELETE` | `_token` | Message (see above) |

Values are exchanged as strings: switches as `"1"`/`"0"`, multiple selects as
`"a|b|c"`, dates as `DD-MM-YYYY` / `DD-MM-YYYY HH:mm:ss`.
