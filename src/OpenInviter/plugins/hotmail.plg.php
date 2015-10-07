<?php
$_pluginInfo=array(
	'name'=>'Live/Hotmail',
	'version'=>'1.6.8',
	'description'=>"Get the contacts from a Windows Live/Hotmail account",
	'base_version'=>'1.8.4',
	'type'=>'email',
	'check_url'=>'http://login.live.com/login.srf?id=2',
	'requirement'=>'email',
	'allowed_domains'=>array('/(hotmail)/i','/(live)/i','/(msn)/i','/(chaishop)/i'),
	'imported_details'=>array('first_name','email_1'),
	);
/**
 * Live/Hotmail Plugin
 * 
 * Imports user's contacts from Windows Live's AddressBook
 * 
 * @author OpenInviter
 * @version 1.6.8
 */
class hotmail extends openinviter_base
	{
	private $login_ok=false;
	public $showContacts=true;
	public $internalError=false;
	protected $timeout=30;
	protected $userAgent='Mozilla/4.1 (compatible; MSIE 5.0; Symbian OS; Nokia 3650;424) Opera 6.10  [en]';
		
	public $debug_array=array(
				'initial_get'=>'srf_uPost',
				'login_post'=>'cid',				
				'get_contacts'=>'compose',
				);
	
	/**
	 * Login function
	 * 
	 * Makes all the necessary requests to authenticate
	 * the current user to the server.
	 * 
	 * @param string $user The current user.
	 * @param string $pass The password for the current user.
	 * @return bool TRUE if the current user was authenticated successfully, FALSE otherwise.
	 */
	public function login($user,$pass)
		{
		$this->resetDebugger();
		$this->service='hotmail';
		$this->service_user=$user;
		$this->service_password=$pass;
		if (!$this->init()) return false;
		$url='https://login.live.com/login.srf?wa=wsignin1.0&rpsnv=11&ct=1308560124&rver=6.1.6206.0&wp=MBI&wreply=http:%2F%2Fcid-5305094c4e322785.profile.live.com%2Fcontacts%3Fwa%3Dwsignin1.0%26lc%3D1033&lc=1033&id=73625&pcexp=false&mkt=en-US';		
		$res=$this->get($url,true);		
		if ($this->checkResponse('initial_get',$res)) $this->updateDebugBuffer('initial_get',$url,'GET');
		else{
			$this->updateDebugBuffer('initial_get',$url,'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			return false;	
			}
		$form_action=$this->getElementString($res,"srf_uPost='","'");
		preg_match('#name\=\"PPFT\" id\=\"(.+)\" value\=\"(.+)\"#U',$res,$matches);
		$post_elements=array('PPFT'=>$matches[2],
							 'LoginOptions'=>1,
							 'NewUser'=>1,
							 'MobilePost'=>1,
							 'PPSX'=>'P',
							 'PwdPad'=>'',
							 'type'=>11,
							 'i3'=>25228,
							 'm1'=>1280,
							 'm2'=>1024,
							 'm3'=>0,
							 'i12'=>1,
							 'login'=>$user,
							 'passwd'=>$pass				 
							);
		$res=$this->post($form_action,$post_elements);
		if ($this->checkResponse('login_post',$res)) $this->updateDebugBuffer('login_post',$form_action,'POST',true,$post_elements);
		else{
			$this->updateDebugBuffer('login_post',$form_action,'POST',false,$post_elements);	
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		$this->login_ok='http://mprofile.live.com/';		
		return true;
		}

	/**
	 * Get the current user's contacts
	 * 
	 * Makes all the necesarry requests to import
	 * the current user's contacts
	 * 
	 * @return mixed The array if contacts if importing was successful, FALSE otherwise.
	 */	
	public function getMyContacts()
		{
		if (!$this->login_ok)
			{
			$this->debugRequest();
			$this->stopPlugin();
			return false;
			}
		else $url=$this->login_ok;
		$res=$this->get($url);
		if ($this->checkResponse('get_contacts',$res)) $this->updateDebugBuffer('get_contacts',$url,'GET');
		else{
			$this->updateDebugBuffer('get_contacts',$url,'GET',false);
			$this->debugRequest();
			$this->stopPlugin();
			return false;	
			}
		$page=0;
		$pagesString=$this->getElementString($res,'indexText" class="SecondaryText">(',')');
		if (!empty($pagesString)) $pagesArray=explode(" ",$pagesString);
		if (empty($pagesArray[3])) $pagesArray[3]=0;		
		while($page<=$pagesArray[3])
			{
			preg_match_all("#compose\&amp;to\=(.+)\&amp\;ru\=#U",$res,$emails);
			preg_match_all("#class=\"BoldText\" href\=\"\/contactinfo\.aspx\?contactid\=(.+)\"\>(.+)\<#U",$res,$names);
			if (!empty($emails[1]))
				foreach($emails[1] as $id=>$email)					
					if (!empty($names[2][$id])) $contacts[str_replace('%2540','@',$email)]=array('email_1'=>str_replace('%2540','@',$email),'first_name'=>$names[2][$id]);
			$page++;
			$res=$this->get($url."?pg={$page}");
			if (empty($res)) break;
			}			
		foreach ($contacts as $email=>$name) if (!$this->isEmail($email)) unset($contacts[$email]);
		return $this->returnContacts($contacts);
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
		$res=$this->get("https://mid.live.com/si/logout.aspx",true);
		$this->debugRequest();
		$this->resetDebugger();
		$this->stopPlugin();
		return true;
		}
		
	}
?>