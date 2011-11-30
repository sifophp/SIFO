<?php

namespace Common;

class ManagerTemplateLauncherController extends \Sifo\Controller
{

	private $_available_templates;
	private $_selected_template;

	/**
	 * Return the found in use vars in tpl.
	 *
	 * @param array $template
	 */
	private function _getRequiredVars( $template_identifier )
	{
		if ( !( isset( $this->_available_templates[$template_identifier] ) ) )
		{
			trigger_error( "Template identifier not found.", E_USER_ERROR );
		}

		$template_path = ROOT_PATH.'/'.$this->_available_templates[$template_identifier];

		if ( !( $template_source = file_get_contents( $template_path ) ) )
		{
			trigger_error( "{$template_path} not found.", E_USER_ERROR );
		}

		if ( !( preg_match_all( '/{.*\$([a-z_\-]*).*}/', $template_source, $matchs) ) )
		{
			trigger_error( "Not found in use vars in the tpl source.", E_USER_WARNING );
		}

		return array_unique( $matchs[1] );
	}

	/**
 	 * List the available templates in the current instance.
	 *
	 * @return array
	 */
	protected function getAvailableTemplates( )
	{
		$available_templates = $this->getConfig( 'templates' );

		return $available_templates;
	}


	public function build()
	{
		if ( true !== \Sifo\Domains::getInstance()->getDevMode() )
		{
			throw new \SifoException_404( 'User tried to access the rebuild page, but he\'s not in development' );
		}

		$get = \Sifo\FilterGet::getInstance();
		if ( $this->_selected_template = $get->getString( 'template') )
		{
			$this->assign( 'selected_template', $this->_selected_template );
		}

		$post = \Sifo\Filter::getInstance();
		if ( $post_elems = $post->getRawRequest() )
		{
			if ( !( $this->_selected_template ) )
			{
				trigger_error( 'Template identifier not found in the current url.', E_USER_ERROR );
			}

			$this->setLayout( $this->_selected_template );

			foreach ( $post_elems as $key=>$elem )
			{
				if ( !empty( $elem ) )
				{
					$elem = ( false !== stripos( $elem, 'array' ) ) ? $elem:"'$elem'";
					eval( '$var = '.$elem.';' ); // Usefull for $var=dummy and $var=array('k'=>'v');

					$this->assign( $key, $var );
				}
			}
		}
		else
		{

			$this->setLayout( 'manager/tpl_launcher.tpl' );

			$this->_available_templates = $this->getAvailableTemplates();

			if( empty( $this->_available_templates ) )
			{
				trigger_error( "Templates config files is empty. Your instance hasn't available templates yet.", E_USER_WARNING );
			}

			$this->assign( 'available_templates', array_keys( $this->_available_templates ) );


			if ( $this->_selected_template )
			{
				$used_vars = $this->_getRequiredVars( $this->_selected_template );
				$this->assign( 'used_vars', $used_vars );
			}

		}
	}

}