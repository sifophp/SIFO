<?php

function twig_function_pagelink()
{
    return new \Twig_Function(
        'pagelink', function (array $args = []) {
        $delimiter = ':';
        if (isset($args[0]['delimiter']))
        {
            $delimiter = $args[0]['delimiter'];
            unset($args[0]['delimiter']);
        }
        if (class_exists('\Sifo\FilterServer') && method_exists('\Sifo\FilterServer', 'getString'))
        {
            $current_querystring = \Sifo\FilterServer::getInstance()->getString('QUERY_STRING');
            $current_path        = \Sifo\FilterServer::getInstance()->getString('REQUEST_URI');
            $current_host        = \Sifo\Urls::$base_url;
        }
        else
        {
            $current_querystring = $_SERVER['QUERY_STRING'];
            $current_path        = $_SERVER['REQUEST_URI'];
            $current_host        = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        }

        // Clean querystring always. The URL could eventually include an empty "?".
        $current_path = str_ireplace('?' . $current_querystring, '', $current_path);

        $current_url = array_reverse(explode($delimiter, $current_path));

        if (isset($args[0]['base_url']))
        {
            $current_url = $args[0]['base_url'];
        }
        else
        {
            $current_url = implode($delimiter, array_reverse($current_url));

            if (isset($args[0]['absolute']))
            {
                $current_url = $current_host . $current_url;
            }
        }

        if (!isset($args[0]['page']))
        {
            trigger_error('pagelink: You should provide the destination pagelink. Params: ' . json_encode($args[0]), E_USER_WARNING);
        }
        else
        {
            if ($args[0]['page'] > 1 || (isset($args[0]['force_first_page']) && $args[0]['force_first_page'] == true))
            {
                return $current_url . $delimiter . $args[0]['page'];
            }

            return $current_url;
        }
    }, ['is_variadic' => true]
    );
}
