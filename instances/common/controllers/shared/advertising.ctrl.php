<?php
namespace Common;
/**
 * Displays and advertisement block from the ads.config.php depending on how this controller was called.
 *
 * Usage:
 *
 * In your parent controller:
 *
 * // ads_google_skyscrapper will be the key loaded from the config (this is the module_name):
 * $this->addModule( 'ads_google_skyscrapper', 'SharedAdvertising' );
 *
 * In your template:
 * {$modules.ads_google_skyscrapper}
 *
 * Then extend the ads.config, put your client ID and configure any additional
 * blocks.
 *
 * @author Albert Lombarte
 * @version 1.0
 *
 */
class SharedAdvertisingController extends \Sifo\Controller
{
	public function build()
	{
		
		$module = $this->getParam( 'module_name' );

		try
		{
			$ads_config = \Sifo\Config::getInstance()->getConfig( 'ads', $module );
			$this->setLayout( $ads_config['layout'] );
			$this->assign( 'ad', $ads_config );
			
		}
		catch( \Sifo\Exception_Configuration $e )
		{
			// The programmer is an asshole, but page should load anyway:
			trigger_error( "Failed to load banners for module '$module', not present in ads.config.php" );
			$this->setLayout( 'empty.tpl' );
		}

		
	}
}