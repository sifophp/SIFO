<?php
/*
 * OpenInviter Hosted Solution Client
 */
$_pluginInfo=array(
	'name'=>'OpenInviter - Hosted',
	'version'=>'1.0.0',
	'description'=>"Import contacts using the OpenInviter hosted solution",
	'base_version'=>'1.7.5',
	'type'=>'hosted',
	'check_url'=>'http://openinviter.com'
	);
/**
 * OpenInviter Hosted Solution
 * 
 * Imports user's contacts using OpenInviter's Hosted Solution
 * 
 * @author OpenInviter
 * @version 1.0.0
 */
class _hosted extends openinviter_base
	{
	private $login_ok=false;
	public $showContacts=true;
	public $internalError=false;
	public $requirement=false;
	public $allowed_domains=false;
	public $service='_hosted';
	public $timeout=60;
	
	public function __construct($service='_hosted')
		{
		$this->service=$service;
		}
	
	public function getHostedServices()
		{
		$path=$this->settings['cookie_path'].'/oi_hosted_services.txt';$services_cache=false;
		if (file_exists($path)) if (time()-filemtime($path)<=7200) $services_cache=true;
		if (!$services_cache)
			{
			if (!$this->init()) return array();
			$headers=array('X_USER'=>$this->settings['username'],'X_SIGNATURE'=>md5(md5($this->settings['private_key']).$this->settings['username']));
		    $res=$this->post("http://hosted.openinviter.com/hosted/services.php",array(),false,false,false,$headers);
			if (empty($res)) { $this->internalError="Unable to connect to server.";return array(); }
			if (strpos($res,"ERROR: ")===0) { $this->internalError=substr($res,7);return array(); }
			file_put_contents($path,$res);
			}
		$plugins['email']=unserialize(file_get_contents($path));
		return $plugins;
		}
	
	/**
	 * Login function
	 * 
	 * Requests the OpenInviter Server to import the contacts for a certain service and user.
	 * Parses the response and stores the contacts in the designated variable.
	 * 
	 * @param string $user The current user.
	 * @param string $pass The password for the current user.
	 * @return bool TRUE if the current user was authenticated successfully, FALSE otherwise.
	 */
	public function login($user,$pass)
		{
		if (!isset($this->hostedServices['email'][$this->service])) { $this->internalError="Unknown service.";return false; }
		$this->resetDebugger();
		$this->service_user=$user;
		$this->service_password=$pass;
		if (!$this->init()) return false;
		$xml="<import><service>".$this->service."</service><user>{$user}</user><password>{$pass}</password></import>";
		$headers=array('Content-Type'=>'application/xml','X_USER'=>$this->settings['username'],'X_SIGNATURE'=>md5(md5($this->settings['private_key']).$xml));
	    $res=$this->post("http://hosted.openinviter.com/hosted/hosted.php",gzcompress($xml,9),false,false,false,$headers,true);
	    if (empty($res)) { $this->internalError="Unable to connect to server.";return false; }
	    $res=gzuncompress($res);
	    if (!$res) { $this->internalError="Unable to import contacts. Please try again later.";return false; }
	    libxml_use_internal_errors(true);
	    $parsed_res=simplexml_load_string($res);
	    libxml_use_internal_errors(false);
	    if (!$parsed_res) { $this->internalError="Unable to import contacts. Please try again later.";return false; }
	    if ((string)$parsed_res->error!='OK') { $this->internalError=(string)$parsed_res->error;return false; }
	    $this->contacts=array();
	    foreach ($parsed_res->contacts->contact as $contact) $this->contacts[(string)$contact->email]=(string)$contact->name;
	    $this->login_ok=true;
		return true;
		}

	/**
	 * Get the current user's contacts
	 * 
	 * Returns the contacts array that was previously imported.
	 * 
	 * @return mixed The array if contacts if importing was successful, FALSE otherwise.
	 */	
	public function getMyContacts()
		{
		if (!$this->login_ok) { $this->internalError="Unable to import contacts. Please try again later.";return false; }
		return $this->contacts;
		}
	
	/**
	 * Terminate session
	 * 
	 * Terminates the current user's session,
	 * debugs the request and reset's the internal 
	 * debudder.
	 * 
	 * @return bool TRUE if the session was terminated successfully, FALSE otherwise.
	 */	
	public function logout()
		{
		if (!$this->checkSession()) return false;
		$this->debugRequest();
		$this->resetDebugger();
		$this->stopPlugin();
		return true;
		}
	}
?>
