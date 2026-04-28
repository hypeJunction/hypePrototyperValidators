# hypePrototyperValidators — Architecture (Elgg 5.x)

## Purpose

Adds server-side and client-side (Parsley.js) validation rules to the
[hypePrototyper](../hypePrototyper) form/entity prototyping framework.
Registers event handlers for `validate:type`, `validate:min`, `validate:max`,
`validate:minlength`, `validate:maxlength`, `validate:contains`, `validate:regex`,
and `input_vars` events on the `prototyper` type.

## Directory layout

```
hypePrototyperValidators/
├── classes/hypeJunction/PrototyperValidators/
│   └── Bootstrap.php          # Plugin bootstrap — load() requires vendor autoload + lib/hooks.php
├── lib/
│   └── hooks.php              # Procedural event handlers (validate:* family + input_vars)
├── views/default/
│   ├── prototyper/elements/
│   │   ├── validation.php     # Help text for fields with validation rules
│   │   └── js_validation.php  # Inline JS to enable Parsley validation
│   └── js/framework/
│       ├── prototyper_validation.js
│       └── legacy/prototyper_validation.php
├── languages/en.php           # Returns translation array (return $array; format)
├── vendor/respect/validation/ # Vendored validation library (^2.0)
├── vendors/parsley/           # Client-side Parsley.js bundle
├── tests/                     # PHPUnit suite (54 tests, 334 assertions)
├── elgg-plugin.php            # Plugin manifest (declarative events + view extensions)
└── composer.json              # Plugin metadata + composer name lowercase, installer-name preserves camelCase dir
```

## Plugin manifest (`elgg-plugin.php`)

| Key                | Description |
|--------------------|-------------|
| `bootstrap`        | `hypeJunction\PrototyperValidators\Bootstrap` — `load()` method handles autoloading; can implement `init()` for future setup |
| `events`           | Eight `validate:*` and one `input_vars` event handlers, all on type `prototyper` |
| `view_extensions`  | Extends `prototyper/elements/help` with `validation`; extends `prototyper/input/before` with `js_validation` |

There are no entities, actions, routes, capabilities, settings, or
notifications declared by this plugin.

## Registered event handlers

All event callbacks live in `lib/hooks.php` (procedural functions with
`(\Elgg\Event $event)` signature, Elgg 5.x style).

| Event name            | Type        | Handler                              |
|-----------------------|-------------|--------------------------------------|
| `validate:type`       | `prototyper`| `prototyper_validate_type`           |
| `validate:min`        | `prototyper`| `prototyper_validate_min`            |
| `validate:max`        | `prototyper`| `prototyper_validate_max`            |
| `validate:minlength`  | `prototyper`| `prototyper_validate_minlength`      |
| `validate:maxlength`  | `prototyper`| `prototyper_validate_maxlength`      |
| `validate:contains`   | `prototyper`| `prototyper_validate_contains`       |
| `validate:regex`      | `prototyper`| `prototyper_validate_regex`          |
| `input_vars`          | `prototyper`| `prototyper_filter_input_view_vars`  |

`prototyper_validate_img_dimensions()` exists for image dimension validation
but is not registered — kept for direct call sites.

Handler pattern:

```php
function prototyper_validate_type(\Elgg\Event $event) {
    $validation = $event->getValue();
    $params     = $event->getParams();
    $field      = elgg_extract('field', $params);
    // ...
    return $validation;
}
```

## Dependencies

| Plugin / Package              | Version    | Why |
|-------------------------------|-----------|-----|
| `elgg/elgg`                   | `^5.0`    | Core platform |
| `composer/installers`         | `^2.0`    | Required by composer 2.2+ |
| `respect/validation`          | `^2.0`    | PHP 8.2+ compatible |
| `hypejunction/hypeprototyper` | `*`       | Runtime dependency — provides `Field` / `ValidationStatus` classes |

## Migration notes (4.x → 5.x)

### Plugin manifest
- `'hooks'` key renamed to `'events'` in `elgg-plugin.php`
- `require_once` side-effects removed from top of `elgg-plugin.php` (Elgg 5.x
  may cache the plugin config; side effects are unreliable there)

### Bootstrap
- `Bootstrap::load()` added — Elgg 5.x calls this before the plugin config is
  consumed; used to load vendor autoload and `lib/hooks.php`

### Hook handlers → Event handlers
- All 9 handler functions in `lib/hooks.php` converted from
  `($hook, $type, $value, $params)` to `(\Elgg\Event $event)` signature
- `$value` → `$event->getValue()`
- `$params` → `$event->getParams()` / `$event->getParam('key')`

### Infrastructure
- PHP 7.4 → 8.2; MySQL 5.7 → 8.0
- Docker stack updated to `elgg/elgg: ^5.0` and `ELGG_SITE_URL=http://elgg/`

### No data migration required
- This plugin stores no entities, metadata, or settings of its own
- It only registers in-memory event handlers and view extensions
- No `Elgg\Upgrade\Batch` script is needed

## Tests

PHPUnit integration suite at `tests/phpunit/integration/...ValidationHooksTest.php`:

- 54 tests, 334 assertions
- `makeEvent()` helper creates a mock `\Elgg\Event` with stubbed `getValue()`,
  `getParams()`, and `getParam()` so handlers can be called directly
- Registration tests use `_elgg_services()->events->hasHandler()` (was `->hooks`)
