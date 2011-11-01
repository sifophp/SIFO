<?php
namespace Common;

namespace Common;

include_once ROOT_PATH . '/instances/common/controllers/shared/firstLevel.ctrl.php';

class UserLoginController extends SharedFirstLevelController
{
	public function buildCommon()
	{
		$this->setLayout( 'user/login.tpl' );
	}
}