hypePrototyper Validators
=========================

## Predefined Validators

### ```type```

Asserts that user input is expected to be of a certain type.

* ```string```
* ```alnum``` - allows only alphanumeric characters and whitespaces
* ```alpha``` - allows only alpha characters and whitespaces
* ```int``` - allow only integer values
* ```numeric``` - allows numeric values
* ```date``` - allows date/time strings and objects
* ```url``` - allows only valid URLs
* ```email``` - allows only valid email addresses

```php
	$field = array(
		'type' => 'text',
		'validation_rules' => array(
			'type' => 'alnum',
		),
	);
```

### ```min``` and ```max```

Asserts that user input is between min and max values

```php
	$field = array(
		'type' => 'text',
		'validation_rules' => array(
			'type' => 'int',
			'min' => 10,
			'max' => 20,
		),
	);
```

### ```minlength``` and ```maxlength```

Asserts that the length of user input is between min and max values

```php
	$field = array(
		'type' => 'password',
		'validation_rules' => array(
			'type' => 'string',
			'minlength' => 6,
			'maxlength' => 25,
		),
	);
```

### ```contains```

Asserts that user input contains a predefined string

```php
	$field = array(
		'type' => 'text',
		'validation_rules' => array(
			'type' => 'string',
			'contains' => 'hello world',
		),
	);
```

### ```regex```

Asserts that user input matches a regex pattern

```php
	$field = array(
		'type' => 'time',
		'validation_rules' => array(
			'type' => 'string',
			'regex' => '^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$',
		),
	);
```

## Custom Validators

You can define custom validation rules, using the following pattern:

```php

// Callback for validating user input
elgg_register_plugin_hook_handler('validate:my_rule', 'prototyper', 'my_callback');

// Register the validation rule to make it available in hypePrototyperUI
hypePrototyper()->config->registerValidationRule('my_rule');
```

## Client-Side Validation

Partial client-side validation is available through Parsley.js. Do enable validation,
add ```data-parsley-validate``` to your form attributes.

```php

echo elgg_view_form('my_prototyped_form', array(
	'enctype' => 'multipart/form-data',
	'data-parsley-validate' => true,
), $vars);
```