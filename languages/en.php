<?php

$validation = array(
	'prototyper:validate:error:type:string' => 'Field %s expects a string value',
	'prototyper:validate:error:type:alnum' => 'Field %s allows only alphanumeric characters',
	'prototyper:validate:error:type:alpha' => 'Field %s allows only alphabetic characters',
	'prototyper:validate:error:type:int' => 'Field %s allows only integers',
	'prototyper:validate:error:type:numeric' => 'Field %s expects a numeric value',
	'prototyper:validate:error:type:date' => 'Field %s expects a valid date',
	'prototyper:validate:error:type:time' => 'Field %s expects valid time',
	'prototyper:validate:error:type:url' => 'Field %s expects a valid URL',
	'prototyper:validate:error:type:email' => 'Field %s expects a valid email',
	'prototyper:validate:error:type:guid' => 'Field %s expects a valid entity',
	'prototyper:validate:error:type:image' => 'Field %s expects an image file',

	'prototyper:validate:error:min' => 'Field %s expects a minimum value of %s',
	'prototyper:validate:error:max' => 'Field %s expects a maximum value of %s',

	'prototyper:validate:error:minlength' => 'Field %s can not be shorter than %s characters',
	'prototyper:validate:error:maxlength' => 'Field %s can not be longer than %s characters',

	'prototyper:validate:error:contains' => 'Field %s must contain \'%s\'',

	'prototyper:validate:error:regex' => 'Field %s must match the following pattern \'%s\'',

	'prototyper:validate:type:string' => 'Can contain any text',
	'prototyper:validate:type:alnum' => 'Must contain only alphanumeric characters and spaces',
	'prototyper:validate:type:alpha' => 'Must contain only alphabetic characters and spaces',
	'prototyper:validate:type:int' => 'Must be an integer',
	'prototyper:validate:type:numeric' => 'Must be a numeric value',
	'prototyper:validate:type:date' => 'Must be a valid date',
	'prototyper:validate:type:url' => 'Must be a valid URL',
	'prototyper:validate:type:email' => 'Must be a valid email',
	'prototyper:validate:type:guid' => 'Must be a valid entity',
	'prototyper:validate:type:image' => 'Accepts only image files',

	'prototyper:validate:min' => 'Should be a minimum of %s',
	'prototyper:validate:max' => 'Should be a maximum of %s',
	'prototyper:validate:minlength' => 'Should be longer than %s characters',
	'prototyper:validate:maxlength' => 'Should not be longer than %s characters',
	'prototyper:validate:contains' => 'Must contain "%s"',
	'prototyper:validate:regex' => 'Should match pattern "%s"',
	'prototyper:validate:img_min_width' => 'Width of no less than %spx',
	'prototyper:validate:img_max_width' => 'Width of no more than %spx',
	'prototyper:validate:img_min_height' => 'Height of no less than %spx',
	'prototyper:validate:img_max_height' => 'Height of no less than %spx',

	'prototyper:validate:error:image_dimensions' => 'Field %s expects a valid image',
	'prototyper:validate:error:img_min_width' => 'Field %s expects an image with a minimum width of %spx',
	'prototyper:validate:error:img_max_width' => 'Field %s expects an image with a maximum width of %spx',
	'prototyper:validate:error:img_min_height' => 'Field %s expects an image with a minimum height of %spx',
	'prototyper:validate:error:img_max_height' => 'Field %s expects an image with a maximum height of %spx',

);

add_translation('en', $validation);
