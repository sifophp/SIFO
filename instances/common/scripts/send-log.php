<?php
// DON'T TOUCH THIS LINES:
define( 'ROOT_PATH', realpath( dirname( __FILE__ ) . '/../../..' ) );
require ROOT_PATH . '/instances/CLBootstrap.php';

// The controller to run customization.
\Sifo\CLBootstrap::$script_controller = 'scripts/send/log'; // <-- Should customize only this line.

// DON'T TOUCH THIS LINE:
\Sifo\CLBootstrap::execute();
?>
