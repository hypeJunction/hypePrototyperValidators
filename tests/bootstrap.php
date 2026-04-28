<?php
/**
 * PHPUnit bootstrap for hypePrototyperValidators tests.
 *
 * Plugin must be installed at {elgg_root}/mod/hypeprototypervalidators/
 * and hypePrototyper must be active.
 */

// tests/ -> plugin/ -> mod/ -> elgg_root/
$elggRoot = dirname(dirname(dirname(__DIR__)));

require_once $elggRoot . '/vendor/autoload.php';

// Load Elgg test classes (UnitTestCase, IntegrationTestCase, etc.)
$testClassesDir = $elggRoot . '/vendor/elgg/elgg/engine/tests/classes';
spl_autoload_register(function ($class) use ($testClassesDir) {
    $file = $testClassesDir . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Plugin autoloader (respect/validation lives here)
$pluginRoot = dirname(__DIR__);
if (file_exists($pluginRoot . '/vendor/autoload.php')) {
    require_once $pluginRoot . '/vendor/autoload.php';
}

// hypePrototyper classes (Field, ValidationStatus, etc.) needed by tests.
// Registered here so mocks can reflect on the abstract Field class.
$hypePrototyperClasses = $elggRoot . '/mod/hypeprototyper/classes';
if (is_dir($hypePrototyperClasses)) {
    spl_autoload_register(function ($class) use ($hypePrototyperClasses) {
        $file = $hypePrototyperClasses . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

// Load hook handler functions (they're plain procedural functions)
require_once $pluginRoot . '/lib/hooks.php';

\Elgg\Application::loadCore();
