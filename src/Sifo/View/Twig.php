<?php

namespace Sifo;

class ViewTwig implements ViewInterface
{
    /** @var \Twig_Environment */
    private $twig;

    private $auto_literal;

    private $escape_html;

    private $twig_plugins_directory_path;

    /** @var array */
    private $variables = [];

    public function __construct()
    {
        $loader             = new \Twig_Loader_Filesystem(ROOT_PATH);
        $this->auto_literal = false;
        $this->escape_html  = false;

        $view_setting = Config::getInstance()->getConfig('views');

        if ($view_setting && isset($view_setting['twig']))
        {
            $twig_settings = $view_setting['twig'];

            if (isset($twig_settings['custom_plugins_dir']))
            {
                // If is set, this path will be the default smarty plugins directory.
                $this->addPluginsDir($twig_settings['custom_plugins_dir']);
            }
            // Set this to false to avoid magical parsing of literal blocks without the {literal} tags.
            $this->auto_literal = $twig_settings['auto_literal'];
            $this->escape_html  = $twig_settings['escape_html'];
        }
        $this->twig = new \Twig_Environment(
            $loader, [
                'autoescape' => $this->escape_html,
                'cache'      => ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/_smarty/compile/'
            ]
        );

        $instance_inheritance = \Sifo\Domains::getInstance()->getInstanceInheritance();
        if (is_array($instance_inheritance))
        {
            // First the child instance, last the parent instance.
            $instance_inheritance = array_reverse($instance_inheritance);
            foreach ($instance_inheritance as $current_instance)
            {
                $this->addPluginsDir(ROOT_PATH . '/instances/' . $current_instance . '/templates/_smarty/twig_plugins');
            }
        }
        else
        {
            $this->addPluginsDir(ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/_smarty/twig_plugins');
        }

        $this->addPluginsDir(ROOT_PATH . '/vendor/sifophp/sifo/src/Twig-sifo-plugins');
        $this->loadPlugins();
        $this->twig->addExtension(new \Twig_Extensions_Extension_Text());
    }

    public function assign($variable_name, $value)
    {
        $this->variables[$variable_name] = $value;
    }

    public function fetch($template_path)
    {
        set_error_handler(array(View::class, "customErrorHandler"));

        $template_path = str_replace(ROOT_PATH, '', $template_path);

        try
        {
            $result = $this->twig->render($template_path, $this->variables);
        }
        catch (\Exception $e)
        {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $result = null;
        }

        restore_error_handler();

        return $result;
    }

    private function addPluginsDir($plugins_dir)
    {
        $this->twig_plugins_directory_path[] = $plugins_dir;
    }

    private function loadPlugins()
    {
        $this->loadFunctions();
        $this->loadFilters();
        $this->loadTags();
    }

    private function loadFunctions()
    {
        $loaded_functions = [];
        foreach ($this->twig_plugins_directory_path as $current_plugins_directory)
        {
            $functions = \glob($current_plugins_directory . '/function.*.php');
            if (empty($functions))
            {
                continue;
            }
            foreach ($functions as $filename)
            {
                \preg_match('/function.(.+).php/', \basename($filename), $matches);
                if (isset($loaded_functions['twig_function_' . $matches[1]]))
                {
                    continue;
                }
                include $filename;
                $loaded_functions['twig_function_' . $matches[1]] = true;

                if (!empty($matches[1]))
                {
                    $this->twig->addFunction(\call_user_func('twig_function_' . $matches[1], $this));
                }
            }
        }
    }

    private function loadFilters()
    {
        $loaded_filters = [];
        foreach ($this->twig_plugins_directory_path as $current_plugins_directory)
        {
            $functions = \glob($current_plugins_directory . '/filter.*.php');
            if (empty($functions))
            {
                continue;
            }
            foreach ($functions as $filename)
            {
                \preg_match('/filter.(.+).php/', \basename($filename), $matches);
                if (isset($loaded_filters['twig_filter_' . $matches[1]]))
                {
                    continue;
                }
                include $filename;
                $loaded_filters['twig_filter_' . $matches[1]] = true;

                if (!empty($matches[1]))
                {
                    $this->twig->addFilter(\call_user_func('twig_filter_' . $matches[1], $this));
                }
            }
        }
    }

    private function loadTags()
    {
        $loaded_tags = [];
        foreach ($this->twig_plugins_directory_path as $current_plugins_directory)
        {
            $functions = \glob($current_plugins_directory . '/tag.*.php');
            if (empty($functions))
            {
                continue;
            }
            foreach ($functions as $filename)
            {
                \preg_match('/tag.(.+).php/', \basename($filename), $matches);
                if (isset($loaded_tags['twig_tag_' . $matches[1]]))
                {
                    continue;
                }
                include $filename;
                $loaded_tags['twig_tag_' . $matches[1]] = true;

                if (!empty($matches[1]))
                {
                    $this->twig->addTokenParser(\call_user_func('twig_tag_' . $matches[1], $this));
                }
            }
        }
    }
}
