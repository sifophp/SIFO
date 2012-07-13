<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sergio.ambel
 * Date: 15/06/12
 * Time: 13:22
 */


define( 'ROOT_PATH', realpath( dirname( __FILE__ ) . '/../..' ) );

require_once ROOT_PATH . '/instances/Bootstrap.php';
require_once dirname( __FILE__).'\Extensions\ControllerTest.php';
require_once dirname( __FILE__).'\VisualPHPUnit\CoverageAnalysis.php';

$instance_name = "common"; // Default.
foreach( $_SERVER['argv'] as $param )
{
	if ( preg_match( '/instances[\\\|\/]([^\\\|^\/]*)/i', $param, $matches) )
	{
		$instance_name = $matches[1];
		break;
	}
}

$_SERVER["HTTP_HOST"] = 'unit.test';

\Sifo\Bootstrap::$instance = $instance_name;
\Sifo\Bootstrap::autoload();
