<?php
// DON'T TOUCH THIS LINES:
define( 'ROOT_PATH', realpath( dirname( __FILE__ ) . '/../../..' ) );
require ROOT_PATH . '/instances/CLBootstrap.php';

// The controller to run customization.
CL\Sifo\Bootstrap::$script_controller = 'scripts/load/avg/autoswitch'; // <-- Should customize only this line.

// DON'T TOUCH THIS LINE:
CL\Sifo\Bootstrap::execute();
?>
