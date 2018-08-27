<?php

function twig_filter_custom_tpl()
{
    return new \Twig_Filter(
        'custom_tpl', function (
        $template
    ) {
        if (!isset($template))
        {
            trigger_error("custom_tpl: The attribute 'template' are not set", E_USER_NOTICE);
        }

        $instance_templates = \Sifo\Config::getInstance()->getConfig('templates');
        if (isset($instance_templates[$template]))
        {
            $selected_template = $instance_templates[$template];
        }
        else
        {
            trigger_error("The template '{$template}' has not been found in the templates folder.", E_USER_ERROR);

            return false;
        }

        return $selected_template;
    }
    );
}
