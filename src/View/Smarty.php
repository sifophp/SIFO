<?php

namespace Sifo\View;

use Sifo\Bootstrap;
use Sifo\Config;

class Smarty implements ViewInterface
{
    /** @var \Smarty */
    private $smarty;

    public function __construct()
    {
        $this->smarty = new \Smarty();

        $this->smarty->inheritance_merge_compiled_includes = false;

        // Get the instances inheritance.
        $instance_inheritance = \Sifo\Http\Domains::getInstance()->getInstanceInheritance();

        // If there is inheritance.
        if (is_array($instance_inheritance)) {
            // First the child instance, last the parent instance.
            $instance_inheritance = array_reverse($instance_inheritance);
            foreach ($instance_inheritance as $current_instance) {
                $this->smarty->addPluginsDir(ROOT_PATH . '/instances/' . $current_instance . '/templates/' . '_smarty/plugins');
            }
        } else {
            $this->smarty->addPluginsDir($templates_path . '_smarty/plugins');
        }
        // Last path is the default smarty plugins directory.
        $this->smarty->addPluginsDir(ROOT_PATH . '/vendor/sifophp/sifo/libraries/Smarty/Plugins');

        $this->smarty->setTemplateDir(ROOT_PATH . '/');  // The templates are taken using the templates.config.php mappings, under the variable $_tpls.

        // Paths definition:
        $templates_path = ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/';
        $this->smarty->setCompileDir($templates_path . '_smarty/compile/');
        $this->smarty->setConfigDir($templates_path . '_smarty/configs/');
        $this->smarty->setCacheDir($templates_path . '_smarty/cache/');

        if (($view_setting = Config::getInstance()->getConfig('views')) && (isset($view_setting['smarty']))) {
            $smarty_settings = $view_setting['smarty'];

            if (isset($smarty_settings['custom_plugins_dir'])) {
                // If is set, this path will be the default smarty plugins directory.
                $this->smarty->addPluginsDir($smarty_settings['custom_plugins_dir']);
            }
            // Set this to false to avoid magical parsing of literal blocks without the {literal} tags.
            $this->smarty->auto_literal = $smarty_settings['auto_literal'];
            $this->smarty->escape_html = $smarty_settings['escape_html'];
        }
    }


    public function fetch($template)
    {
        $this->template_path = $template;

        set_error_handler(array(Views::class, "customErrorHandler"));
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
