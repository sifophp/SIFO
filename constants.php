<?php

define('ROOT_PATH', __DIR__);
define('ADODB_DIR', __DIR__ . '/src/adodb5');
define('ADODB_BAD_RS', '<p>Bad $rs in %s. Connection or SQL invalid. Try using $connection->debug=true;</p>');
define('ADODB_FETCH_NUM', 1);
define('ADODB_FETCH_ASSOC', 2);
define('ADODB_FETCH_BOTH', 3);
define('ADODB_FETCH_DEFAULT', 0);
define('ADODB_OPT_HIGH', 2);
define('ADODB_OPT_LOW', 1);
define('ADODB_ASSOC_CASE',0);
define('ADODB_PHPVER',74000);
define('ADODB_EXTENSION', '.php');
define('ADODB_TABLE_REGEX','([]0-9a-z_\:\"\`\.\@\[-]*)');


define('PEAR_ERROR_RETURN', 1);
define('SMARTY_RESOURCE_CHAR_SET', 'utf-8');

define('IFX_SCROLL',1);

define('TIMESTAMP_FIRST_YEAR',0);

define("DB_ERROR", -1);
define("DB_ERROR_SYNTAX", -2);
define("DB_ERROR_CONSTRAINT", -3);
define("DB_ERROR_NOT_FOUND", -4);
define("DB_ERROR_ALREADY_EXISTS", -5);
define("DB_ERROR_UNSUPPORTED", -6);
define("DB_ERROR_MISMATCH", -7);
define("DB_ERROR_INVALID", -8);
define("DB_ERROR_NOT_CAPABLE", -9);
define("DB_ERROR_TRUNCATED", -10);
define("DB_ERROR_INVALID_NUMBER", -11);
define("DB_ERROR_INVALID_DATE", -12);
define("DB_ERROR_DIVZERO", -13);
define("DB_ERROR_NODBSELECTED", -14);
define("DB_ERROR_CANNOT_CREATE", -15);
define("DB_ERROR_CANNOT_DELETE", -16);
define("DB_ERROR_CANNOT_DROP", -17);
define("DB_ERROR_NOSUCHTABLE", -18);
define("DB_ERROR_NOSUCHFIELD", -19);
define("DB_ERROR_NEED_MORE_DATA", -20);
define("DB_ERROR_NOT_LOCKED", -21);
define("DB_ERROR_VALUE_COUNT_ON_ROW", -22);
define("DB_ERROR_INVALID_DSN", -23);
define("DB_ERROR_CONNECT_FAILED", -24);
define("DB_ERROR_EXTENSION_NOT_FOUND", -25);
define("DB_ERROR_NOSUCHDB", -30);
define("DB_ERROR_ACCESS_VIOLATION", -26);
define("DB_ERROR_DEADLOCK", -27);
define("DB_ERROR_STATEMENT_TIMEOUT", -28);
define("DB_ERROR_SERIALIZATION_FAILURE", -29);
define('DB_FETCHMODE_ASSOC', 2);
