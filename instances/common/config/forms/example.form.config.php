<?php
/*
 * Examples using validateElements:
 *
 * Only fields 'name' and 'filter' are required. Examples:
 *
 * $form_elems[] = array(
 *	'name' => 'email',  // Name of the input
 * 	'filter' => 'Email', // Filter rule to apply, e.g: 'Email' for \Sifo\Filter::getEmail function
 * 	'params' => false, // Additional parameters needed by the filtering function.
 * 	'required' => true // The field is required to continue?
 * 	);
 *
 * // Validate an integer between 16 and 150
 * $form_elems[] = array(
 * 	 'name' => 'age',
 * 	 'filter' => 'Integer',
 * 	 'params' => array( 16, 150 )
 * 	);
 *
 * // Validate a username with a regular expression
 * $form_elems[] = array(
 * 	 'name' => 'username',
 * 	 'filter' => 'Regexp',
 * 	 'params' => array( '/[a-z]+/' ),
 *	 'error' => 'Only letters are accepted in the username.'
 * 	);
*/

$config[] = array(
	'name' => 'email',
	'filter' => 'getEmail',
	'error' => 'Please write a valid email',
	'required' => true
);

$config[] = array(
	'name' => 'phone',
	'filter' => 'getRegexp',
	'params' => array( '/^(\+\d\d)?\d{9}$/' ),
	'error' => 'A valid phone number is with 9 number or with +xx prefix',
	'required' => true
);


?>
