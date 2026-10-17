# JavaScript API

[← Back to the README](../README.md)

`alternative.js` defines a global object **`myo`** with two namespaces:

- **`myo.UI`**: DataTable wiring, form binding, alerts;
- **`myo.WS`**: AJAX calls to the server.

Dependencies: jQuery, DataTables (with Buttons), Bootstrap 4 modals, Select2,
SweetAlert2 (`Swal`). See [Installation](installation.md#3-front-end-assets).

> The calls in `myo.WS` are **synchronous** (`async: false`): they return the
> result directly and block the page until the server answers.

## `myo.UI`

### DataTables

| Function | Description |
| -------- | ----------- |
| `initializeDataTable(tableId)` | Prepares the table with id `tableId`. Call it before `$('#tableId').DataTable({...})`. See [DataTables](datatables.md#what-initializedatatable-does). |
| `OnCreatedRow(row, data)` | Pass it as the DataTables `createdRow` option. It connects the edit / delete / info buttons of the row to the forms set with `onModifyActionForm`, `onDeleteActionForm` and `onInfoActionForm`, and hides the buttons that have no form. |

### Opening forms from your own code

| Function | Description |
| -------- | ----------- |
| `onClickModifyDataTable(formId, keyValues)` | Loads the record into the form `formId` and opens it. `keyValues` is an array with the values of the placeholders of the form `actionread`, in order. |
| `onClickInfoDataTable(formId, keyValues)` | Same as above, for the info form. |
| `onClickInsertDataTable(formId)` | Clears the form `formId` and opens it to insert a new record. |
| `onClickDeleteDataTable(confirmId, keyValues)` | Fills the placeholders of the `action` of the confirm modal `confirmId` and opens it. |

```js
// open the user form on the record with id 42
myo.UI.onClickModifyDataTable('userForm', [42]);
```

### Form binding

These functions work on the controls that have a `data-fieldname` attribute
(see [Form components](form-components.md#how-fields-are-bound-to-records)).

| Function | Description |
| -------- | ----------- |
| `populateFormFields(formId, keyValues)` | Reads the record from the form `actionread` (placeholders replaced by `keyValues`) and fills the controls. With an empty `keyValues` it clears the form instead. |
| `populateFields(formElement, data)` | Fills the controls of `formElement` (a DOM element) from the object `data`. Handles inputs, switches, textareas, static selects and AJAX Select2 (calls `{api}/{value}` to get the labels). Triggers a `change` event on switches and selects. |
| `clearFields(formElement, keys)` | Empties all the bound controls. |
| `generateDataPost(formId)` | Returns an object `{ fieldname: value, ... }` with the current values of the form controls. Switches become `"1"`/`"0"`, multiple selections are joined with `"|"`. |
| `getListKeys(elementId, datasetName)` | Returns the placeholder names (`{...}`) found in the `data-<datasetName>` attribute of the element, e.g. `['id']`. |
| `replaceKeys(url, keyNames, keyValues)` | Replaces every `{key}` placeholder of `url` with the value in the same position of `keyValues`, encoded with `encodeURIComponent`. E.g. `replaceKeys('/api/x/{a}/{b}', ['a','b'], [1,2])` → `/api/x/1/2`. |
| `getObjKeys(keyNames, data)` | Returns an object with only the properties of `data` listed in `keyNames`. |
| `changeField(field)` | Dispatches a `change` event on the control. |
| `validateEmailField(field, event)` | Validates an e-mail control; on error raises an alert and keeps the focus. Used by `input-email`. |

### Alerts

| Member | Description |
| ------ | ----------- |
| `alertType` | Enumeration: `OperativeSuccess` (0), `OperativeInfo` (1), `OperativeWarning` (2), `OperativeError` (3), `RuntimeError` (4). |
| `raiseAlert(type, message)` | Shows a SweetAlert2 message of the given type. |
| `processAlert(response)` | Looks for a [server message](datatables.md#server-messages) in `response`, shows it, and returns `false` for blocking messages (errors), `true` otherwise. |

```js
myo.UI.raiseAlert(myo.UI.alertType.OperativeWarning, 'Select a row first.');
```

## `myo.WS`

All the functions send `token` as the `_token` parameter, for Laravel's CSRF
verification. Use `'{{ csrf_token() }}'` in Blade or read it from the meta tag:

```js
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
```

| Function | Description | Returns |
| -------- | ----------- | ------- |
| `callReadRequest(url, token)` | `GET url`. Processes any message in the response. | An object with `success` (boolean) and `data` (the response). |
| `callUpdateRequest(formId, url, token)` | `POST url` with the values of the form `formId` (see `generateDataPost`). Processes the message in the response. | `true` if successful, `false` on error. |
| `callDeleteRequest(url, token)` | `DELETE url`. Processes the message in the response. | `true` if successful, `false` on error. |

```js
const r = myo.WS.callReadRequest('/api/users/42', token);
if (r.success) {
    console.log(r.data.name);
}
```

## Other members

| Member | Description |
| ------ | ----------- |
| `dttable` (global) | The DataTable instance prepared by the last `initializeDataTable()` call. |
