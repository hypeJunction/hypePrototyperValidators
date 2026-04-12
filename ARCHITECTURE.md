# hypePrototyperValidators — Architecture (Elgg 4.x)

## Purpose

Adds server-side and client-side (Parsley.js) validation rules to the
[hypePrototyper](../hypePrototyper) form/entity prototyping framework.
Registers validation handlers for `type`, `min`, `max`, `minlength`,
`maxlength`, `contains`, and `regex` rules.

## Directory layout

```
hypePrototyperValidators/
├── classes/hypeJunction/PrototyperValidators/
│   └── Bootstrap.php          # Plugin bootstrap (registers validation rules with hypePrototyper)
├── lib/
│   └── hooks.php              # Procedural hook handlers (validate:* family + input_vars)
├── views/default/
│   ├── prototyper/elements/
│   │   ├── validation.php     # Help text for fields with validation rules
│   │   └── js_validation.php  # Inline JS to enable Parsley validation
│   └── js/framework/
│       ├── prototyper_validation.js
│       └── legacy/prototyper_validation.php
├── languages/en.php           # Returns translation array (Elgg 3.x+ format)
├── vendor/respect/validation/ # Vendored validation library (^2.0)
├── vendors/parsley/           # Client-side Parsley.js bundle
├── tests/                     # PHPUnit pre-migration suite (54 tests)
├── elgg-plugin.php            # Plugin manifest (declarative hooks + view extensions)
└── composer.json              # Plugin metadata + composer name lowercase, installer-name preserves camelCase dir
```

## Plugin manifest (`elgg-plugin.php`)

| Key                | Description |
|--------------------|-------------|
| `bootstrap`        | `hypeJunction\PrototyperValidators\Bootstrap` — registers validation rules with hypePrototyper config |
| `hooks`            | Eight `validate:*` and one `input_vars` hook handlers, all on type `prototyper` |
| `view_extensions`  | Extends `prototyper/elements/help` with `validation`; extends `prototyper/input/before` with `js_validation` |

There are no entities, actions, routes, capabilities, settings, or
notifications declared by this plugin.

## Registered hook handlers

All hook callbacks live in `lib/hooks.php` (procedural functions, declared
with the legacy `($hook, $type, $value, $params)` signature which Elgg 4.x
still accepts).

| Hook              | Type        | Handler                              |
|-------------------|-------------|--------------------------------------|
| `validate:type`   | `prototyper`| `prototyper_validate_type`           |
| `validate:min`    | `prototyper`| `prototyper_validate_min`            |
| `validate:max`    | `prototyper`| `prototyper_validate_max`            |
| `validate:minlength`  | `prototyper`| `prototyper_validate_minlength`  |
| `validate:maxlength`  | `prototyper`| `prototyper_validate_maxlength`  |
| `validate:contains`   | `prototyper`| `prototyper_validate_contains`   |
| `validate:regex`      | `prototyper`| `prototyper_validate_regex`      |
| `input_vars`          | `prototyper`| `prototyper_filter_input_view_vars` |

`prototyper_validate_img_dimensions()` exists for image dimension validation
but is not registered through `elgg-plugin.php` — kept for direct call sites.

## Dependencies

| Plugin / Package           | Version    | Why |
|----------------------------|-----------|-----|
| `elgg/elgg`                | `^4.0`    | Core platform |
| `composer/installers`      | `^2.0`    | Required by composer 2.2+ |
| `respect/validation`       | `^2.0`    | PHP 7.4+ compatible (upgraded from `~0.9` which used PHP-reserved `String` class name) |
| `hypejunction/hypeprototyper` | `*`    | Runtime dependency — provides `Field` / `ValidationStatus` classes used by hooks |
| `hypejunction/forms_validation` | suggest | Optional client-side enhancement |

## Migration notes (3.x → 4.x)

### Composer / metadata
- `manifest.xml` removed; `composer.json` is now the only metadata source
- Composer `name` lowercased to `hypejunction/hypeprototypervalidators`
- `extra.installer-name` preserves the camelCase `hypePrototyperValidators`
  directory used by Bodyology and other deployments
- `elgg/elgg: ^4.0`, `composer/installers: ^2.0` added to `require`
- `hypejunction/hypeprototyper` moved from `require-dev` to `require`
  (it is a hard runtime dependency)

### Plugin entry point
- Old `start.php` removed (was already replaced in earlier 3.x commits)
- `elgg-plugin.php` declares hooks and view extensions
- `Bootstrap` class wires up validation rules with the hypePrototyper config
  service and registers the Parsley JS asset via `elgg_define_js`
  (still valid in 4.x; deprecated/removed in 6.x)

### Dependency upgrade
- `respect/validation` upgraded `~0.9` → `^2.0` (separate concern from Elgg
  upgrade but blocked the test suite on PHP 7+ until done)
- API renames in `lib/hooks.php`:
  - `v::numeric()` → `v::numericVal()`
  - `v::intType()` → `v::intVal()` (loose validation against string input
    that comes from `get_input()`)
- Other validators (`stringType`, `alnum`, `alpha`, `date`, `length`,
  `contains`, `regex`, `filterVar`, `min`, `max`) work unchanged

### Hook callback signatures
- Hook handlers still use the legacy `($hook, $type, $value, $params)`
  signature, which Elgg 4.x supports with a deprecation notice. The 4→5
  migration will need to convert these to `\Elgg\Hook` typed callbacks
  (still 4.x style) or `\Elgg\Event` (5.x).

### No data migration required
- This plugin stores no entities, metadata, or settings of its own
- It only registers in-memory hook handlers and view extensions
- No `Elgg\Upgrade\Batch` script is needed

## Tests

PHPUnit integration suite at `tests/phpunit/integration/...ValidationHooksTest.php`
locks in behavior of all hook handlers and the view extensions:

- 54 tests, 332+ assertions
- Tests pass against Elgg 4.x with the dependency upgrades described above
- The `IntegrationTestCase::up()` skip guard for `respect/validation ~0.9`
  has been removed since the dependency is now `^2.0`
