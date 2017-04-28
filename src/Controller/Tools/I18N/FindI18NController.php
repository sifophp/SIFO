<?php

namespace Sifo\Controller\Tools\I18N;

use Sifo\Bootstrap;
use Sifo\Controller\Controller;
use Sifo\Exception\Http\InternalServerError;
use Sifo\Exception\Http\NotFound;
use Sifo\Http\Domains;
use Sifo\Http\Filter\FilterPost;
use Sifo\I18N;

class FindI18NController extends Controller
{
    /**
     * @var FilterPost
     */
    private $filter_post;

    public function build()
    {
        $this->setLayout('manager/findi18n.tpl');

        if (!Domains::getInstance()->getDevMode()) {
            throw new NotFound('Translation only available while in devel mode');
        }

        $available_instances = $this->getFileSystemFiles('instances', true);
        $this->assign('available_instances', $available_instances);

        $available_locales = array();
        foreach ($available_instances as $instance) {
            $available_locales[$instance] = $this->getFilesystemFiles("instances/$instance/locale");
        }
        $this->assign('available_locales', $available_locales);

        $this->filter_post = FilterPost::getInstance();
        if ($this->filter_post->isSent('instance')) {
            $this->getI18nStats();
        } else {
            $this->assign('selected_charset', 'utf-8');
        }
    }

