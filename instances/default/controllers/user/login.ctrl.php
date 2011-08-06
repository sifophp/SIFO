<?php

include_once ROOT_PATH . '/instances/default/controllers/shared/firstLevel.ctrl.php';

class UserLoginController extends SharedFirstLevelController
{
	public function buildCommon()
	{
		$this->setLayout( 'user/login.tpl' );
	}
}