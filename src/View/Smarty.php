<?php

namespace Sifo\View;

use Sifo\Bootstrap;
use Sifo\Config;
use Sifo\Http\Domains;

class Smarty implements ViewInterface
{
    /** @var \Smarty */
    private $smarty;

    public function __construct()
    {
        $this->smarty = new \Smarty();

        $this->smarty->inheritance_merge_compiled_includes = false;

        $instance_inheritance = Domains::getInstance()->getInstanceInheritance();

        // First the child instance, last the parent instance.
        $instance_inheritance = array_reverse($instance_inheritance);
        foreach ($instance_inheritance as $current_instance) {
            $this->smarty->addPluginsDir(ROOT_PATH . '/instances/' . $current_instance . '/templates/' . '_smarty/plugins');
        }

        // Last path is the default smarty plugins directory.
        $this->smarty->addPluginsDir(ROOT_PATH . '/vendor/sifophp/sifo/libraries/Smarty/Plugins');

        // The templates are taken using the templates.config.php mappings, under the variable $_tpls.
        $this->smarty->setTemplateDir(ROOT_PATH . '/');

        $this->smarty->setCompileDir(ROOT_PATH . '/var/cache/smarty/compile/' . Bootstrap::$instance . '/');
        $this->smarty->setCacheDir(ROOT_PATH . '/var/cache/smarty/cache/' . Bootstrap::$instance . '/');

        if ($view_setting = Config::getInstance()->getConfig('views', 'smarty')) {
            foreach ($view_setting as $property => $value)
            {
                if (isset($this->smarty->$property))
                {
                    $this->smarty->$property = $value;
                }
            }

            if (isset($smarty_settings['custom_plugins_dir'])) {
                $this->smarty->addPluginsDir($smarty_settings['custom_plugins_dir']);
            }
        }
    }


    public function fetch($template)
    {
        $this->template_path = $template;

        set_error_handler(array(View::class, "customErrorHandler"));
        \Smarty::muteExpectedErrors();

        try {
            $result = $this->smarty->fetch(
                $template,
                $cache_id = null,
                $compile_id = null,
                $parent = null,
                $display = false,
                $merge_tpl_vars = true,
                $no_output_filter = false
            );
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            $result = null;
        }

        // The current method launch an set_error_handler but inside self::muteExpectedErrors() ther is one more.
        // We need launch two restores to turn back to the preview expected behaviour.
        restore_error_handler();
        restore_error_handler();

        return $result;
    }

    public function assign($variable_name, $value)
    {
        $this->smarty->assign($variable_name, $value);
    }
}
