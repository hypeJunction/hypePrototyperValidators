<?php

/**
 * Prototyper validation                                                                                               
 */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require_once __DIR__ . '/vendor/autoload.php';
}

require_once __DIR__ . '/lib/hooks.php';

elgg_register_event_handler('init', 'system', 'prototyper_validators_init');

/**
 * Init
 * @return void
 */
function prototyper_validators_init() {

	elgg_extend_view('prototyper/elements/help', 'prototyper/elements/validation');
	elgg_extend_view('prototyper/input/before', 'prototyper/elements/js_validation');

	elgg_register_plugin_hook_handler('validate:type', 'prototyper', 'prototyper_validate_type');
	hypePrototyper()->config->registerValidationRule('type', array(
		'string',
		'alnum',
		'alpha',
		'int',
		'numeric',
		'date',
		'url',
		'email',
		'guid',
		'image',
	));

	elgg_register_plugin_hook_handler('validate:min', 'prototyper', 'prototyper_validate_min');
	hypePrototyper()->config->registerValidationRule('min');

	elgg_register_plugin_hook_handler('validate:max', 'prototyper', 'prototyper_validate_max');
	hypePrototyper()->config->registerValidationRule('max');

	elgg_register_plugin_hook_handler('validate:minlength', 'prototyper', 'prototyper_validate_minlength');
	hypePrototyper()->config->registerValidationRule('minlength');

	elgg_register_plugin_hook_handler('validate:maxlength', 'prototyper', 'prototyper_validate_maxlength');
	hypePrototyper()->config->registerValidationRule('maxlength');

	elgg_register_plugin_hook_handler('validate:contains', 'prototyper', 'prototyper_validate_contains');
	hypePrototyper()->config->registerValidationRule('contains');

	elgg_register_plugin_hook_handler('validate:regex', 'prototyper', 'prototyper_validate_regex');
	hypePrototyper()->config->registerValidationRule('regex');

	if (\hypeJunction\Integration::isElggVersionBelow('1.9.0')) {
		elgg_register_js('parsley', '/mod/hypePrototyperValidators/vendors/parsley/parsley.min.js', 'footer');
		elgg_register_simplecache_view('js/framework/legacy/prototyper_validation');
		elgg_register_js('prototyper_validation', elgg_get_simplecache_url('js', 'framework/legacy/prototyper_validation'), 'footer');
	} else {
		elgg_define_js('parsley', array(
			'src' => '/mod/hypePrototyperValidators/vendors/parsley/parsley.min.js',
			'deps' => array('jquery'),
		));
	}

	elgg_register_plugin_hook_handler('input_vars', 'prototyper', 'prototyper_filter_input_view_vars');
}
