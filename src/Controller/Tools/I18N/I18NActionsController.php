<?php

namespace Sifo\Controller\Tools\I18N;

use Common\I18nTranslatorModel;
use Sifo\Controller\Controller;
use Sifo\Exception\Http\NotFound;
use Sifo\Http\Domains;
use Sifo\Http\Filter\FilterPost;

class I18NActionsController extends Controller
{
    public $is_json = true;

    public function build()
    {
        if (!Domains::getInstance()->getDevMode()) {
            throw new NotFound('Translation only available while in devel mode');
        }

        $result = false;

        // Get instance name.
        $params = $this->getParams();
        switch ($params['parsed_params']['action']) {
            case 'addMessage': {
                $result = $this->addMessage($params['parsed_params']['instance']);
            }
                break;
            case 'customizeTranslation': {
                $result = $this->customizeTranslation();
            }
                break;
        }

        return $result;
    }

    /**
     * Add message.
     * @return mixed
     */
    protected function addMessage($instance)
    {
        $message = FilterPost::getInstance()->getString('msgid');
        $translator_model = new I18nTranslatorModel();

        // Check if this message exists in the instances parents.
        // Get selected instance inheritance.
        $instance_domains = $this->getConfig('domains', $instance);

        $instance_inheritance = array();
        if (isset($instance_domains['instance_inheritance'])) {
            $instance_inheritance = $instance_domains['instance_inheritance'];
        }

        if ($translator_model->getMessageInInhertitance($message, $instance_inheritance) > 0) {
            return [
                'status' => 'KO',
                'msg' => 'This message already exists in parent instance. Please, customize it.'
            ];
        } elseif ($translator_model->addMessage($message, $instance)) {
            return ['status' => 'OK', 'msg' => 'Message successfully saved.'];
        }

        return ['status' => 'KO', 'msg' => 'Failed adding message.'];
    }

    /**
     * Customize translation.
     * @return mixed
     */
    protected function customizeTranslation()
    {
        $message = FilterPost::getInstance()->getString('msgid');
        $id_message = null;
        if (is_numeric($message)) {
            $id_message = $message;
        }
        $instance = $this->getParsedParam('instance');
        $translator_model = new I18nTranslatorModel();

        $id_message = $translator_model->getTranslation($message, $id_message);

        $result = ['status' => 'KO', 'msg' => 'This Message or ID doesn\'t exist.'];
        if ($id_message) {
            $result = ['status' => 'OK', 'msg' => 'Message successfully customized.'];
            if (!$translator_model->customizeTranslation($id_message, $instance)) {
                $result = ['status' => 'KO', 'msg' => 'This message is already customized in this instance.'];
            }
        }

        return $result;
    }

    /**
     * Define accepted params for this controller.
     *
     * @return array
     */
    protected function getParamsDefinition()
    {
        $config = [
            'action' => [
                'internal_key' => 'a',
                'is_list' => false,
                'apply_translation' => false
            ],
            'instance' => [
                'internal_key' => 'i',
                'is_list' => false,
                'apply_translation' => false
            ],
        ];
        return $config;
    }
}
