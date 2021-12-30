<?php

function twig_function_genurl()
{
    return new \Twig_Function(
        'genurl', function (array $args = []) {
        $delimiter = ':';
        if (isset($args[0]['delimiter']))
        {
            $delimiter = $args[0]['delimiter'];
            unset($args[0]['delimiter']);
        }

        if (false !== strpos($delimiter, '$'))
        {
            trigger_error("fill: The delimiter '$' is banned in function {url}", E_USER_NOTICE);
        }

        $action = $args[0]['action'] ?? 'replace';

        if (!in_array($action, array('add', 'replace', 'remove', 'clean_params')))
        {
            trigger_error(
                "[genurl] You must specify a valid genurl action parameter (add, replace, remove or clean_params). Remember that you can't add a parsed parameter called 'action' in order to avoid collision."
            );
        }

        // You can also specify {genurl key='filter_name' value='filter_value'} instead of {genurl filter_name='filter_value'}.
        // This is useful when you have dynamic filtering.
        if (!empty($args[0]['key']) && isset($args[0]['value']))
        {
            $args[0][$args[0]['key']] = $args[0]['value'];
        }

        $url_params            = $args[0]['params'];
        $url_params_definition = $args[0]['params_definition'];

        // Build $order based in params_definition position for each key.
        if (is_array($url_params_definition))
        {
            $n = 0;
            foreach ($url_params_definition as $key => $value)
            {
                $order[$key] = $n;
                $n++;
            }
        }

        $html_result          = $args[0]['subject'];
        $original_html_result = $html_result;

        $normalize = (!isset($args[0]['normalize']) || $args[0]['normalize'] != 'no');
        unset($args[0]['action']);
        unset($args[0]['normalize']);
        unset($args[0]['params']);
        unset($args[0]['params_definition']);
        unset($args[0]['subject']);
        unset($args[0]['key']);
        unset($args[0]['value']);

        // Step 1: Fill $url_params with actual and new values.

        if ($action == 'replace') // Replace actual value with new one.
        {
            foreach ($args[0] as $key => $value)
            {
                $url_params[$key] = $value;
                if (true === $url_params_definition[$key]['is_list'])
                {
                    $url_params[$key] = array($value);
                }
            }
        }
        elseif ($action == 'clean_params')
        {
            $url_params = array();
            foreach ($args[0] as $key => $value)
            {
                $url_params[$key] = $value;
                if (true === $url_params_definition[$key]['is_list'])
                {
                    $url_params[$key] = array($value);
                }
            }
        }
        elseif ($action == 'add') // Add actual value with new one.
        {
            foreach ($args[0] as $key => $value)
            {
                $url_params[$key][] = $value;
            }
        }
        elseif ($action == 'remove')
        {
            foreach ($args[0] as $key => $value)
            {
                if (true === $url_params_definition[$key]['is_list'])
                {
                    $found_key = array_search(strtolower($value), $url_params[$key]);
                    if (false !== $found_key)
                    {
                        unset($url_params[$key][$found_key]);
                    }
                }
                else
                {
                    if (is_array($url_params) && array_key_exists($key, $url_params))
                    {
                        unset($url_params[$key]);
                    }
                }
            }
        }

        // Step 2: translate actual params to right key=>value pairs based on url definition.
        $n = 0;

        if (is_array($url_params))
        {
            foreach ($url_params as $_key => $val)
            {
                if (is_array($val))
                {
                    foreach ($val as $__key => $__val)
                    {
                        if (true === $url_params_definition[$_key]['apply_translation'])
                        {
                            $current_domain = \Sifo\I18N::getDomain();
                            \Sifo\I18N::setDomain('urlparams', \Sifo\I18N::getLocale());
                            $val[$__key] = \Sifo\I18N::getTranslation($__val);
                            \Sifo\I18N::setDomain($current_domain, \Sifo\I18N::getLocale());
                        }
                        else
                        {
                            $val[$__key] = $__val;
                        }
                        if ($normalize)
                        {
                            $val[$__key] = \Sifo\Urls::normalize($val[$__key]);
                        }
                    }
                    // Ordering values list:
                    sort($val);
                    $val = implode(',', $val);
                }
                elseif (true === $url_params_definition[$_key]['apply_translation'])
                {
                    $current_domain = \Sifo\I18N::getDomain();
                    \Sifo\I18N::setDomain('urlparams', \Sifo\I18N::getLocale());
                    $val = \Sifo\I18N::getTranslation($val);
                    \Sifo\I18N::setDomain($current_domain, \Sifo\I18N::getLocale());
                    if ($normalize)
                    {
                        $val = \Sifo\Urls::normalize($val);
                    }
                }
                elseif ($normalize)
                {
                    $val = \Sifo\Urls::normalize($val);
                }

                if (isset($val) && '' != $val && false !== $val)
                {
                    $n++;

                    if (array_key_exists($_key, $url_params_definition))
                    {
                        $_html_filters[$_key] = $url_params_definition[$_key]['internal_key'] . $delimiter . $val;
                    }
                    else
                    {
                        trigger_error("fill: The parameter '" . $_key . "' is not defined in given params_definition", E_USER_NOTICE);
                    }
                }
            }

            if (isset($_html_filters) && is_array($_html_filters))
            {
                // We alphabetically order the filters based on 'internal_key'
                // to prevent duplicated URL with the same parameters.
                ksort($_html_filters);
                $html_result .= $delimiter . implode($delimiter, $_html_filters);
            }
        }

        if ($n > 0)
        {
            return $html_result;
        }

        return $original_html_result;
    }, ['is_variadic' => true]
    );
}
