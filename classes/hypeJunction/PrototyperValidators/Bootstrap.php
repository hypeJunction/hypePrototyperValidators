<?php

namespace hypeJunction\PrototyperValidators;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {

	/**
	 * {@inheritdoc}
	 */
	public function init() {
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

		elgg_define_js('parsley', [
			'src' => '/mod/hypePrototyperValidators/vendors/parsley/parsley.min.js',
			'deps' => ['jquery'],
		]);
	}
}
