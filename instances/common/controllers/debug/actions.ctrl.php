<?php

namespace Common;

use Sifo\DebugDataBaseHandler;

class DebugActionsController extends \Sifo\Controller
{
	public $is_json = true;

	/**
	 * @var \Sifo\FilterGet
	 */
	private $filter_get;

	/**
	 * @var DebugDataBaseHandler Class responsible of dealing with stored debugs executions
	 */
	private $debug_persistence_handler;

	/**
	 * @var string Debug execution identifier. Obtained from the "execution_key" query string.
	 */
	private $execution_key;

	/**
	 * @var string Debug execution identifier. Obtained from the "child_execution_key" query string.
	 * Child execution to link to the parent one.
	 */
	private $child_execution_key;

	/**
	 * @var integer 0 or 1 indicating the new pinned status to set
	 */
	private $is_pinned;

	public function __construct()
	{
		parent::__construct();

		$this->debug_persistence_handler = new DebugDataBaseHandler();
		$this->filter_get                = \Sifo\FilterGet::getInstance();
	}

	function build()
	{
		if ( !\Sifo\Domains::getInstance()->getDevMode() )
		{
			throw new \Sifo\Exception_404( 'Debug actions only available while in devel mode' );
		}

		// Disables debug mode in order to do not save the debug for the Linker execution (avoid iinceeeptioooon)
		\Sifo\Domains::getInstance()->setDebugMode( false );

		switch ( $this->filter_get->getString( 'action' ) )
		{
			case 'link':
				return $this->linkAction();
				break;
			case 'pin':
				return $this->pinAction();
				break;
			default:
				return array(
						'success' => array(
								'status' => 'KO',
								'msg'    => "Sifo debug action not properly specified ('action' query string param must be a valid one)."
						)
				);
				break;
		}
	}

	private function linkAction()
	{
		// If the user has passed any execution identifier, show its debug
		if ( !( $this->child_execution_key = $this->filter_get->getString( 'child_execution_key' ) ) || !( $this->execution_key = $this->filter_get->getString( 'execution_key' ) ) )
		{
			return array(
					'status' => 'KO',
					'msg'    => "In order to link a parent sifo debug execution to its parent, we need the two execution keys (missing 'child_execution_key' or 'execution_key' query string params)."
			);
		}

		if ( !$this->debug_persistence_handler->linkChildExecutionToParent( $this->child_execution_key, $this->execution_key ) )
		{
			return array(
					'status' => 'KO',
					'msg'    => "Error while trying to link the two executions."
			);
		}

		return array(
				'success' => array(
						'status' => 'OK',
						'msg'    => "Executions linked successfully."
				)
		);
	}

	private function pinAction()
	{
		// If the user has passed any execution identifier, show its debug
		if ( !( $this->execution_key = $this->filter_get->getString( 'execution_key' ) ) || false === ( $this->is_pinned = $this->filter_get->getInteger( 'is_pinned' ) ) )
		{
			return array(
					'status' => 'KO',
					'msg'    => "In order to [un]pin a sifo debug execution we need its execution key and the new pinned status (missing 'execution_key' or 'is_pinned' query string params)."
			);
		}

		if ( !$this->debug_persistence_handler->pinExecution( $this->execution_key, $this->is_pinned ) )
		{
			return array(
					'status' => 'KO',
					'msg'    => "Error while trying to [un]pin the execution."
			);
		}

		return array(
			'success' => array(
				'status' => 'OK',
				'msg'    => "Execution [un]pinned successfully."
			)
		);
	}
}