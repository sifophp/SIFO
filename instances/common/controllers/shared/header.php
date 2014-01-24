<?php

namespace Common;

class SharedHeaderController extends \Sifo\Controller
{
	public function build()
	{
		$this->setLayout( 'shared/header.tpl' );
	}
}
?>