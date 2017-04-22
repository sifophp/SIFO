<?php

namespace Sifo\Debug;

use Sifo\Database\Redis\PredisProxyClient as OriginalPredisProxyClient;

class PredisProxyClient extends OriginalPredisProxyClient
{
    public function __call($method, $args)
    {
        if (is_object($this->client))
        {
            $call_data               = array();
            $call_data['connection'] = $this->connection_params;
            $call_data['method']     = $method;
            $call_data['args']       = $args;
            $call_data['controller'] = $this->getCallerClass();
            $call_data['results']    = call_user_func_array(array($this->client, $method), $args);

            Debug::push('redis', $call_data);

            return $call_data['results'];
        }

        return null;
    }

    public function getCallerClass()
    {
        $trace = debug_backtrace();
        foreach ($trace as $steps)
        {
            $classes[$steps['class']] = $steps['class'];
        }

        return implode(' > ', array_slice($classes, 0, 4));
    }
}
