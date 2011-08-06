<?php

$config["username"] = "youropeninviterusername";
$config["private_key"] = "yourprivatekey";
$config["cookie_path"] = '/tmp';
//Replace "curl" with "wget" if you would like to use wget instead
$config["transport"] = "curl";
//Available options: on_error =  log only requests containing errors; always =  log all requests; false =  don`t log anything
$config["local_debug"] = "always";
//When set to TRUE OpenInviter sends debug information to our servers. Set it to FALSE to disable this feature
$config["remote_debug"] = FALSE;
//When set to TRUE OpenInviter uses the OpenInviter Hosted Solution servers to import the contacts.
$config["hosted"] = FALSE;
//If you want to use a proxy in OpenInviter by adding another key to the array. Example: "proxy_1"= array("host"= "1.2.3.4","port"= "8080","user"= "user","password"= "pass")
//You can add as many proxies as you want and OpenInviter will randomly choose which one to use on each import.
$config["proxies"] = array();
$config["stats"] = TRUE;
$config["plugins_cache_time"] = 1800;
$config["plugins_cache_file"] = "oi_plugins.php";
$config["update_files"] = true;