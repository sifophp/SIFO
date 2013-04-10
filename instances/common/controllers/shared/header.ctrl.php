<?php

namespace Common;

class SharedHeaderController extends \Sifo\Controller
{
	public function indexAction()
	{
		$this->setLayout( 'shared/header.tpl' );
	}
}
?>