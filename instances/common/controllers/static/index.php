<?php
namespace Common;

class StaticIndexController extends SharedFirstLevelController
{
	public function buildCommon()
	{
		$params = $this->getParams();
		$this->setLayout( 'static/common.tpl' );

		$english_path = \Sifo\Router::getReversalRoute( $params['path'] );
		$template = 'static/' . $english_path . '_' . $this->language .'.tpl';

		if ( $static = $this->fetch( $template ) )
		{
			$this->assign( 'static_content', $static );
		}
		else
		{
			throw new \Sifo\Exception_404( 'Page ' . $params['path'] . ' not found.' );
		}

	}
}

?>