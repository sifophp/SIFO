<?php
/**
 * LICENSE
 * 
 * Copyright 2010 Albert Lombarte
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

include ROOT_PATH . '/libs/EpiClasses/EpiCurl.php';

/**
 * Interacts with the Twitter API, launching the same request for every given account in parallel.
 *
 * At the time of programming this class OAuth was not supported by Twitter.
 *
 * Usage:
 * $twitter = APITwitter::getInstance();
 * $twitter->setAccount( 'user1', 'plainpass1' ) );
 * $twitter->getFavorites(); // The function call must be done after all accounts are added.
 * (loop suggested with all your accounts)
 * $twitter->setAccount( 'user2', 'pass2' ) );
 * $twitter->getFavorites(); // The function call must be done after all accounts are added.
 * (end of loop)
 * $result = $twitter->fetch();
 *
 * The code above would return the favorites for all added accounts inside an array:
 *
 * Limitations: Public methods don't need setAccount, but YOU CAN'T MIX REQUESTS THAT ARE DIFFERENT IN THE $require_authentication.
 *
 * @author Albert Lombarte <alombarte@gmail.com>
 *
 */
class APITwitter {

	/**
	 * Twitter API return format. You can request the following formats: xml, json, rss, atom.
	 *
	 */
	const RETURN_FORMAT = 'json';

	/**
	 * A name where requests with no-auth will pe placed in the array.
	 *
	 */
	const NO_ACCOUNT_NAME ='@public';

	/**
	 * Client sending the updates.
	 *
	 */
	const SOURCE = 'splitweet';

	/**
	 * Stores the singleton. Creates an object for every different return format.
	 *
	 * @var	Config
	 */
	static protected $instance;

	/**
	 * Multi account storage.
	 *
	 * @var string
	 */
	static protected $accounts;

	/**
	 * Current working account. Uses the twitter username.
	 *
	 * @var string
	 */
	static protected $active_account;

	/**
	 * Private constructor. Not allowed to instantiate classe using new APITwitter().
	 *
	 * @param $output_format Format you want to get the results. Twitter supports: xml, json, rss and atom.
	 */
	private function __construct( $output_format )
	{
		$this->format = $output_format;
		$this->setAccount( self::NO_ACCOUNT_NAME , 'unused' );
	}

	/**
	 * Singleton of the Twitter class.
	 *
	 * @param $output_format Format you want to get the results. Twitter supports: xml, json, rss and atom. Not all methods are available for every format. Read Twitter API.
	 *
	 * @return object Config
	 */
	public static function getInstance( $output_format = self::RETURN_FORMAT )
	{
		if ( !isset ( self::$instance[$output_format] ) )
		{
			self::$instance[$output_format] = new self( $output_format );
		}

		return self::$instance[$output_format];
	}

	/**
	 * Add an account in the stack. You need to call this method when you need to perform non-public operations.
	 *
	 * @param string $username Twitter username.
	 * @param string $password Twitter password, plain, as Twitter needs it.
	 */
	public function setAccount( $username, $password )
	{
		self::$accounts[$username] = array(
			'credentials' => sprintf( "%s:%s", $username, $password ),
			'methods' => array()
		);

		self::$active_account = $username;

		return $username;
	}

	/**
	 * Empties the accounts array, so another function can be called with new accounts.
	 *
	 */
	public function resetAccounts()
	{
		self::$accounts = null;
	}


	/**
	 * Returns the 20 most recent statuses from non-protected users who have set a custom user icon.
	 * Does not require authentication.
	 * Note that the public timeline is cached for 60 seconds so requesting it more often than that is a waste of resources.
	 *
	 * formats: xml, json, rss, atom
	 */
	public function getPublicTimeline()
	{
		$url = sprintf( "http://twitter.com/statuses/public_timeline.%s", $this->format );
		return $this->addRequest( 'getPublicTimeline', $url );
	}

