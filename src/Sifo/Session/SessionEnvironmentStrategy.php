<?php

namespace Sifo;

class SessionEnvironmentStrategy implements SessionNameStrategy
{
    public function set()
    {
        $instance_inheritance = Domains::getInstance()->getInstanceInheritance();
        $vertical_instance = array_pop($instance_inheritance);
        $instance_environment_initial = isset($_SERVER['APP_ENV'][0]) ? $_SERVER['APP_ENV'][0] : '';
        $instance_session_name = "SSID_{$instance_environment_initial}_{$vertical_instance}";
        session_name($instance_session_name);
    }
}
