<?php

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require_once __DIR__ . '/vendor/autoload.php';
}

require_once __DIR__ . '/lib/hooks.php';

return [
	'bootstrap' => \hypeJunction\PrototyperValidators\Bootstrap::class,

	'hooks' => [
		'validate:type' => [
			'prototyper' => [
				'prototyper_validate_type' => [],
			],
		],
		'validate:min' => [
			'prototyper' => [
				'prototyper_validate_min' => [],
			],
		],
		'validate:max' => [
			'prototyper' => [
				'prototyper_validate_max' => [],
			],
		],
		'validate:minlength' => [
			'prototyper' => [
				'prototyper_validate_minlength' => [],
			],
		],
		'validate:maxlength' => [
			'prototyper' => [
				'prototyper_validate_maxlength' => [],
			],
		],
		'validate:contains' => [
			'prototyper' => [
				'prototyper_validate_contains' => [],
			],
		],
		'validate:regex' => [
			'prototyper' => [
				'prototyper_validate_regex' => [],
			],
		],
		'input_vars' => [
			'prototyper' => [
				'prototyper_filter_input_view_vars' => [],
			],
		],
	],

	'view_extensions' => [
		'prototyper/elements/help' => [
			'prototyper/elements/validation' => [],
		],
		'prototyper/input/before' => [
			'prototyper/elements/js_validation' => [],
		],
	],
];
