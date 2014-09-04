<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sergio.ambel
 * Date: 15/06/12
 * Time: 13:22
 */


define( 'ROOT_PATH', realpath( dirname( __FILE__ ) . '/../..' ) );

require_once ROOT_PATH . '/vendor/sifophp/sifo/src/sifo/Bootstrap.php';
require_once ROOT_PATH.'/vendor/sifophp/sifo/src/PHPUnit-3.5.0/Text/Template.php';


$instance_name = "common"; // Default.

foreach( $_SERVER['argv'] as $param )
{
	if ( preg_match( '/instances[\\\|\/]([^\\\|^\/]*)/i', $param, $matches) )
	{
		$instance_name = $matches[1];
		break;
	}
}

\Sifo\Bootstrap::$instance = $instance_name;
\Sifo\Bootstrap::autoload();
