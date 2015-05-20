<?php

if (\hypeJunction\Integration::isElggVersionBelow('1.9.0')) {
	elgg_load_js('parsley');
	//elgg_load_js('prototyper_validation');
} else {
	elgg_require_js('parsley');
	//elgg_require_js('framework/prototyper_validation');
}