	/**
	 * Returns the 20 most recent statuses posted by the authenticating user and that user's friends. This is the equivalent of /home on the Web.
	 *
	 * formats: xml, json, rss, atom
	 *
	 * @param integer $page Number of page to retrieve,.
	 * @param integer $count Specifies the number of statuses to retrieve. May not be greater than 200.
	 * @param integer $since_id Returns only statuses with an ID greater than (that is, more recent than) the specified ID.
	 * @param string $max_id Returns only statuses with an ID less than (that is, older than) the specified ID..
	 * @return array
	 */
	public function getFriendsTimeline( $page = false, $count = 10, $since_id= false, $max_id= false )
	{
		$url = sprintf("http://twitter.com/statuses/friends_timeline.%s", $this->format);

		if ( $page ) 		$params[] = "page=$page";
		if ( $count ) 		$params[] = "count=$count";
		if ( $max_id ) 		$params[] = "max_id=$max_id";
		if ( $since_id ) 	$params[] = "since_id=$since_id";

		if ( $page || $count || $max_id || $since_id  )
		{
			$url .= '?' . implode( '&', $params );
		}

		return $this->addRequest( 'getFriendsTimeline', $url, true );
	}

	/**
	 * Returns the 20 most recent statuses posted from the authenticating user. It's also possible to request
	 * another user's timeline via the id parameter below. This is the equivalent of the Web /archive page
	 * for your own user, or the profile page for a third party.
	 *
	 * @param string $id Specifies the ID or screen name of the user for whom to return the friends_timeline. E.g: 1234, or 'bob'
	 * @param integer $count Specifies the number of statuses to retrieve. May not be greater than 200, default 20.
	 * @param string $since Narrows the returned results to just those statuses created after the specified HTTP-formatted date, up to 24 hours old.  The same behavior is available by setting an If-Modified-Since header in your HTTP request.
	 * @param integer $since_id Returns only statuses with an ID greater than (that is, more recent than) the specified ID
	 * @param integer $page Page to retrieve.
	 * @return array
	 */
	public function getUserTimeline( $id = false, $count = false, $since = false, $since_id = false, $page = false )
	{
		if ( false !== $id )
		{
			$url = sprintf("http://twitter.com/statuses/user_timeline/%s.%s", $id, $this->format);
		}
		else
		{
			$url = sprintf("http://twitter.com/statuses/user_timeline.%s", $this->format);
		}

		if ( $page ) 		$params[] = "page=$page";
		if ( $count ) 		$params[] = "count=$count";
		if ( $since ) 		$params[] = "since=".urlencode( $since );
		if ( $since_id ) 	$params[] = "since_id=$since_id";

		if ( $page || $count || $since || $since_id  )
		{
			$url .= '?' . implode( '&', $params );
		}

		return $this->addRequest( 'getUserTimeline', $url, false );
	}

	/**
	 * Returns a single status, specified by the id parameter below.  The status's author will be returned inline.
	 *
	 * @param integer $id The numerical ID of the status you're trying to retrieve (passed as string for non 64-bit machines)
	 * @return array
	 */
	public function showStatus( $id )
	{
		$url = sprintf( "http://twitter.com/statuses/show/%s.%s", $id, $this->format );
		return $this->addRequest( 'showStatus', $url );
	}

	/**
	 * Updates the authenticating user's status. Requires the status parameter specified below.
	 *
	 * @param string $status The text of your status update.
	 * @param integer $in_reply_to_id The ID of an existing status that the status to be posted is in reply to.
	 * 			This implicitly sets the in_reply_to_user_id attribute of the resulting status to the user ID of the message being replied to.
	 * 			Invalid/missing status IDs will be ignored.
	 * @return array
	 */
	public function updateStatus( $status, $in_reply_to_id = false )
	{
		$status = urlencode( stripslashes( $status ) );
		$url = sprintf( "http://twitter.com/statuses/update.xml?source=" . self::SOURCE . "&status=%s", $status );

		if ( $in_reply_to_id )
		{
			$url .= "&in_reply_to_status_id=$in_reply_to_id";
		}

		return $this->addRequest( 'updateStatus', $url, true, true );
	}

