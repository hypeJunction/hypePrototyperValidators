<?php

namespace hypeJunction\PrototyperValidators;

use Elgg\DefaultPluginBootstrap;

/**
 * Bootstrap class.
 */
class Bootstrap extends DefaultPluginBootstrap {

	/**
	 * load.
	 *
	 * @return mixed
	 */
	public function load() {
		if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
			require_once __DIR__ . '/../../../vendor/autoload.php';
		}

		require_once __DIR__ . '/../../../lib/hooks.php';
	}

	/**
	 * init.
	 *
	 * @return mixed
	 */
	public function init() {
		if (!function_exists('hypePrototyper')) {
			return;
		}

		hypePrototyper()->config->registerValidationRule('type', [
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
		]);

		hypePrototyper()->config->registerValidationRule('min');
		hypePrototyper()->config->registerValidationRule('max');
		hypePrototyper()->config->registerValidationRule('minlength');
		hypePrototyper()->config->registerValidationRule('maxlength');
		hypePrototyper()->config->registerValidationRule('contains');
		hypePrototyper()->config->registerValidationRule('regex');

		// Parsley is provided as an Elgg 7 ES module by forms_validation
		// (elgg_register_esm('parsley.js', ...) + its validation.mjs binds
		// [data-parsley-validate] forms). Loading our own classic <script> copy
		// here is redundant and broken on Elgg 7 (no global jQuery at <script>
		// time -> "jQuery is not defined"), so it is removed.
	}
}
