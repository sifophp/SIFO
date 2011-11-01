<?php
namespace Common;

namespace Common;

include_once ROOT_PATH . '/instances/common/controllers/shared/firstLevel.ctrl.php';

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
			throw new Sifo\Exception_404( 'Page ' . $params['path'] . ' not found.' );
		}

	}
}

?>