	/**
	 * Returns the 20 most recent @replies (status updates prefixed with @username) for the authenticating user.
	 *
	 * @param integer $page Retrieves the 20 next most recent replies
	 * @param string $since Narrows the returned results to just those replies created after the specified HTTP-formatted date, up to 24 hours old.
	 * @param integer $since_id Returns only statuses with an ID greater than (that is, more recent than) the specified ID
	 * @return array
	 */
	public function getReplies($page = false, $count = 10, $since_id = false, $max_id = false )
	{
		$url = sprintf( "http://twitter.com/statuses/mentions.%s", $this->format );

		if ( $page ) 		$params[] = "page=$page";
		if ( $count ) 		$params[] = "count=$count";
		if ( $since_id ) 	$params[] = "since_id=$since_id";
		if ( $max_id ) 		$params[] = "max_id=$max_id";

		if ( $page || $count || $since_id || $max_id  )
		{
			$url .= '?' . implode( '&', $params );
		}

		return $this->addRequest( 'getReplies', $url, true );
	}

	/**
	 * Destroys the status specified by the required ID parameter.  The authenticating user must be the author of the specified status.
	 *
	 * @param string $id The ID of the status to destroy. (Marked as string to allow LARGE numbers)
	 * @return array
	 */
	public function destroyStatus( $id )
	{
		$url = sprintf( "http://twitter.com/statuses/destroy/%s.%s", $id, $this->format );
		return $this->addRequest( 'destroyStatus', $url, true, true );
	}

	/**
	 * Returns up to 100 of the authenticating user's friends who have most recently updated, each with current status inline.
	 *
	 * @param string $id Optional. The ID or screen name of the user for whom to request a list of friends.
	 * @param integer $page Retrieves the next 100 friends.
	 * @return array
	 */
	public function getFriends( $id = false, $page = false )
	{
		if ( false !== $id )
		{
			$url = sprintf( "http://twitter.com/statuses/friends/%s.%s", $id, $this->format );
			$require_credentials = false;
		}
		else
		{
			$url = sprintf( "http://twitter.com/statuses/friends.%s", $this->format );
			$require_credentials = true;
		}
		if ( $page )
		{
			$url .= "?page=$page";
		}

		return $this->addRequest( 'getFriends', $url, $require_credentials );
	}

	/**
	 * Returns the authenticating user's followers, each with current status inline. They are ordered by the order in which they joined Twitter (this is going to be changed).
	 *
	 * @param string $id Optional. The ID or screen name of the user for whom to request a list of friends.
	 * @param integer $page Retrieves the next 100 followers.
	 * @return array
	 */
	public function getFollowers( $id = false, $page = false )
	{
		if ( false !== $id )
		{
			$url = sprintf( "http://twitter.com/statuses/followers/%s.%s", $id, $this->format );
		}
		else
		{
			$url = sprintf( "http://twitter.com/statuses/followers.%s", $this->format );
		}
		if ( $page )
		{
			$url .= "?page=$page";
		}

		return $this->addRequest( 'getFollowers', $url, true );
	}


	/**
	 * Returns extended information of a given user, specified by ID or screen name as per the required id parameter below.
	 * This information includes design settings, so third party developers can theme their widgets according to a given user's preferences.
	 * You must be properly authenticated to request the page of a protected user.
	 *
	 * @param string $id The ID or screen name of a user
	 * @param unknown_type $email May be used in place of "id" parameter above.
	 * @return array
	 */
	public function showUser( $id, $email = false )
	{
		// If an email is passed retrieve info by email instead of ID.
		if ( false !== $email )
		{
			$url = sprintf( "http://twitter.com/users/show.%s?email=%s", $this->format, $email );
		}
		else
		{
			$url = sprintf( "http://twitter.com/users/show/%s.%s", $id, $this->format );
		}

		return $this->addRequest( 'showUser', $url, false );
	}


