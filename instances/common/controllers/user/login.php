<?php
namespace Common;

class UserLoginController extends SharedFirstLevelController
{
	public function buildCommon()
	{
		$this->setLayout( 'user/login.tpl' );
	}
}