# Changelog

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
