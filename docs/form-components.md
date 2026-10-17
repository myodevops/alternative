# Form components

[← Back to the README](../README.md)

All components are registered with the `alternative` prefix, so the component
`input-email` is used as `<x-alternative-input-email ... />`. Each one wraps an
AdminLTE component and adds what [`alternative.js`](javascript.md) needs to read
and write records automatically.

## How fields are bound to records

Every input component accepts a **`fieldname`** attribute. It is rendered as
`data-fieldname="..."` on the HTML control, and it is the name of the record
field the control is bound to:

- when a [modal form](#modal-form) loads a record, each control with a
  `data-fieldname` receives the value of the JSON property with the same name;
- when the form is submitted, the request body contains one parameter per control,
  named after its `fieldname`.

Controls without a `fieldname` are ignored by the binding. The `name` attribute
is only the HTML name/id of the control and can be anything unique on the page.

## Common attributes of the input components

`input`, `input-email`, `input-url`, `input-number`, `input-date`,
`input-datetime`, `input-time`, `input-textarea`, `input-checkbox` and `option`
share these attributes:

| Attribute          | Required | Default | Description |
| ------------------ | -------- | ------- | ----------- |
| `name`             | yes      |         | HTML name and id of the control. |
| `label`            | yes      |         | Label shown above the control. HTML entities are decoded. |
| `placeholder`      | yes      |         | Placeholder text. |
| `fieldname`        | no       | `""`    | Record field bound to the control (see above). |
| `disabled`         | no       | not set | Disables the control. It's a flag: writing `disabled` (with any value) disables it. |
| `prepend-slot-class` | no     | `""`    | CSS classes of an icon shown before the control, e.g. `fas fa-user`. |
| `append-slot-class`  | no     | `""`    | CSS classes of an icon shown after the control. |

`input-textarea` and `input-checkbox` don't support the slot classes.

## Input components

### `input`

Plain text input (`<x-adminlte-input>`).

```blade
<x-alternative-input name="iName" label="Name" placeholder="Full name"
                     fieldname="name" prepend-slot-class="fas fa-user" />
```

### `input-email`

Text input validated as an e-mail address when it changes. An invalid value
raises an error alert and keeps the focus on the field.

```blade
<x-alternative-input-email name="iEmail" label="E-mail" placeholder="name@example.com" fieldname="email" />
```

### `input-url`

Input with URL semantics.

```blade
<x-alternative-input-url name="iSite" label="Web site" placeholder="https://" fieldname="website" />
```

### `input-number`

Numeric input (`type="number"`).

| Extra attribute | Default         | Description |
| --------------- | --------------- | ----------- |
| `min`           | `0`             | Minimum value. |
| `max`           | `PHP_FLOAT_MAX` | Maximum value. |

```blade
<x-alternative-input-number name="iQty" label="Quantity" placeholder="0" fieldname="qty" min="1" max="999" />
```

### `input-date`

Single date picker (`<x-adminlte-date-range>` in single-date mode), format
`DD-MM-YYYY`, initialised to today. Shows a calendar icon unless
`prepend-slot-class` is given.

```blade
<x-alternative-input-date name="iBirth" label="Birth date" placeholder="DD-MM-YYYY" fieldname="birth_date" />
```

### `input-datetime`

Single date-and-time picker, 24-hour format with seconds:
`DD-MM-YYYY HH:mm:ss`.

```blade
<x-alternative-input-datetime name="iStart" label="Start" placeholder="DD-MM-YYYY HH:mm:ss" fieldname="starts_at" />
```

The values are exchanged as text in the format above, so the server has to
read and send dates in the same format.

### `input-time`

Time input (`type="time"`).

```blade
<x-alternative-input-time name="iOpen" label="Opening time" placeholder="HH:MM" fieldname="opens_at" />
```

### `input-textarea`

Multi-line text (`<x-adminlte-textarea>`).

| Extra attribute | Default | Description |
| --------------- | ------- | ----------- |
| `rows`          | `2`     | Number of visible rows. |
| `wrap`          | `on`    | `on` wraps long lines, `off` scrolls horizontally. |

```blade
<x-alternative-input-textarea name="iNotes" label="Notes" placeholder="Notes" fieldname="notes" rows="5" />
```

### `input-checkbox`

On/off switch (`<x-adminlte-input-switch>`, Bootstrap Switch). The value is
exchanged as the string **`"1"`** (on) or **`"0"`** (off).

| Extra attribute  | Default   | Description |
| ---------------- | --------- | ----------- |
| `dataoncolor`    | `success` | Bootstrap color of the "on" state. |
| `dataoffcolor`   | `danger`  | Bootstrap color of the "off" state. |
| `dataontext`     | `""`      | Text of the "on" state. |
| `dataofftext`    | `""`      | Text of the "off" state. |

```blade
<x-alternative-input-checkbox name="iActive" label="Active" placeholder="" fieldname="active"
                              dataontext="Yes" dataofftext="No" />
```

### `option`

Select box based on Select2 (`<x-adminlte-select2>`). The options come either
from a static array or from an API.

| Extra attribute | Default      | Description |
| --------------- | ------------ | ----------- |
| `options`       | `[]`         | Static options as an associative array `value => text`. Use the `:options` syntax to pass a PHP array. |
| `api`           | `""`         | URL of a Select2 AJAX data source. When set, `options` is ignored. |
| `withsearch`    | not set      | With `api`: shows the search box (when there are at least 5 results). Without it the search box is hidden. |
| `multiple`      | not set      | Flag: allows multiple selection. |
| `labelclass`    | `text-black` | CSS class of the label. |

Static options:

```blade
<x-alternative-option name="iRole" label="Role" placeholder="Choose a role" fieldname="role"
                      :options="['admin' => 'Administrator', 'user' => 'User']" />
```

AJAX options:

```blade
<x-alternative-option name="iCity" label="City" placeholder="Choose a city" fieldname="city_id"
                      api="/api/cities" withsearch />
```

The API at `api` must implement two calls:

- `GET {api}?term=...`: the standard Select2 AJAX call. Return the Select2 format
  `{ "results": [ { "id": 1, "text": "Rome" }, ... ] }`.
- `GET {api}/{value}`: called when a record is loaded into the form, to get the
  label of the selected value. Return `{ "text": "Rome" }`.

With `multiple`, the values are exchanged as one string with the values
separated by `|` (for example `"3|7|12"`). When loading a record, the server can
also return the values as a JSON array (`[3, 7, 12]`), for both static and AJAX
selects; an empty value selects nothing.

### `input-key`

Hidden field that holds the primary key of the record shown in a modal form.
Every [modal form](#modal-form) that edits records should contain one.

| Attribute   | Required | Description |
| ----------- | -------- | ----------- |
| `fieldname` | yes      | Name of the key field, e.g. `id`. |

```blade
<x-alternative-input-key fieldname="id" />
```

When the form is opened to insert a new record, the key field is cleared, so the
server receives an empty key. Use this to tell an insert from an update.

## Containers

### `modal-form`

A modal dialog (`<x-adminlte-modal>`) that loads a record, lets the user edit
it, and submits it. Usually it is opened by the edit, info or insert button of a
[DataTable](datatables.md), but it can also be opened from your own code with
[`myo.UI.onClickModifyDataTable()`](javascript.md#opening-forms-from-your-own-code).

| Attribute     | Required | Default | Description |
| ------------- | -------- | ------- | ----------- |
| `id`          | yes      |         | Id of the modal. The DataTable options refer to it. |
| `title`       | yes      |         | Title of the modal. |
| `actionread`  | no       | `#`     | URL used to load the record. Write the key in braces, e.g. `/api/users/{id}`: the placeholder is replaced with the value of the field `id` of the selected row. |
| `actionwrite` | no       | `#`     | URL the form data is `POST`ed to on submit. |
| `method`      | no       | `get`   | Use `modify` to enable saving; any other value makes the Submit button do nothing. The built-in viewers use `read`. |
| `readonly`    | no       | `false` | When set, the footer only shows an **Ok** button that closes the modal. |
| `theme`       | no       | `light` | `light`, `danger`, `info`, `success`, `warning`… |
| `size`        | no       | `lg`    | Width: `sm`, `lg` or `xl`. |

```blade
<x-alternative-modal-form id="userForm" title="User" method="modify"
                          actionread="/api/users/{id}" actionwrite="/api/users">
    <x-alternative-input-key fieldname="id" />
    <div class="row">
        <div class="col"><x-alternative-input name="iName" label="Name" placeholder="Name" fieldname="name" /></div>
        <div class="col"><x-alternative-input-email name="iEmail" label="E-mail" placeholder="E-mail" fieldname="email" /></div>
    </div>
</x-alternative-modal-form>
```

When **Submit** is pressed (with `method="modify"`):

1. the values of all bound controls are posted to `actionwrite` together with `_token`;
2. the response is processed as a [message](datatables.md#server-messages);
3. if the response is not an error, the modal is closed and the DataTable is reloaded.

The modal uses a static backdrop: clicking outside it doesn't close it.

### `modal-confirm`

A small confirmation dialog with **Ok** and **Cancel** buttons. With
`method="delete"` it's the delete confirmation of a DataTable.

| Attribute     | Required | Default  | Description |
| ------------- | -------- | -------- | ----------- |
| `id`          | yes      |          | Id of the modal. |
| `action`      | no       | `#`      | URL called when the user confirms. Placeholders in braces (`/api/users/{id}`) are replaced with the values of the selected row. |
| `method`      | no       | `get`    | Only `delete` triggers a request (HTTP `DELETE`). |
| `theme`       | no       | `light`  | Color theme. |
| `oklabel`     | no       | `Ok`     | Label of the confirm button (translated with `__()`). |
| `cancellabel` | no       | `Cancel` | Label of the cancel button (translated with `__()`). |

The content of the component is the message shown to the user:

```blade
<x-alternative-modal-confirm id="userDelete" action="/api/users/{id}" method="delete" theme="danger">
    Do you really want to delete this user?
</x-alternative-modal-confirm>
```

On a successful `DELETE` the modal is closed and the current page of the table is
reloaded from the server. If the request fails, the error message is shown and
the modal stays open.

### `modal-message`

A small informational modal with a single **Ok** button.

| Attribute | Required | Default | Description |
| --------- | -------- | ------- | ----------- |
| `id`      | yes      |         | Id of the modal. |
| `theme`   | no       | `light` | Color theme. |

```blade
<x-alternative-modal-message id="infoMsg">The operation was completed.</x-alternative-modal-message>
```

Open it with `$('#infoMsg').modal('show')`.

### `modeless-form`

A form rendered inline in the page as an AdminLTE card (`<x-adminlte-card>`),
not as a modal. It loads its record when the page is ready and saves it with
**Submit**. Typical use: a settings or profile page that edits one record.

| Attribute     | Required | Default      | Description |
| ------------- | -------- | ------------ | ----------- |
| `id`          | yes      |              | Id of the card. |
| `title`       | yes      |              | Title of the card. |
| `actionread`  | no       | `#`          | URL called with `GET` at page load to fill the fields. It takes no placeholders: write the complete URL. |
| `actionwrite` | no       | `#`          | URL the data is posted to on submit. |
| `method`      | no       | `get`        | Use `modify` to enable saving. |
| `theme`       | no       | `light`      | Card theme. |
| `bodyclass`   | no       | `""`         | Extra classes for the card body. |
| `headerclass` | no       | `bg-success` | Extra classes for the card header. |
| `footerclass` | no       | `bg-light`   | Extra classes for the card footer. |
| `icon`        | no       | `""`         | Icon of the card header. |
| `collapsible` | no       | `null`       | Card tool: collapse button. |
| `removable`   | no       | `null`       | Card tool: remove button. |
| `maximizable` | no       | `null`       | Card tool: maximize button. |

```blade
<x-alternative-modeless-form id="profileForm" title="My profile" method="modify"
                             actionread="/api/profile" actionwrite="/api/profile">
    <x-alternative-input name="iName" label="Name" placeholder="Name" fieldname="name" />
    <x-alternative-input-email name="iEmail" label="E-mail" placeholder="E-mail" fieldname="email" />
</x-alternative-modeless-form>
```

The **Cancel** button has no behavior attached; add your own handler if you need it.

## Tables

### `datatable`

Renders an empty AdminLTE DataTable with the standard ALTErnative look (light
theme, dark header, bordered, compressed, striped, hoverable, with buttons).
You then initialise it in JavaScript: see [DataTables and CRUD pages](datatables.md).

| Attribute | Required | Description |
| --------- | -------- | ----------- |
| `name`    | yes      | Id of the table. |
| `heads`   | yes      | Column headers, in the format of `<x-adminlte-datatable>` (strings, or arrays like `['label' => 'ID', 'no-export' => true]`). Use `:heads` to pass a PHP array. |

```blade
<x-alternative-datatable name="users_table" :heads="['ID', 'Name', 'E-mail', 'Actions']" />
```

### `datatable-actions`

The edit / delete / info buttons shown in the **actions** column of each row.
You don't normally use it directly: the server-side helper adds it to every
record (see [DataTables](datatables.md#3-the-model)).

The buttons have no `id`; they are identified by the attribute
`data-dtaction="modify"`, `"delete"` or `"info"`, which you can also use in your
CSS or scripts (e.g. `#users_table [data-dtaction="delete"]`).

### `errlog-datatable` and `laravellog-datatable`

Complete viewers for the application error log and for `laravel.log`. See
[Logging](logging.md).
