<?php

namespace Common;

/**
 * Example of implementation:
 *
 * // Anywhere:
 * echo '<a href="' . new OAuthTwitterModel()->getAuthenticationUrl() . '">Authorize with Twitter</a>';
 *
 * // In the callback controller:
 * $token = new OAuthTwitterModel()->getAuth( $oauth_token );
 * new OAuthTwitterModel()->testGetFriends( $token['oauth_token'], $token['oauth_token_secret'] );
 */
include_once ROOT_PATH . '/libs/OpenInviter/openinviter.php';

class Inviter
{

	/**
	 * Facebook Object
	 * @var facebook
	 */
	protected $inviterObj;
	protected $active_plugins = array();

	public function __construct()
	{
		$this->inviterObj = new OpenInviter();
		$this->active_plugins = $this->inviterObj->getPlugins();
	}

	public function getContacts( $user, $password, $provider )
	{
		if ( !$user || !$password )
		{
			return false;
		}

		if ( !isset( $this->inviterObj ) || !is_object( $this->inviterObj ) )
		{
			$this->inviterObj = new OpenInviter();
		}

		$this->inviterObj->startPlugin( $provider );
		$this->inviterObj->login( $user, $password );

		$contacts = $this->inviterObj->getMyContacts();
		$error = $this->inviterObj->getInternalError();

		return $contacts;
	}

}