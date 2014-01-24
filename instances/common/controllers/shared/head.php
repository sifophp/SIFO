<?php
namespace Common;

use \Sifo\Controller;
use \Sifo\JsPacker;
use \Sifo\CssPacker;

class SharedHeadController extends \Sifo\Controller
{

	protected $css_groups = array( 'default', 'print' );
	protected $js_groups = array( 'default' );

	public function build()
	{
		$this->setLayout( 'shared/head.tpl' );

		$params = $this->getParams();
		$this->assign( 'path', $params['path'] );
		$this->getClass( 'Metadata', false );

		if ( null == \Sifo\Metadata::get() )
		{
			\Sifo\Metadata::setKey( 'default' );
		}

		$this->assign( 'metadata', \Sifo\Metadata::get() );

		$this->assignMedia();
	}

	/**
	 * Sets the static revision. This method gives a different hash every hour.
	 *
	 * ONLY FOR DEMONSTRATION PURPOSES.
	 */
	static public function getStaticRevision()
	{
		return md5( date( 'd-m-Y-H' ) );
	}

	/**
	 * Assign a variable to the tpl with the HTML code to load the JS and CSS files.
	 */
		/**
	 * Assign a variable to the tpl with the HTML code to load the JS and CSS files.
	 */
	protected function assignMedia()
	{

		// On development create all the packed files on the fly:
		if ( \Sifo\Domains::getInstance()->getDevMode() )
		{
			$packer = new \Sifo\JsPacker();
			$packer->packMedia();
			$packer = new \Sifo\CssPacker();
			$packer->packMedia();
		}

		$this->assign( 'media', \Sifo\Config::getInstance()->getConfig( 'css' ) );
		$this->assign( 'css_groups', $this->css_groups );
		$this->assign( 'js_groups', $this->js_groups );

		$this->assign( 'static_rev', $this->getStaticRevision() );
		$this->assign( 'media_module', $this->fetch( 'shared/media_packer.tpl' ) );

	}
}