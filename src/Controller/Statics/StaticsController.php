<?php

namespace Sifo\Controller\Statics;

use Sifo\Controller\Controller;
use Sifo\Exception\Http\NotFound;
use Sifo\Http\Router;

class StaticsController extends Controller
{
    public function build()
    {
        $params = $this->getParams();
        $this->setLayout('static/common.tpl');

        $english_path = Router::getReversalRoute($params['path']);
        $template = 'static/' . $english_path . '_' . $this->language . '.tpl';

        if ($static = $this->fetch($template)) {
            $this->assign('static_content', $static);
        } else {
            throw new NotFound('Page ' . $params['path'] . ' not found.');
        }

    }
}
