<?php

$field = elgg_extract('field', $vars);

if (!$field instanceof \hypeJunction\Prototyper\Elements\Field) {
	return;
}

$validation = $field->getValidationRules();
if (empty($validation)) {
	return;
}

$items = [];
foreach ($validation as $rule => $expectation) {
	if ($rule == 'type') {
		if (in_array($expectation, ['text', 'string'])) {
			continue;
		}

		$items[] = elgg_format_element('span', [
			'class' => 'prototyper-validation-rules-help',
		], elgg_echo("prototyper:validate:type:$expectation"));
	} else {
		$items[] = elgg_format_element('span', [
			'class' => 'prototyper-validation-rules-help',
		], elgg_echo("prototyper:validate:$rule", [$expectation]));
	}
}

echo elgg_format_element('div', [
	'class' => 'elgg-text-help',
], implode(', ', $items));