    /**
     * Extracts from the filesystem all the files under a path.
     * If the flag only_dirs is set to true returns only the directories names.
     *
     * @param string $relative_path
     * @param bool $only_dirs
     * @return array
     */
    public function getFileSystemFiles($relative_path, $only_dirs = false)
    {
        $files = array();

        // Extract directories:
        $iterator = new \DirectoryIterator(ROOT_PATH . "/$relative_path");

        foreach ($iterator as $file_info) {
            $file = $file_info->getFilename();

            // Exclude .svn, .cache and any other file starting with .
            if (0 !== strpos($file, '.')) {
                if (!$only_dirs || $file_info->isDir()) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    /**
     * Gets the selected parameters (instance, locale and charset), parses all literals (message used in translation method) inside this instance
     * and computes which of them exists in the materialized translations file in order to assign them to the view.
     */
    private function getI18nStats()
    {
        $selected_instance = $this->filter_post->getString('instance');
        $selected_raw_locale = $this->filter_post->getString('locale');

        list($file_type, $language_code, $country_code_and_extension) = explode('_', $selected_raw_locale);
        $this->assign('language', $language_code);
        $locale = $language_code . '_' . $country_code_and_extension[0] . $country_code_and_extension[1];

        $literals = $this->getLiterals($selected_instance);
        $this->assign('literals', $literals);

        try {
            I18N::setDomain($file_type, $locale, $selected_instance);
            $translations_i18 = I18N::$translations;

            $missing_messages = array_diff_key($literals, $translations_i18[$file_type . '_' . $locale]);
            $leftover_messages = array_diff_key($translations_i18[$file_type . '_' . $locale], $literals);

            $this->assign('selected_instance', $selected_instance);
            $this->assign('missing_messages', $missing_messages);
            $this->assign('leftover_messages', $leftover_messages);
            $this->assign('selected_locale', $selected_raw_locale);
            $this->assign('selected_charset', $this->filter_post->getString('charset'));
        } catch (InternalServerError $exception) {
            $this->assign('error', $exception->getMessage());
        }
    }

    /**
     * Parses all templates, models, controllers, configs and Smarty plugins searching for strings used inside translation methods and returns them structured in an array.
     *
     * @param string $instance Sifo instance to search in
     * @return array structured array where:
     *      array key: The string to be translated (message).
     *      array value: The different files which uses this message separated by commas.
     */
    public function getLiterals($instance)
    {
        $path = Bootstrap::$application . "/$instance";

        // Parse all templates
        $literals_groups['tpl'] = $this->extractStringsForTranslation("$path/templates", $instance, true);

        // Parse all controllers:
        $literals_groups['controllers'] = $this->extractStringsForTranslation("$path/src", $instance, false);

        // Parse all form configs:
        $literals_groups['forms'] = $this->extractStringsForTranslation("$path/etc", $instance, false);

        // Smarty plugins:
        $sifo_plugins_path = ROOT_PATH . '/vendor/sifophp/sifo/src/Smarty-sifo-plugins';
        $literals_groups['smarty'] = $this->extractStringsForTranslation($sifo_plugins_path, 'libs', false);

        // Your instance plugins:
        $instance_plugins = $path . '/templates/_smarty/plugins';
        if (is_dir($instance_plugins)) {
            $literals_groups['smarty'] = array_merge($literals_groups['smarty'],
                $this->extractStringsForTranslation($instance_plugins, $instance, false));
        }

        $final_literals = array();

        foreach ($literals_groups as $group) {
            foreach ($group as $literal => $relative_path) {
                if (array_key_exists($literal, $final_literals)) {
                    $final_literals[$literal] = ($final_literals[$literal] . ", " . $relative_path);
                } else {
                    $final_literals[$literal] = $relative_path;
                }
            }
        }

        return $final_literals;
    }

    /**
     * Search for all literals/strings used in translation methods inside a $path
     *
     * @param string $path
     * @param string $instance
     * @param bool $in_templates
     * @return array structured array where:
     *      array key: The string to be translated (message).
     *      array value: The different files which uses this message separated by commas.
     */
    private function extractStringsForTranslation($path, $instance, $in_templates = false)
    {
        // Parse .php files:
        $literals = array();
        $file_list = array();

        if (!$in_templates) {
            exec("find * $path |grep .php$", $file_list);
        } else {
            exec("find * $path |grep .tpl$", $file_list);
        }

        foreach ($file_list as $file_path) {
            $tpl_text = shell_exec("cat {$file_path}");

            if (!$in_templates) {
                // $this->translate functions
                preg_match_all("/translate\s*\(\s*\'([^\']+)\'[^\)]*\)/", $tpl_text, $translate_single_quotes);
                preg_match_all("/translate\s*\(\s*\"([^\"]+)\"[^\)]*\)/", $tpl_text, $translate_double_quotes);

                // \Sifo\\Sifo\\Sifo\I18N::getTranslation functions
                preg_match_all("/getTranslation\s*\(\s*\'([^\']+)\'[^\)]*\)/", $tpl_text,
                    $i18n_translate_single_quotes);
                preg_match_all("/getTranslation\s*\(\s*\"([^\"]+)\"[^\)]*\)/", $tpl_text,
                    $i18n_translate_double_quotes);

                // \Sifo\FlashMessages
                preg_match_all("/FlashMessages::set\s*\(\s*\'([^\']+)\'[^\)]*\)/", $tpl_text,
                    $flash_translate_single_quotes);
                preg_match_all("/FlashMessages::set\s*\(\s*\"([^\"]+)\"[^\)]*\)/", $tpl_text,
                    $flash_translate_double_quotes);

                $file_literals = array_unique(array_merge(
                    $translate_single_quotes[1],
                    $translate_double_quotes[1],
                    $i18n_translate_single_quotes[1],
                    $i18n_translate_double_quotes[1],
                    $flash_translate_single_quotes[1],
                    $flash_translate_double_quotes[1]
                ));
            } else {
                // {t}Search 'T' blocks{/t}
                preg_match_all("/\{t([^\{\}]*)\}([^\{\}]+)\{\/t[^\}]*\}/", $tpl_text, $matches);
                $file_literals = array_unique($matches[2]);
            }

            if (preg_match("/{$instance}\/(.+)$/", $file_path, $matches)) {
                $file_relative_path = $matches[1];
            }

            foreach ($file_literals as $literal) {
                if (array_key_exists($literal, $literals)) {
                    $literals[$literal] = ($literals[$literal] . ", " . $file_relative_path);
                } else {
                    $literals[$literal] = $file_relative_path;
                }
            }
        }
        return $literals;
    }
}