	/**
	 * Returns a list of the 20 most recent direct messages sent to the authenticating user.
	 * The XML and JSON versions include detailed information about the sending and recipient users.
	 *
	 * @param string $since Narrows the resulting list of direct messages to just those sent after the specified HTTP-formatted date, up to 24 hours old
	 * @param integer $since_id Returns only direct messages with an ID greater than (that is, more recent than) the specified ID
	 * @param integer $page Retrieves the 20 next most recent direct messages.
	 * @return array
	 */
	public function getDirectMessages( $page = false, $count = 10, $since_id = false, $max_id = false )
	{

		$url = sprintf( "http://twitter.com/direct_messages.%s", $this->format );

		if ( $page ) 		$params[] = "page=$page";
		if ( $count ) 		$params[] = "count=$count";
		if ( $since_id ) 	$params[] = "since_id=$since_id";
		if ( $max_id ) 		$params[] = "max_id=$max_id";

		if ( $page || $count || $since_id || $max_id  )
		{
			$url .= '?' . implode( '&', $params );
		}

		return $this->addRequest( 'getDirectMessages', $url, true );
	}

	/**
	 * Returns a list of the 20 most recent direct messages sent by the authenticating user.
	 * The XML and JSON versions include detailed information about the sending and recipient users.
	 *
	 * @param string $since Narrows the resulting list of direct messages to just those sent after the specified HTTP-formatted date, up to 24 hours old
	 * @param integer $since_id Returns only direct messages with an ID greater than (that is, more recent than) the specified ID
	 * @param integer $page Retrieves the 20 next most recent direct messages.
	 * @return array
	 */
	public function getSentMessages( $page = false, $since = false, $since_id = false )
	{
		$url = sprintf( "http://twitter.com/direct_messages/sent.%s", $this->format );

		if ( $page ) 		$params[] = "page=$page";
		if ( $since ) 		$params[] = "since=".urlencode( $since );
		if ( $since_id ) 	$params[] = "since_id=$since_id";

		if ( $page || $since || $since_id  )
		{
			$url .= '?' . implode( '&', $params );
		}

		return $this->addRequest( 'getSentMessages', $url, true );
	}

	/**
	 * Sends a new direct message to the specified user from the authenticating user.
	 * Returns the sent message in the requested format when successful.
	 * @param string $user The ID or screen name of the recipient user
	 * @param string $text The text of your direct message. Under 140 chars.
	 * @return array
	 */
	public function newDirectMessage( $user, $text )
	{
		// TODO: Look if encode/decode is well done:
		$text = urlencode( stripslashes( $text ) );
		$url = sprintf( "http://twitter.com/direct_messages/new.%s?user=%s&text=%s", $this->format, $user, $text );

		return $this->addRequest( 'newDirectMessage', $url, true, true );
	}

	/**
	 * Destroys the direct message specified in the required ID parameter. The authenticating user must be the recipient of the specified direct message.
	 *
	 * @param integer $id The ID of the direct message to destroy.
	 * @return array
	 */
	public function destroyDirectMessage( $id )
	{
		$url = sprintf( "http://twitter.com/direct_messages/destroy/%s.%s", $id, $this->format );
		return $this->addRequest( 'destroyDirectMessage', $url, true );
	}


	/**
	 * Befriends the user specified in the ID parameter as the authenticating user.
	 * Returns the befriended user in the requested format when successful.
	 * Returns a string describing the failure condition when unsuccessful.
	 *
	 * @param string $id The ID or screen name of the user to befriend.
	 * @param boolean $follow Enable notifications for the target user in addition to becoming friends.
	 * @return array
	 */
	public function createFriendship( $id, $follow = false )
	{
		$url = sprintf( "http://twitter.com/friendships/create/%s.%s", $id, $this->format );

		if ( $follow )
		{
			$url .= '?follow=true';
		}

		return $this->addRequest( 'createFriendship', $url, true, true );
	}

	/**
	 * Discontinues friendship with the user specified in the ID parameter as the authenticating user.
	 * Returns the un-friended user in the requested format when successful.
	 * Returns a string describing the failure condition when unsuccessful.
	 *
	 * @param string $id The ID or screen name of the user to befriend.
	 * @return array
	 */
	public function destroyFriendship( $id )
	{
		$url = sprintf( "http://twitter.com/friendships/destroy/%s.%s", $id, $this->format );
		return $this->addRequest( 'destroyFriendship', $url, true, true );
	}

