<?php
namespace Common;

/**
 * Create common sets of actions for all controllers
 */
abstract class SharedFirstLevelController extends \Sifo\Controller
{
	abstract public function buildCommon();

	public function indexAction()
	{
		// Common actions go here:
		if ( $this->requiresAuth() )
		{
			if ( !$this->isLogged() )
			{
				throw new \Sifo\Exception_403( 'Authentication needed first.' );
			}
		}

		// Common modules to all parent controllers.
		$this->addModule( 'head', 'SharedHead' );
		$this->addModule( 'system_messages', 'SharedSystemMessages' );
		$this->addModule( 'header', 'SharedHeader' );
		$this->addModule( 'footer', 'SharedFooter' );

		// Then execute children (may overwrite values)
		return $this->buildCommon();
	}

	/**
	 * Determines if a class requires authentication before being executed.
	 *
	 * @param string $classname Optional parameter. Name of the class you want to check. Leave unset to use executing class.
	 * @return boolean
	 */
	protected function requiresAuth( $classname = null )
	{
		if ( null === $classname )
		{
			$classname = get_class( $this );
		}

		// Reimplement this in every child instance.
		return false;
	}

	/**
	 * Determines whether the user is correctly logged in.
	 *
	 * @return boolean
	 */
	protected function isLogged()
	{
		return true;
	}
}
