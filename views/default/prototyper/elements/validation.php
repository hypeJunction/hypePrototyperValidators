<?php

$field = elgg_extract('field', $vars);

if (!$field instanceof \hypeJunction\Prototyper\Elements\Field) {
	return;
}

$validation = $field->getValidationRules();
if (empty($validation)) {
	return;
}

$items = array();
foreach ($validation as $rule => $expectation) {
	if ($rule == 'type') {
		if (in_array($expectation, array('text', 'string'))) {
			continue;
		}
		$items[] = elgg_format_element('span', array(
			'class' => 'prototyper-validation-rules-help',
				), elgg_echo("prototyper:validate:type:$expectation"));
	} else {
		$items[] = elgg_format_element('span', array(
			'class' => 'prototyper-validation-rules-help',
				), elgg_echo("prototyper:validate:$rule", array($expectation)));
	}
}

echo elgg_format_element('div', array(
	'class' => 'elgg-text-help',
		), implode(', ', $items));
