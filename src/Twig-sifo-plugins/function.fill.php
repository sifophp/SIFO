<?php

function twig_function_fill($twig)
{
    return new \Twig_Function(
        'fill', function (array $args = []) use ($twig) {
        $delimiter = '%';

        if (isset($args[0]['delimiter']))
        {
            $delimiter = $args[0]['delimiter'];
            unset($args[0]['delimiter']);
        }

        $normalize            = true;
        $args[0]['normalize'] = true;

        if (isset($args[0]['normalize']))
        {
            switch ($args[0]['normalize'])
            {
                case 'no':
                case '0':
                case 'false':
                    $normalize = false;
                    break;
                default:
                    $normalize = true;
            }
        }

        if (false !== strpos($delimiter, '$'))
        {
            trigger_error("fill: The delimiter '$' is banned in function {url}", E_USER_NOTICE);
        }

        if (!isset($args[0]['subject']) || (is_countable($args[0]) ? count($args[0]) : 0) < 2)
        {
            trigger_error("fill: The attribute 'subject' and at least one parameter is needed in function {url}", E_USER_NOTICE);
        }

        $escapevar = $twig->escape_html;
        if (isset($args[0]['escapevar']))
        {
            $escapevar = ($twig->escape_html && ($args[0]['escapevar'] != 'no'));
            unset($args[0]['escapevar']);
        }

        $html_result = $args[0]['subject'];
        $tmp_result  = $html_result;
        unset($args[0]['subject']);

        foreach ($args[0] as $key => $_val)
        {
            if ($escapevar)
            {
                $_val = htmlspecialchars($_val, ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
            }
            $_val       = (string) $_val;
            $tmp_result = str_replace($delimiter . $key . $delimiter, (string) $_val, $tmp_result);

            // The UrlParse::normalize, amongs other things lowers the string. Check if plugin calls with lower=no to skip:
            if ($normalize && true === \Sifo\Urls::$normalize_values && (!isset($args[0]['lower']) || $args[0]['lower'] != 'no'))
            {
                $html_result = str_replace($delimiter . $key . $delimiter, \Sifo\Urls::normalize((string) $_val), $html_result);
            }
            else
            {
                $html_result = $tmp_result;
            }
        }

        if (false !== strpos(urldecode($html_result), (string) $delimiter))
        {
            trigger_error("fill: There are still parameters to replace, because the '$delimiter' delimiter was found in $html_result");
        }

        return $html_result;
    }, ['is_variadic' => true]
    );
}
