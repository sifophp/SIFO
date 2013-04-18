<?php
/**
 * This config file allows you to define Regex to be used in the router.config.php patterns field.
 * The default values provided are:

$config['isInteger']        = '([1-9]([0-9]+)?\b)';
$config['isHex']            = '(0[xX][0-9a-fA-F])';
$config['isFloat']          = '([-+]?\b[0-9]+(\.[0-9]+)?\b)';
$config['isBoolean']        = '^([0|1|true|false])';
$config['isAlphaNumeric']   = '([a-zA-z0-9_\-])';
$config['isString']         = '([\s\S])';

 */

$config['isInteger']        = '(([1-9]\d?))';
$config['isHex']            = '(0[xX][0-9a-fA-F])';
$config['isFloat']          = '([-+]?\b[0-9]+(\.[0-9]+)?\b)';
$config['isBoolean']        = '^([0|1|true|false])';
$config['isAlphaNumeric']   = '([a-zA-z0-9_\-])';
$config['isString']         = '([\s\S])';
$config['isLocale']         = '([a-zA-Z][a-zA-Z])';             // eg: en
$config['isLongLocale']     = '([a-z][a-z]_[A-Z][A-Z])';        // eg: en_US