	/**
	 * Tests if a friendship exists between two users.
	 *
	 * @param unknown_type $user_a The ID or screen_name of the first user to test friendship for.
	 * @param unknown_type $user_b The ID or screen_name of the second user to test friendship for.
	 */
	public function existsFriendship( $user_a, $user_b )
	{
		$url = sprintf("http://twitter.com/friendships/exists.%s?user_a=%s&user_b=%s", $this->format, $user_a, $user_b );
		return $this->addRequest( 'existsFriendship', $url, true );
	}

	/**
	 * Returns an HTTP 200 OK response code and a representation of the requesting user if authentication was successful;
	 * returns a 401 status code and an error message if not.
	 * Use this method to test if supplied user credentials are valid.
	 *
	 * @return array
	 */
	public function verifyCredentials()
	{
		$url = sprintf( "http://twitter.com/account/verify_credentials.%s", $this->format );
		return $this->addRequest( 'verifyCredentials', $url, true );
	}

	/**
	 * Ends the session of the authenticating user, returning a null cookie.
	 * Use this method to sign users out of client-facing applications like widgets.
	 *
	 * @return array
	 */
	public function endSession()
	{
		$url = sprintf( "http://twitter.com/account/end_session.%s", $this->format );
		return $this->addRequest( 'endSession', $url, true );
	}

	/**
	 * Sets values that users are able to set under the "Account" tab of their settings page. Only the parameters specified will be updated;
	 * to only update the "name" attribute, for example, only include that parameter in your request.
	 *
	 * @param string $name Maximum of 40 characters
	 * @param string $email Maximum of 40 characters. Must be a valid email address.
	 * @param string $url Maximum of 100 characters. Will be prepended with "http://" if not present.
	 * @param string $location Maximum of 30 characters. The contents are not normalized or geocoded in any way.
	 * @param string $description Maximum of 160 characters.
	 * @return array
	 */
	public function updateProfile( $name = false, $email = false, $url = false, $location = false, $description = false )
	{
		if ( $name || $email ||$url || $location || $description )
		{
			$url = sprintf("http://twitter.com/account/update_profile.%s", $this->format);

			if ( $name ) 		$params[] = "name=$name";
			if ( $email ) 		$params[] = "email=".urlencode( $email );
			if ( $email ) 		$params[] = "url=" . urlencode( $url );
			if ( $location ) 	$params[] = "location=".urlencode( $location );
			if ( $description ) $params[] = "description=".urlencode( $description );

			$url .= '?' . implode( '&', $params );

			return $this->addRequest( 'updateProfile', $url, true, true );
		}
	}


	/**
	 * Returns the 20 most recent favorite statuses for the authenticating user or user specified by the ID parameter in the requested format.
	 *
	 * @param string $id The ID or screen name of the user for whom to request a list of favorite statuses.
	 * @param integer $page Retrieves the 20 next most recent favorite statuses
	 * @return array
	 */
	public function getFavorites( $id = false, $page = false )
	{
		if ( $id == false )
		{
			$url = sprintf( "http://twitter.com/favorites.%s", $this->format );
		}
		else
		{
			$url = sprintf( "http://twitter.com/favorites/%s.%s", $id, $this->format );
		}

		if ( $page ) {
			$url .= sprintf( "?page=%d", $page );
		}

		return $this->addRequest( 'getFavorites', $url, true );
	}

	/**
	 * Favorites the status specified in the ID parameter as the authenticating user.
	 * Returns the favorite status when successful.
	 *
	 * @param integer $id The ID of the status to favorite
	 * @return array
	 */
	public function createFavorite( $id )
	{
		$url = sprintf( "http://twitter.com/favorites/create/%s.%s", $id, $this->format );
		return $this->addRequest( 'createFavorite', $url, true, true );
	}

