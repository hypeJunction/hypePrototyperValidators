//<script>

	elgg.provide('elgg.prototyper_validation');

	elgg.prototyper_validation.init = function () {

	};

	elgg.register_hook_handler('init', 'system', elgg.prototyper_validation.init);