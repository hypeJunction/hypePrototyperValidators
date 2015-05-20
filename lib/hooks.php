<?php

use hypeJunction\Prototyper\Elements\ValidationStatus;
use hypeJunction\Prototyper\Elements\Field;
use Respect\Validation\Validator as v;

/**
 * Validates input type
 *
 * @param string           $hook       "validate:type"
 * @param string           $type       "prototyper"
 * @param ValidationStatus $validation Current validation status
 * @param array            $params     Hook params
 * @return ValidationStatus
 */
function prototyper_validate_type($hook, $type, $validation, $params) {

	if (!$validation instanceof ValidationStatus) {
		$validation = new ValidationStatus();
	}

	$field = elgg_extract('field', $params);
	if (!$field instanceof Field) {
		return $validation;
	}

	$rule = elgg_extract('rule', $params);
	if ($rule != "type") {
		return $validation;
	}

	$value = elgg_extract('value', $params);
	$expectation = elgg_extract('expectation', $params);

	switch ($expectation) {

		case 'text' :
		case 'string' :
			if (!v::string()->validate($value)) {
				$validation->setFail(elgg_echo('prototyper:validate:error:type:string', array($field->getLabel())));
			}
			break;

		case 'alnum' :
		case 'alphanum' :
			if (!v::alnum()->validate($value)) {
				$validation->setFail(elgg_echo('prototyper:validate:error:type:alnum', array($field->getLabel())));
			}
			break;

		case 'alpha' :
			if (!v::alpha()->validate($value)) {
				$validation->setFail(elgg_echo('prototyper:validate:error:type:alpha', array($field->getLabel())));
			}
			break;

		case 'number' :
		case 'numeric' :
			if (!v::numeric()->validate($value)) {
				$validation->setFail(elgg_echo('prototyper:validate:error:type:numeric', array($field->getLabel())));
			}
			break;

		case 'integer' :
		case 'int' :
			if (!v::int()->validate($value)) {
				$validation->setFail(elgg_echo('prototyper:validate:error:type:int', array($field->getLabel())));
			}
			break;

		case 'date' :
			if (!v::date()->validate($value)) {
				$validation->setFail(elgg_echo('prototyper:validate:error:type:date', array($field->getLabel())));
			}
			break;

		case 'url' :
			if (!v::filterVar(FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)->validate($value)) {
				$validation->setFail(elgg_echo('prototyper:validate:error:type:url', array($field->getLabel())));
			}
			break;

		case 'email' :
			if (!v::filterVar(FILTER_VALIDATE_EMAIL)->validate($value)) {
				$validation->setFail(elgg_echo('prototyper:validate:error:type:email', array($field->getLabel())));
			}
			break;

		case 'guid' :
		case 'entity' :
			if (!elgg_entity_exists($value)) {
				$validation->setFail(elgg_echo('prototyper:validate:error:type:guid', array($field->getLabel())));
			}
			break;

		case 'image' :
			$type = elgg_extract('type', $value);
			if (!$type || substr_count($type, 'image/') == 0) {
				$validation->setFail(elgg_echo('prototyper:validate:error:type:image', array($field->getLabel())));
			}
			break;
	}

	return $validation;
}

/**
 * Validates that input is greater than the expecation
 *
 * @param string           $hook       "validate:min"
 * @param string           $type       "prototyper"
 * @param ValidationStatus $validation Current validation status
 * @param array            $params     Hook params
 * @return ValidationStatus
 */
function prototyper_validate_min($hook, $type, $validation, $params) {

	if (!$validation instanceof ValidationStatus) {
		$validation = new ValidationStatus();
	}

	$field = elgg_extract('field', $params);
	if (!$field instanceof Field) {
		return $validation;
	}


	$rule = elgg_extract('rule', $params);
	if ($rule != "min") {
		return $validation;
	}

	$value = elgg_extract('value', $params);
	$expectation = elgg_extract('expectation', $params);

	if (!v::min($expectation)->validate($value)) {
		$validation->setFail(elgg_echo('prototyper:validate:error:min', array($field->getLabel(), $expectation)));
	}

	return $validation;
}

/**
 * Validates that input is less than the expecation
 *
 * @param string           $hook       "validate:max"
 * @param string           $type       "prototyper"
 * @param ValidationStatus $validation Current validation status
 * @param array            $params     Hook params
 * @return ValidationStatus
 */
function prototyper_validate_max($hook, $type, $validation, $params) {

	if (!$validation instanceof ValidationStatus) {
		$validation = new ValidationStatus();
	}

	$field = elgg_extract('field', $params);
	if (!$field instanceof Field) {
		return $validation;
	}

	$rule = elgg_extract('rule', $params);
	if ($rule != "max") {
		return $validation;
	}

	$value = elgg_extract('value', $params);
	$expectation = elgg_extract('expectation', $params);

	if (!v::max($expectation)->validate($value)) {
		$validation->setFail(elgg_echo('prototyper:validate:error:max', array($field->getLabel(), $expectation)));
	}

	return $validation;
}

