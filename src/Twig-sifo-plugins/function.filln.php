<?php

function twig_function_filln()
{
    return new \Twig_Function(
        'filln', function (array $args = []) {
        $delimiter = '%';
        if (isset($args[0]['delimiter']))
        {
            $delimiter = $args[0]['delimiter'];
            unset($args[0]['delimiter']);
        }

        if (false !== strpos($delimiter, '$'))
        {
            trigger_error("fill: The delimiter '$' is banned in function {url}", E_USER_WARNING);
        }

        $strict_params_normalize = false;
        if (isset($args[0]['strict']))
        {
            if (in_array($args[0]['strict'], array(true, false)))
            {
                $strict_params_normalize = $args[0]['strict'];
            }
            else
            {
                trigger_error(
                    "filln: The 'strict' parameter is a reserved parameter in order to indicate if the other parameters' normalize has to be in strict mode or not. You only can indicate 'tru' or 'false' as its value, not: "
                    . $args[0]['strict'],
                    E_USER_WARNING
                );
            }
            unset($args[0]['strict']);
        }

        if (!isset($args[0]['subject']) || (is_countable($args[0]) ? count($args[0]) : 0) < 2)
        {
            trigger_error(
                "fill: The attribute 'subject' and at least one parameter is needed in function {url} in: " . \Sifo\FilterServer::getInstance()->getString(
                    'SCRIPT_URI'
                ),
                E_USER_WARNING
            );
        }

        $subject    = $args[0]['subject'];
        $tmp_result = $subject;

        foreach ($args[0] as $key => $current_value)
        {
            $current_value = (string) $current_value;
            $tmp_result    = str_replace($delimiter . $key . $delimiter, $current_value, $tmp_result);

            if (method_exists('\Verticalroot\UrlsManagement', 'normalize'))
            {
                $urlized_val = \Verticalroot\UrlsManagement::normalize($current_value, $strict_params_normalize);
                $subject     = str_replace($delimiter . $key . $delimiter, $urlized_val, $subject);
            }
            else if (method_exists('Urls', 'normalize'))
            {
                $urlized_val = \Sifo\Urls::normalize($current_value);
                $subject     = str_replace($delimiter . $key . $delimiter, $urlized_val, $subject);
            }
            else
            {
                $subject = $tmp_result;
            }
        }

        return $subject;
    }, ['is_variadic' => true]
    );
}
