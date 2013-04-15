<?php
/**
 * SYNTAX:
 *
 * Every Key of the array is a group of JS files. All files inside a group will
 * be merged into a single file with the group name.
 *
 * The number is the Priority or order, the lower, the more soon the JS file
 * appears in the pack.
 *
 * $config['GROUP_NAME'] = array(
 *	LOAD ORDER IN PACK => array(
 *		'name' => 'A NAME TO IDENTIFY THIS JS FILE',
 *		'filename' => 'PATH TO FILENAME, EXCLUDING ROOT PATH'
 */
$config = array();