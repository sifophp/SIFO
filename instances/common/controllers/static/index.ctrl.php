<?php

include_once ROOT_PATH . '/instances/default/controllers/shared/firstLevel.ctrl.php';

class StaticIndexController extends SharedFirstLevelController
{
	public function buildCommon()
	{
		$params = $this->getParams();
		$this->setLayout( 'static/common.tpl' );

		$english_path = Router::getReversalRoute( $params['path'] );
		$template = 'static/' . $english_path . '_' . $this->language .'.tpl';

		if ( $static = $this->fetch( $template ) )
		{
			$this->assign( 'static_content', $static );
		}
		else
		{
			throw new Exception_404( 'Page ' . $params['path'] . ' not found.' );
		}

	}
}

?>