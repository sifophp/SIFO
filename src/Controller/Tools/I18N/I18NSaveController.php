<?php

namespace Sifo\Controller\Tools\I18N;

use Sifo\Bootstrap;
use Sifo\Controller\Controller;
use Sifo\Database\Database;
use Sifo\Exception\Http\NotFound;
use Sifo\Http\Domains;
use Sifo\Http\Filter\FilterPost;

class I18NSaveController extends Controller
{
    public $is_json = true;

    public function build()
    {
        if (!Domains::getInstance()->getDevMode()) {
            throw new NotFound('Translation only available while in devel mode');
        }

        $filter = FilterPost::getInstance();

        // Get instance name.
        $params = $this->getParams();
        $instance = Bootstrap::$instance;

        if (isset($params['params'][0])) {
            $instance = $params['params'][0];
        }

        $lang = $filter->getString('lang');
        $given_translation = $filter->getUnfiltered('translation');
        $id_message = $filter->getString('id_message');
        $translator_email = (!isset($user['email'])) ? '' : $user['email'];

        if ($given_translation) {
            // TODO: REMOVE this: Temporal fix until magic quotes is disabled:
            $given_translation = str_replace('\\', '', $given_translation);

            $query = 'REPLACE i18n_translations (id_message, lang, translation,author,instance) VALUES(?,?,?,?,?);';

            $result = Database::getInstance()->Execute($query,
                [$id_message, $lang, $given_translation, $translator_email, $instance]);

            if ($result) {
                return [
                    'status' => 'OK',
                    'msg' => 'Successfully saved'
                ];
            }
        }

        return [
            'status' => 'KO',
            'msg' => 'Failed to save the translation'
        ];
    }
}