	/**
	 * Un-favorites the status specified in the ID parameter as the authenticating user.
	 * Returns the un-favorited status in the requested format when successful.
	 *
	 * @param integer $id The ID of the status to un-favorite
	 * @return array
	 */
	public function destroyFavorite( $id )
	{
		$url = sprintf( "http://twitter.com/favorites/destroy/%s.%s", $id, $this->format );
		return $this->addRequest( 'destroyFavorite', $url, true, true );
	}

	/**
	 * Enables notifications for updates from the specified user to the authenticating user.
	 * Returns the specified user when successful.
	 *
	 * @param string $id The ID or screen name of the user to follow.
	 * @return array
	 */
	public function follow( $id )
	{
		$url = sprintf( "http://twitter.com/notifications/follow/%s.%s", $id, $this->format );
		return $this->addRequest( 'follow', $url, true, true );
	}


	/**
	 * Disables notifications for updates from the specified user to the authenticating user. Returns the specified user when successful.
	 *
	 * NOTE: The Notification Methods require the authenticated user to already be friends with the
	 * specified user otherwise the error "there was a problem following the specified user" will be returned
	 *
	 * @param string $id The ID or screen name of the user to leave
	 */
	public function stopFollowing( $id )
	{
		$url = sprintf( "http://twitter.com/notifications/leave/%s.%s", $id, $this->format );
		return $this->addRequest( 'stopFollowing', $url, true, true );
	}

	/**
	 * Blocks the user specified in the ID parameter as the authenticating user.
	 * Returns the blocked user in the requested format when successful.
	 * You can find out more about blocking in the Twitter Support Knowledge Base.
	 *
	 * @param string $id The ID or screen_name of the user to block
	 * @return array
	 */
	public function blockUser( $id )
	{
		$url = sprintf( "http://twitter.com/blocks/create/%s.%s", $id, $this->format );
		return $this->addRequest( 'blockUser', $url, true, true );
	}

	/**
	 * Unblocks the user specified in the ID parameter as the authenticating user.
	 * Returns the unblocked user in the requested format when successful.
	 *
	 * @param string $id The ID or screen_name of the user to unblock
	 * @return array
	 */
	public function unblockUser( $id )
	{
		$url = sprintf( "http://twitter.com/blocks/destroy/%s.%s", $id, $this->format );
		return $this->addRequest( 'unblockUser', $url, true, true );
	}


	/**
	 * Returns the lists available for the given user.
	 *
	 * @param string $user
	 * @return array
	 */
	public function getLists( $user )
	{
		$url = sprintf( 'http://api.twitter.com/1/%s/lists.%s', $user, $this->format );
		return $this>addRequest( 'getLists', $url, true, false );
	}

	public function searchTerm( $term, $num_results, $exclude_users = null )
	{
		$term = str_replace( ' ', '+', $term );

		$url = sprintf( "http://search.twitter.com/search.%s?rpp=%s&q=%s", $this->format, $num_results, $term );

		// Exclude mentions from @brand (own tweets)
		if ( false === strpos( $term, '+' ) )
		{
			$url .= "+-from%3A$term";
		}

		return $this->addRequest( 'searchMentions|'.$term, $url  );
	}

	private function addRequest( $title, $url, $require_credentials = false, $http_post = false )
	{
		// Store where and how to request:
		self::$accounts[self::$active_account]['methods'][$title]['url'] = $url;
		self::$accounts[self::$active_account]['methods'][$title]['require_credentials'] = $require_credentials;
		self::$accounts[self::$active_account]['methods'][$title]['http_post'] = $http_post;
	}
	/**
	 * Fetches the given URL for every added account or a single URL if credentials aren't required.
	 *
	 * @param string $url Url to fetch.
	 * @param boolean $require_credentials Whether user needs to be authenticated or not.
	 * @param boolean $http_post Send data as a POST.
	 * @return array
	 */
	public function fetch()
	{
		$mc = EpiCurl::getInstance();

		foreach ( self::$accounts as $account_name => $values )
		{
			foreach ( $values['methods'] as $method_name => $method )
			{
				$curl_handle = curl_init( $method['url'] );
				// Due to headers' lios: http://www.shoemoney.com/2008/12/29/lib-curl-twitter-api-expect-100-continue-pain-and-how-to-fix-it/
				curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Expect:'));
				curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

				// x seconds for timeout
				curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5);

				if ( $method['require_credentials'] ) {
					curl_setopt( $curl_handle, CURLOPT_USERPWD, $values['credentials'] );
				}
				if ( $method['http_post'] ) {
					curl_setopt( $curl_handle, CURLOPT_POST, true );
				}

				self::$accounts[$account_name][$method_name]['curl'] = $mc->addCurl( $curl_handle );
			}
		}