/**
 * Validates that input length is greater than the expecation
 *
 * @param string           $hook       "validate:minlength"
 * @param string           $type       "prototyper"
 * @param ValidationStatus $validation Current validation status
 * @param array            $params     Hook params
 * @return ValidationStatus
 */
function prototyper_validate_minlength($hook, $type, $validation, $params) {

	if (!$validation instanceof ValidationStatus) {
		$validation = new ValidationStatus();
	}

	$field = elgg_extract('field', $params);
	if (!$field instanceof Field) {
		return $validation;
	}

	$rule = elgg_extract('rule', $params);
	if ($rule != "minlength") {
		return $validation;
	}

	$value = elgg_extract('value', $params);
	$expectation = elgg_extract('expectation', $params);

	if (!v::length($expectation, null)->validate($value)) {
		$validation->setFail(elgg_echo('prototyper:validate:error:minlength', array($field->getLabel(), $expectation)));
	}

	return $validation;
}

/**
 * Validates that input length is greater than the expecation
 *
 * @param string           $hook       "validate:maxlength"
 * @param string           $type       "prototyper"
 * @param ValidationStatus $validation Current validation status
 * @param array            $params     Hook params
 * @return ValidationStatus
 */
function prototyper_validate_maxlength($hook, $type, $validation, $params) {

	if (!$validation instanceof ValidationStatus) {
		$validation = new ValidationStatus();
	}

	$field = elgg_extract('field', $params);
	if (!$field instanceof Field) {
		return $validation;
	}

	$rule = elgg_extract('rule', $params);
	if ($rule != "maxlength") {
		return $validation;
	}

	$value = elgg_extract('value', $params);
	$expectation = elgg_extract('expectation', $params);

	if (!v::length(null, $expectation)->validate($value)) {
		$validation->setFail(elgg_echo('prototyper:validate:error:maxlength', array($field->getLabel(), $expectation)));
	}

	return $validation;
}

/**
 * Validates that input contains a string
 *
 * @param string           $hook       "validate:contains"
 * @param string           $type       "prototyper"
 * @param ValidationStatus $validation Current validation status
 * @param array            $params     Hook params
 * @return ValidationStatus
 */
function prototyper_validate_contains($hook, $type, $validation, $params) {

	if (!$validation instanceof ValidationStatus) {
		$validation = new ValidationStatus();
	}

	$field = elgg_extract('field', $params);
	if (!$field instanceof Field) {
		return $validation;
	}

	$rule = elgg_extract('rule', $params);
	if ($rule != "contains") {
		return $validation;
	}

	$value = elgg_extract('value', $params);
	$expectation = elgg_extract('expectation', $params);

	if (!v::contains($expectation)->validate($value)) {
		$validation->setFail(elgg_echo('prototyper:validate:error:contains', array($field->getLabel(), $expectation)));
	}

	return $validation;
}

/**
 * Validates that input matches a regex
 *
 * @param string           $hook       "validate:regex"
 * @param string           $type       "prototyper"
 * @param ValidationStatus $validation Current validation status
 * @param array            $params     Hook params
 * @return ValidationStatus
 */
function prototyper_validate_regex($hook, $type, $validation, $params) {

	if (!$validation instanceof ValidationStatus) {
		$validation = new ValidationStatus();
	}

	$field = elgg_extract('field', $params);
	if (!$field instanceof Field) {
		return $validation;
	}

	$rule = elgg_extract('rule', $params);
	if ($rule != "regex") {
		return $validation;
	}

	$value = elgg_extract('value', $params);
	$expectation = elgg_extract('expectation', $params);

	if (!v::regex($expectation)->validate($value)) {
		$validation->setFail(elgg_echo('prototyper:validate:error:regex', array($field->getLabel(), $expectation)));
	}

	return $validation;
}

/**
 * Filters input view vars
 *
 * @param string $hook   "input_vars"
 * @param string $type   "prototyper"
 * @param array  $return Vars
 * @param array  $params Hook params
 * @return array
 */
function prototyper_filter_input_view_vars($hook, $type, $return, $params) {

	$field = elgg_extract('field', $params);
	if (!$field instanceof Field) {
		return $return;
	}

	$validation = $field->getValidationRules();
	if (empty($validation)) {
		return $return;
	}

	$type_map = array(
		'alnum' => 'alphanum',
		'int' => 'integer',
		'numeric' => 'number',
		'date' => 'false',
		'url' => 'url',
		'email' => 'email',
	);

	// Convert validation rules to data- attributes compatible with parsley
	foreach ($validation as $rule => $expectation) {
		switch ($rule) {
			case 'type' :
				$return['data-parsley-type'] = elgg_extract($expectation, $type_map, false);
				break;

			case 'regex' :
				$return['data-parsley-pattern'] = $expectation;
				break;

			default:
				$return["data-parsley-$rule"] = $expectation;
				break;
		}
	}

	$return['data-parsley-trigger'] = 'change';
	if ($field->isRequired()) {
		$return['data-parsley-required'] = true;
	}
	if ($field->isMultiple() || in_array($field->getType(), array('checkboxes', 'radio'))) {
		$return['data-parsley-multiple'] = $field->getShortname();
	}
	
	return $return;
}
