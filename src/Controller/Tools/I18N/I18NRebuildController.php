<?php

namespace Sifo\Controller\Tools\I18N;

use Common\I18nTranslatorModel;
use Sifo\Bootstrap;
use Sifo\Controller\Controller;
use Sifo\Exception\Http\NotFound;
use Sifo\Http\Domains;

class I18NRebuildController extends Controller
{
    public $is_json = true;

    public function build()
    {
        if (!Domains::getInstance()->getDevMode()) {
            throw new NotFound('Translation only available while in devel mode');
        }

        $translator = new I18nTranslatorModel();

        // Get instance name.
        $params = $this->getParams();
        $instance = Bootstrap::$instance;
        if (isset($params['params'][0])) {
            $instance = $params['params'][0];
        }

        // Get selected instance inheritance.
        $instance_domains = $this->getConfig('domains', $instance);

        $instance_inheritance = [];
        if (isset($instance_domains['instance_inheritance'])) {
            $instance_inheritance = $instance_domains['instance_inheritance'];
        }

        $is_parent_instance = false;
        if (empty($instance_inheritance) || (count($instance_inheritance) == 1 && $instance_inheritance[0] == 'common')) {
            $is_parent_instance = true;
        }

        // Get languages.
        $langs_in_DB = $translator->getDifferentLanguages();

        foreach ($langs_in_DB as $l) {
            $language_list[] = $l['l10n'];
        }

        foreach ($language_list as $language) {
            $language_str = $translator->getTranslations($language, $instance, $is_parent_instance);

            foreach ($language_str as $position => $str) {
                $msgid = $str['message'];
                $msgstr = ($str['translation'] == null ? '' : $str['translation']);
                $translations[$msgid][$language] = $msgstr;
            }
            unset($language_str);
        }

        $messages_order = array_map('mb_strtolower', array_keys($translations));
        array_multisort($messages_order, SORT_STRING, $translations);

        $failed = array();
        $result = true;


        foreach ($language_list as $language) {
            $buffer = '';
            $empty_strings_buffer = '';
            $empty[$language] = 0;

            foreach ($translations as $msgid => $msgstr) {
                $msgstr[$language] = (isset($msgstr[$language])) ? trim($msgstr[$language]) : null;
                if (!empty($msgstr[$language])) {
                    $item = $this->buildItem($msgid, $msgstr[$language]);
                    $buffer .= $item;
                } else {
                    $item = $this->buildItem($msgid, $msgid);
                    $empty[$language]++;
                    $empty_strings_buffer .= $item;
                }
            }

            $include_parent_instance = $this->getIncludeInheritance($instance, $language);

            $buffer = <<<PHP
<?php
$include_parent_instance

// Translations file, lang='$language'
// Empty strings: $empty[$language]
$empty_strings_buffer
// Completed strings:
$buffer

return $translations;
PHP;
            $path = ROOT_PATH . '/instances/' . $instance . '/locale/messages_' . $language . '.php';
            $write = @file_put_contents($path, $buffer);

            if (!$write) {
                $failed[] = $language;
            }

            $result = $result && $write;
        }

        if ($result) {
            return [
                'status' => 'OK',
                'msg' => 'Successfully saved'
            ];
        }


        return [
            'status' => 'KO',
            'msg' => 'Failed to save the translation:' . implode("\n", $failed)
        ];
    }

    protected function buildItem($msgid, $translation)
    {
        $item = '$translations["' . str_replace('"', '\"', $msgid) . '"] = ' . '"';
        $item .= str_replace('"', '\"', $translation);
        $item .= "\";\n";

        return $item;
    }

    protected function getIncludeInheritance($instance, $language)
    {
        $instance_domains = $this->getConfig('domains', $instance);
        $instance_inheritance = [];
        if (isset($instance_domains['instance_inheritance'])) {
            $instance_inheritance = $instance_domains['instance_inheritance'];
        }

        $instance_parent = array_pop($instance_inheritance);

        if (!empty($instance_parent)) {
            return "include ROOT_PATH . '/instances/{$instance_parent}/locale/messages_$language.php';";
        }
        return '';
    }
}
