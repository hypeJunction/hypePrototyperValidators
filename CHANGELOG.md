# Changelog

## 5.0.0 — Elgg 5.x migration

### Breaking changes

- Requires Elgg 5.x (`elgg/elgg: ^5.0`) and PHP 8.2+

### Migration

- `elgg-plugin.php`: renamed `'hooks'` key to `'events'` (Elgg 5.x requirement)
- Removed `require_once` side-effects from top of `elgg-plugin.php`; moved to `Bootstrap::load()`
- All 9 event handlers in `lib/hooks.php` converted from the legacy 4-argument
  `($hook, $type, $value, $params)` signature to `(\Elgg\Event $event)` with
  `$event->getValue()` / `$event->getParams()` / `$event->getParam()`
- Docker stack upgraded: PHP 7.4 → 8.2, MySQL 5.7 → 8.0, Elgg 4.x → 5.x install

### Tests

- Updated `ValidationHooksTest`: added `makeEvent()` helper producing a mock
  `\Elgg\Event`; all direct 4-arg function calls converted to event-based calls
- Registration tests switched from `_elgg_services()->hooks` to
  `_elgg_services()->events`

---

## 4.0.0 — Elgg 4.x migration

### Migration

- Removed `manifest.xml`; `composer.json` is the sole plugin metadata source (Elgg 4.x requirement)
- Lowercased composer `name` to `hypejunction/hypeprototypervalidators`; `extra.installer-name` preserves the original camelCase install directory
- Added explicit `elgg/elgg: ^4.0` and `composer/installers: ^2.0` runtime requirements
- Moved `hypejunction/hypeprototyper` from `require-dev` to `require` (hard runtime dependency)
- Added `extra.elgg-plugin.id` for explicit plugin id mapping

### Dependency upgrades

- Upgraded `respect/validation` from `~0.9` to `^2.0` (PHP 7.4+ compatible)
- `lib/hooks.php`: `v::numeric()` → `v::numericVal()` and `v::intType()` → `v::intVal()` for the new API. The loose `*Val` variants preserve behavior against string input from `get_input()`.

### Tests

- Removed PHP 7.0+ skip guard from `ValidationHooksTest::up()` (no longer blocked by `respect/validation ~0.9`)
- Mock builder for `Field` now includes `getValidationRules` so the input-vars view tests can stub it
- `getPluginID()` returns `hypeprototypervalidators` so the integration test case activates the plugin under test
