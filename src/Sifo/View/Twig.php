<?php

namespace Sifo;

class ViewTwig implements ViewInterface
{
    /** @var \Twig_Environment */
    private $twig;

    private $twig_plugins_directory_paths;

    /** @var array */
    private $variables = [];

    public function __construct()
    {
        $loader     = new \Twig_Loader_Filesystem(ROOT_PATH);
        $this->twig = new \Twig_Environment(
            $loader, [
                'autoescape' => false,
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

        $this->loadPlugins();
        $this->twig->addExtension(new \Twig_Extensions_Extension_Text());

        $function = new \Twig_SimpleFunction(
            't', function ($text) {
            return I18N::getTranslation($text);
        }
        );

        $this->twig->addFunction($function);
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

    private function addPluginsDir(string $plugins_dir)
    {
        $this->twig_plugins_directory_path[] = $plugins_dir;
    }

    private function loadPlugins()
    {
        $this->loadFunctions();
        $this->loadFilters();
    }

    private function loadFunctions()
    {
        foreach ($this->twig_plugins_directory_path as $current_plugins_directory)
        {
            $functions = \glob($current_plugins_directory . '/function.*.php');
            if (empty($functions))
            {
                continue;
            }
            foreach ($functions as $filename)
            {
                include $filename;
                \preg_match('/function.(.+).php/', \basename($filename), $matches);
                if (!empty($matches[1]))
                {
                    $this->twig->addFunction(\call_user_func('twig_function_' . $matches[1]));
                }
            }
        }
    }

    private function loadFilters()
    {
        foreach ($this->twig_plugins_directory_path as $current_plugins_directory)
        {
            $functions = \glob($current_plugins_directory . '/filter.*.php');
            if (empty($functions))
            {
                continue;
            }
            foreach ($functions as $filename)
            {
                include $filename;
                \preg_match('/filter.(.+).php/', \basename($filename), $matches);
                if (!empty($matches[1]))
                {
                    $this->twig->addFilter(\call_user_func('twig_filter_' . $matches[1]));
                }
            }
        }
    }
}
