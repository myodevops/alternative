# PHP helpers

[← Back to the README](../README.md)

## MessageHandler

`myodevops\ALTErnative\Tools\MessageHandler` builds the responses that
`alternative.js` shows to the user as alerts (see
[Server messages](datatables.md#server-messages)).

```php
use myodevops\ALTErnative\Tools\MessageHandler;
use myodevops\ALTErnative\Tools\JsonMessageType;

return MessageHandler::jsonMessage(JsonMessageType::OPERATIVE_SUCCESS, 'Record saved.');
```

### `MessageHandler::jsonMessage($type, $message, $infoArray = [])`

| Parameter    | Description |
| ------------ | ----------- |
| `$type`      | One of the `JsonMessageType` constants. |
| `$message`   | Text shown to the user. |
| `$infoArray` | Optional associative array of extra properties added next to `message`. |

Returns an array that Laravel serialises to JSON:

```php
MessageHandler::jsonMessage(JsonMessageType::OPERATIVE_ERROR, 'Code already used.', ['field' => 'code']);
// { "_operativeError": { "message": "Code already used.", "field": "code" } }
```

If `$infoArray` is not an array or object, or `$type` is not one of the
`JsonMessageType` constants, the method returns a `RUNTIME_ERROR` message that
describes the problem. For a wrong type, the original message is kept:

```json
{ "_runtimeError": { "message": "Wrong message type (_operativeSucess) in MessageHandler: User saved." } }
```

### `JsonMessageType`

| Constant            | Value                |
| ------------------- | -------------------- |
| `OPERATIVE_SUCCESS` | `_operativeSuccess`  |
| `OPERATIVE_INFO`    | `_operativeInfo`     |
| `OPERATIVE_WARNING` | `_operativeWarning`  |
| `OPERATIVE_ERROR`   | `_operativeError`    |
| `RUNTIME_ERROR`     | `_runtimeError`      |

## Utils

### `Utils::checkEmptyValues(array $array, array $keysToCheck)`

Returns the first key of `$keysToCheck` that is missing in `$array` or has an
empty value (as with PHP `empty()`), or `null` if all of them have a value.
Handy for validating the data posted by a form:

```php
use myodevops\ALTErnative\Tools\Utils;

$missing = Utils::checkEmptyValues($request->all(), ['name', 'email']);
if ($missing !== null) {
    return MessageHandler::jsonMessage(JsonMessageType::OPERATIVE_ERROR, "The field $missing is required.");
}
```

Note that, as with `empty()`, the string `"0"` counts as empty, so don't use it
for switch fields.

## Setting

`myodevops\ALTErnative\Models\Setting` is a simple key/value store in the
`settings` table of the `altesqlite` database.

| Method | Description |
| ------ | ----------- |
| `Setting::GetValue($keyname)` | Returns the value, or `false` if the key doesn't exist. |
| `Setting::SetValue($keyname, $value)` | Creates or updates the key. Errors are written to the [error log](logging.md). |

```php
use myodevops\ALTErnative\Models\Setting;

Setting::SetValue('last_import', now()->toDateTimeString());
$last = Setting::GetValue('last_import');
```

## Log

`myodevops\ALTErnative\Tools\Log` writes to the application error log. See
[Logging](logging.md).

## Blade directives

| Directive | Output |
| --------- | ------ |
| `@alternativeAsset('name')` | `<script src="/vendor/myodevops/alternative/dist/js/name.js"></script>` |
| `@alternativeCss('name')`   | `<link rel="stylesheet" href="/vendor/myodevops/alternative/dist/css/name.css">` |

Both need the published assets
(`php artisan vendor:publish --tag=laravel-assets`).

## Publish tags

| Tag                  | Publishes |
| -------------------- | --------- |
| `laravel-assets`     | `public/` of the package → `public/vendor/myodevops/alternative/` |