		foreach ( self::$accounts as $account_name => $values )
		{
			foreach ( $values['methods'] as $method_name => $value )
			{
				self::$accounts[$account_name][$method_name] = self::$accounts[$account_name][$method_name]['curl']->data; // ->code can be returned as well.
			}
			// Finished with all this account methods, unset:
			unset( self::$accounts[$account_name]['methods'] );
			unset( self::$accounts[$account_name]['credentials'] );
			unset( self::$accounts[$account_name]['curl'] );
		}

		if ( empty( self::$accounts[self::NO_ACCOUNT_NAME] ) )
		{
			unset( self::$accounts[self::NO_ACCOUNT_NAME] );
		}

		return self::$accounts;
	}

	public function processResponse( $response )
	{
		if ( self::RETURN_FORMAT == 'json' )
		{
			$response = json_decode( $response, true );
			if ( isset( $response['error'] ) )
			{
				return $response;
			}
		}
		else
		{
			$response = simplexml_load_string( $response );
			if ( isset( $response->error ) )
			{
				return array( 'error' => $response->error );
			}
		}
	}


	// TODO: Add the following methods:
	/*

	Adding this methods the API will be complete.

	update_delivery_device
	Sets which device Twitter delivers updates to for the authenticating user.  Sending none as the device parameter will disable IM or SMS updates.
	URL: http://twitter.com/account/update_delivery_device.format
	Method(s): POST
	Parameters:
	* device.  Required.  Must be one of: sms, im, none.  Ex: http://twitter.com/account/update_delivery_device.xml?device=im

	update_profile_colors
	Sets one or more hex values that control the color scheme of the authenticating user's profile page on twitter.com.  These values are also returned in the /users/show API method.
	URL: http://twitter.com/account/update_profile_colors.format
	Method(s): POST
	Parameters: one or more of the following parameters must be present.  Each parameter's value must be a valid hexidecimal value, and may be either three or six characters (ex: #fff or #ffffff).
	* profile_background_color.  Optional.
	* profile_text_color.  Optional.
	* profile_link_color.  Optional.
	* profile_sidebar_fill_color.  Optional.
	* profile_sidebar_border_color.  Optional.

	update_profile_image
	Updates the authenticating user's profile image.  Expects raw multipart data, not a URL to an image.
	URL: http://twitter.com/account/update_profile_image.format
	Method(s): POST
	Parameters:
	* image.  Required.  Must be a valid GIF, JPG, or PNG image of less than 700 kilobytes in size.  Images with width larger than 500 pixels will be scaled down.

	update_profile_background_image
	Updates the authenticating user's profile background image.  Expects raw multipart data, not a URL to an image.
	URL: http://twitter.com/account/update_profile_background_image.format
	Method(s): POST
	Parameters:
	* image.  Required.  Must be a valid GIF, JPG, or PNG image of less than 800 kilobytes in size.  Images with width larger than 2048 pixels will be scaled down.

	rate_limit_status
	Returns the remaining number of API requests available to the requesting user before the API limit is reached for the current hour. Calls to rate_limit_status do not count against the rate limit.  If authentication credentials are provided, the rate limit status for the authenticating user is returned.  Otherwise, the rate limit status for the requester's IP address is returned.
	URL: http://twitter.com/account/rate_limit_status.format
	Method(s): GET
	*/
}
?>
