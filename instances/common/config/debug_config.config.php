<?php
/**
 * Debug config.
 * If array is empty, in Debug will be showed all the information available.
 * You can define wich modules do you want to be showed in debug. For example:
 *
 *  $config['debug']['queries'] = true;
 *  $config['debug']['searches'] = false;
 *  $config['debug']['log_messages'] = true;
 *
 * In that case, debug will show the queries module and logs modules. Searches won't be showed even it has information.
 *
 */

$config['debug'] = array();
