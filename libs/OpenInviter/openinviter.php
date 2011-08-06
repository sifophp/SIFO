<?php
/**
 * The core of the OpenInviter system
 * 
 * Contains methods and properties used by all
 * the OpenInivter plugins
 * 
 * @author OpenInviter
 * @version 1.7.6
 */
class openinviter
	{
	public $pluginTypes=array('email'=>'Email Providers','social'=>'Social Networks');
	private $version='1.9.2';
	private $configStructure=array(
		'username'=>array('required'=>true,'default'=>''),
		'private_key'=>array('required'=>true,'default'=>''),
		'message_body'=>array('required'=>false,'default'=>''),
		'message_subject'=>array('required'=>false,'default'=>''),
		'plugins_cache_time'=>array('required'=>false,'default'=>1800),
		'plugins_cache_file'=>array('required'=>true,'default'=>'oi_plugins.php'),
		'cookie_path'=>array('required'=>true,'default'=>'/tmp'),
		'local_debug'=>array('required'=>false,'default'=>false),
		'remote_debug'=>array('required'=>false,'default'=>false),
		'hosted'=>array('required'=>false,'default'=>false),
		'proxies'=>array('required'=>false,'default'=>array()),
		'stats'=>array('required'=>false,'default'=>false),
		'stats_user'=>array('required'=>false,'default'=>''),
		'stats_password'=>array('required'=>false,'default'=>''),
		'update_files'=>array('required'=>false,'default'=>TRUE),
	);
	private $statsDB=false;
	private $configOK;
	private $basePath='';
	private $availablePlugins=array();
	private $currentPlugin=array();
	
	public function __construct()
		{
		$this->basePath=dirname(__FILE__);
		$openinviter_settings = Config::getInstance()->getConfig( 'openinviter' );
		require_once($this->basePath."/plugins/_base.php");
		$this->settings=$openinviter_settings;
		$this->configOK=$this->checkConfig();
		}
	
	private function checkConfig()
		{
		$to_add=array();$ok=true;
		foreach ($this->configStructure as $option=>$details)
			{
			if (!isset($this->settings[$option])) $to_add[$option]=$details['default'];
			if ($ok) if ($details['required'] AND empty($this->settings[$option])) { $this->internalError="`{$option}` is not defined in config.php";$ok=false; }
			}
		if (!empty($to_add))
			{
			$file_path=$this->basePath."/config.php";
			foreach ($to_add as $option=>$value) $this->settings[$option]=$value;
			if (is_writable($file_path))
				{
				$file_contents="<?php\n";
				$file_contents.="\$openinviter_settings=array(\n".$this->arrayToText($this->settings)."\n);\n";
				$file_contents.="?>";
				file_put_contents($file_path,$file_contents);
				}
			}
		return $ok;
		}
	
	private function arrayToText($array)
		{
		$text='';
		$flag=false;
		$i=0;
		foreach ($array as $key=>$val)
			{
			if($flag) $text.=",\n";
			$flag=true;
			$text.="'{$key}'=>";
			if (is_array($val)) $text.='array('.$this->arrayToText($val).')';
			elseif (is_bool($val)) $text.=($val?'true':'false');
			else $text.="\"{$val}\"";
			}
		return($text);
		}
	
	private function statsCheck()
		{
		if (!$this->settings['stats']) return true;
		$db_file=$this->basePath.'/openinviter_stats.sqlite';
		if (!file_exists($db_file))
			{
			if (!is_writable($this->basePath)) { $this->internalError="Unable to write stats. ".$this->basePath." is not writable";return false; }
			if (!$this->statsOpenDB()) { $this->internalError="Unable to create the stats database.";return false; }
			$this->statsQuery("CREATE TABLE oi_imports (id INTEGER PRIMARY KEY, service VARCHAR(16), contacts INTEGER, insert_dt DATETIME, insert_ip VARCHAR(15))");
			$this->statsQuery("CREATE TABLE oi_messages (id INTEGER PRIMARY KEY, service VARCHAR(16), type CHAR(1), messages INTEGER, insert_dt DATETIME, insert_ip VARCHAR(15))");
			}
		elseif (!is_readable($db_file)) { $this->internalError="Unable to open stats database. {$db_file} is not readable.";return false; }
		elseif (!is_writable($db_file)) { $this->internalError="Unable to write stats. {$db_file} is not writable";return false; }
		elseif (!$this->statsOpenDB()) { $this->internalError="Unable to open the stats database.";return false; }
		return true;
		}
	
	private function statsOpenDB()
		{
		if (!$this->settings['stats']) return true;
		if (function_exists('sqlite_open')) 
			if ($this->statsDB=sqlite_open($this->basePath.'/openinviter_stats.sqlite',0666)) return true;
		return false;
		}
	
	private function statsRecordImport($contacts)
		{
		if (!$this->settings['stats']) return true;
		if (!$this->statsDB) if (!$this->statsOpenDB()) return false;
		$this->statsQuery("INSERT INTO oi_imports (service,contacts,insert_dt,insert_ip) VALUES ('{$this->plugin->service}','{$contacts}','".date("Y-m-d H:i:s")."','{$_SERVER['REMOTE_ADDR']}')");
		}
	
	private function statsRecordMessages($msg_type,$messages)
		{
		if (!$this->settings['stats']) return true;
		if (!$this->statsDB) if (!$this->statsOpenDB()) return false;
		$this->statsQuery("INSERT INTO oi_messages (service,type,messages,insert_dt,insert_ip) VALUES ('{$this->plugin->service}','{$msg_type}','{$messages}','".date("Y-m-d H:i:s")."','{$_SERVER['REMOTE_ADDR']}')");
		}
	
	public function statsQuery($query)
		{
		if (!$this->settings['stats']) return false;
		if (!$this->statsDB)
			{
			if (!$this->statsCheck()) return false;
			if (!$this->statsOpenDB()) return false;
			}
		return sqlite_query($this->statsDB,$query,SQLITE_ASSOC);		
		}
	
	/**
	 * Start internal plugin
	 * 
	 * Starts the internal plugin and
	 * transfers the settings to it.
	 * 
	 * @param string $plugin_name The name of the plugin being started
	 */	  
	public function startPlugin($plugin_name,$getPlugins=false)
		{
		if (!$getPlugins) $this->currentPlugin=$this->availablePlugins[$plugin_name];
		if (file_exists($this->basePath."/postinstall.php")) { $this->internalError="You have to delete postinstall.php before using OpenInviter";return false; }
		elseif (!$this->configOK) return false;
		elseif (!$this->statsCheck()) return false;
		elseif ($this->settings['hosted'])
			{
			if (!file_exists($this->basePath."/plugins/_hosted.plg.php")) $this->internalError="Invalid service provider";
			else
				{
				if (!class_exists('_hosted')) require_once($this->basePath."/plugins/_hosted.plg.php");
				if ($getPlugins)
					{
					$this->servicesLink=new _hosted($plugin_name);
					$this->servicesLink->settings=$this->settings;
					$this->servicesLink->base_version=$this->version;
					$this->servicesLink->base_path=$this->basePath;
					}
				else
					{
					$this->plugin=new _hosted($plugin_name);
					$this->plugin->settings=$this->settings;
					$this->plugin->base_version=$this->version;
	    			$this->plugin->base_path=$this->basePath;
	    			$this->plugin->hostedServices=$this->getPlugins();
					}
				}
			}
		elseif (file_exists($this->basePath."/plugins/{$plugin_name}.plg.php"))
			{
			$ok=true;
			if (!class_exists($plugin_name)) require_once($this->basePath."/plugins/{$plugin_name}.plg.php");
			$this->plugin=new $plugin_name();
    		$this->plugin->settings=$this->settings;
    		$this->plugin->base_version=$this->version;
    		$this->plugin->base_path=$this->basePath;
    		$this->currentPlugin=$this->availablePlugins[$plugin_name];
			if (file_exists($this->basePath."/conf/{$plugin_name}.conf")) 
				{
				include($this->basePath."/conf/{$plugin_name}.conf");
				if (empty($enable)) $this->internalError="Invalid service provider";
				if (!empty($messageDelay)) $this->plugin->messageDelay=$messageDelay; else  $this->plugin->messageDelay=1;
				if (!empty($maxMessages)) $this->plugin->maxMessages=$maxMessages; else $this->plugin->maxMessages=10;
				}
			}
		else { $this->internalError="Invalid service provider";return false; }
		return true;
		}
	
	/**
	 * Stop the internal plugin
	 * 
	 * Acts as a wrapper function for the stopPlugin
	 * function in the OpenInviter_Base class
	 */
	public function stopPlugin($graceful=false)
		{
		$this->plugin->stopPlugin($graceful);
		}

	/**
	 * Login function
	 * 
	 * Acts as a wrapper function for the plugin's
	 * login function.
	 * 
	 * @param string $user The username being logged in
	 * @param string $pass The password for the username being logged in
	 * @return mixed FALSE if the login credentials don't match the plugin's requirements or the result of the plugin's login function.
	 */
	public function login($user,$pass)
		{
		if (!$this->checkLoginCredentials($user)) return false;
		return $this->plugin->login($user,$pass);
		}
	
	/**
	 * Get the current user's contacts
	 * 
	 * Acts as a wrapper function for the plugin's
	 * getMyContacts function.
	 * 
	 * @return mixed The result of the plugin's getMyContacts function.
	 */
	public function getMyContacts()
		{
		$contacts=$this->plugin->getMyContacts();
		if ($contacts!==false) $this->statsRecordImport(count($contacts));
		return $contacts;
		}	

	/**
	 * End the current user's session
	 * 
	 * Acts as a wrapper function for the plugin's
	 * logout function
	 * 
	 * @return bool The result of the plugin's logout function.
	 */
	public function logout()
		{
		return $this->plugin->logout();	
		}

	public function writePlConf($name_file,$type)
		{
		if (!file_exists($this->basePath."/conf")) mkdir($this->basePath."/conf",0755,true);
		if ($type=='social')  file_put_contents($this->basePath."/conf/{$name_file}.conf",'<?php $enable=true;$autoUpdate=true;$messageDelay=1;$maxMessages=10;?>');	
		elseif($type=='email') file_put_contents($this->basePath."/conf/{$name_file}.conf",'<?php $enable=true;$autoUpdate=true; ?>');
		elseif($type=='hosted') file_put_contents($this->basePath."/conf/{$name_file}.conf",'<?php $enable=false;$autoUpdate=true; ?>');		
		}

	/**
	 * Get the installed plugins
	 * 
	 * Returns information about the available plugins
	 * 
	 * @return mixed An array of the plugins available or FALSE if there are no plugins available.
	 */
	public function getPlugins($update=false,$required_details=false)
		{
		$plugins=array();
		if ($required_details) 
			{
			$valid_rcache=false;$cache_rpath=$this->settings['cookie_path'].'/'."int_{$required_details}.php";
			if (file_exists($cache_rpath))
				{
				include($cache_rpath);
				$cache_rts=filemtime($cache_rpath);
				if (time()-$cache_rts<=$this->settings['plugins_cache_time']) $valid_rcache=true;
				}
			if ($valid_rcache) return $returnPlugins;
			}
		$cache_path=$this->settings['cookie_path'].'/'.$this->settings['plugins_cache_file'];$valid_cache=false;
		$cache_ts=0;
		if (!$update)
			if (file_exists($cache_path))
				{
				include($cache_path);
				$cache_ts=filemtime($cache_path);
				if (time()-$cache_ts<=$this->settings['plugins_cache_time']) $valid_cache=true;
				}
		if (!$valid_cache)
			{
			$array_file=array();
			$temp=glob($this->basePath."/plugins/*.plg.php");
	        foreach ($temp as $file) $array_file[basename($file,'.plg.php')]=$file;
	        if (!$update)
	        	{
		        if ($this->settings['hosted'])
		        	{
					if ($this->startPlugin('_hosted',true)!==FALSE) { $plugins=array();$plugins['hosted']=$this->servicesLink->getHostedServices(); }
		        	else return array();
		        	}
	        	if (isset($array_file['_hosted'])) unset($array_file['_hosted']);
	        	}	
	         if ($update==TRUE OR $this->settings['hosted']==FALSE)
	        	{
	        	$reWriteAll=false;
				if (count($array_file)>0) 
					{			
					ksort($array_file);$modified_files=array();
					if (!empty($plugins['hosted'])) { $reWriteAll=true;$plugins=array(); }
					else
						foreach ($plugins as $key=>$vals)
							{
							foreach ($vals as $key2=>$val2)
								if (!isset($array_file[$key2])) unset($vals[$key2]);
							if (empty($vals)) unset($plugins[$key]);
							else $plugins[$key]=$vals;
							}
					foreach ($array_file as $plugin_key=>$file) 
						if (filemtime($file)>$cache_ts OR $reWriteAll) 
							$modified_files[$plugin_key]=$file;
					foreach($modified_files as $plugin_key=>$file)
						if (file_exists($this->basePath."/conf/{$plugin_key}.conf"))
							{
							include_once($this->basePath."/conf/{$plugin_key}.conf");
							if ($enable AND $update==false)
								{ include($file); if ($this->checkVersion($_pluginInfo['base_version'])) $plugins[$_pluginInfo['type']][$plugin_key]=$_pluginInfo; }
							elseif ($update==true)
								{ include($file); if ($this->checkVersion($_pluginInfo['base_version'])) $plugins[$_pluginInfo['type']][$plugin_key]=array_merge(array('autoupdate'=>$autoUpdate),$_pluginInfo); }
							}
						else
							{  include($file);if ($this->checkVersion($_pluginInfo['base_version'])) $plugins[$_pluginInfo['type']][$plugin_key]=$_pluginInfo; $this->writePlConf($plugin_key,$_pluginInfo['type']);}
					}
				foreach ($plugins as $key=>$val) if (empty($val)) unset($plugins[$key]);
	        	}
			if (!$update)
				{
				if ((!$valid_cache) AND (empty($modified_files)) AND (!$this->settings['hosted'])) touch($this->settings['cookie_path'].'/'.$this->settings['plugins_cache_file']);
				else
					{
					$cache_contents="<?php\n";
					$cache_contents.="\$plugins=array(\n".$this->arrayToText($plugins)."\n);\n";
					$cache_contents.="?>";
					file_put_contents($cache_path,$cache_contents);
					}
				}
			}
		if (!$this->settings['hosted']) $returnPlugins=$plugins;			
		else $returnPlugins=(!empty($plugins['hosted'])?$plugins['hosted']:array());		
		if ($required_details) 
			{			
			if (!$valid_rcache)
				{					 
				foreach($returnPlugins as $types=>$plugins)
					foreach($plugins as $plugKey=>$plugin)
						if (!empty($plugin['imported_details']))
							{ if (!in_array($required_details,$plugin['imported_details'])) unset($returnPlugins[$types][$plugKey]); }
						else unset($returnPlugins[$types][$plugKey]);
				if (!empty($returnPlugins))
					{
					$cache_contents="<?php\n";
					$cache_contents.="\$returnPlugins=array(\n".$this->arrayToText($returnPlugins)."\n);\n";
					$cache_contents.="?>";
					file_put_contents($cache_rpath,$cache_contents);	
					}		
				}
			return $returnPlugins;
			}
		$temp=array();
		if (!empty($returnPlugins)) 
			foreach ($returnPlugins as $type=>$type_plugins)
				$temp=array_merge($temp,$type_plugins);				
		$this->availablePlugins=$temp;							 																	
		return $returnPlugins;
		}
	
	/**
	 * Send a message
	 * 
	 * Acts as a wrapper for the plugin's
	 * sendMessage function.
	 * 
	 * @param string $session_id The OpenInviter user's session ID
	 * @param string $message The message being sent to the users
	 * @param array $contacts An array of contacts that are going to receive the message
	 * @return mixed -1 if the plugin doesn't have an internal sendMessage function or the result of the plugin's sendMessage function
	 */
	public function sendMessage($session_id,$message,$contacts)
		{
		$this->plugin->init($session_id);
		$internal=$this->getInternalError();
		if ($internal) return false;
		if (!method_exists($this->plugin,'sendMessage')) { $this->statsRecordMessages('E',count($contacts));return -1; }
		else 
			{
			$sent=$this->plugin->sendMessage($session_id,$message,$contacts);
			if ($sent!==false) $this->statsRecordMessages('I',count($contacts));
			return $sent;
			}
		}
	
	/**
	 * Find out if the contacts should be displayed
	 * 
	 * Tells whether the current plugin will display
	 * a list of contacts or not
	 * 
	 * @return bool TRUE if the plugin displays the list of contacts, FALSE otherwise.
	 */
	public function showContacts()
		{
		return $this->plugin->showContacts;
		}
	
	/**
	 * Check version requirements
	 * 
	 * Checks if the current version of OpenInviter
	 * is greater than the plugin's required version
	 * 
	 * @param string $required_version The OpenInviter version that the plugin requires.
	 * @return bool TRUE if the version if equal or greater, FALSE otherwise.
	 */
	public function checkVersion($required_version)
		{
		if (version_compare($required_version,$this->version,'<=')) return true;
		return false;
		}
	
	/**
	 * Find out the version of OpenInviter
	 * 
	 * Find out the version of the OpenInviter
	 * base class
	 * 
	 * @return string The version of the OpenInviter base class.
	 */
	public function getVersion()
		{
		return $this->version;
		}
	
	/**
	 * Check the provided login credentials
	 * 
	 * Checks whether the provided login credentials
	 * match the plugin's required structure and (if required)
	 * if the provided domain name is allowed for the
	 * current plugin.
	 * 
	 * @param string $user The provided user name.
	 * @return bool TRUE if the login credentials match the required structure, FALSE otherwise. 
	 */
	private function checkLoginCredentials($user)
		{
		$is_email=$this->plugin->isEmail($user);
		if ($this->currentPlugin['requirement'])
			{
			if ($this->currentPlugin['requirement']=='email' AND !$is_email)
				{
				$this->internalError="Please enter the full email, not just the username";
				return false;
				}
			elseif ($this->currentPlugin['requirement']=='user' AND $is_email)
				{
				$this->internalError="Please enter just the username, not the full email";
				return false;
				}
			}
		if ($this->currentPlugin['allowed_domains'] AND $is_email)
			{
			$temp=explode('@',$user);$user_domain=$temp[1];$temp=false;
			foreach ($this->currentPlugin['allowed_domains'] as $domain)
				if (preg_match($domain,$user_domain)) { $temp=true;break; }
			if (!$temp)
				{
				$this->internalError="<b>{$user_domain}</b> is not a valid domain for this provider";
				return false;
				}
			}
		return true;
		}
	
	public function getPluginByDomain($user)
		{
		$user_domain=explode('@',$user);if (!isset($user_domain[1])) return false;
		$user_domain=$user_domain[1];
		foreach ($this->availablePlugins as $plugin=>$details)
			{
			$patterns=array();
			if ($details['allowed_domains']) $patterns=$details['allowed_domains']; elseif (isset($details['detected_domains'])) $patterns=$details['detected_domains'];
			foreach ($patterns as $domain_pattern)
				if (preg_match($domain_pattern,$user_domain)) return $plugin;
			}
		return false;
		}
	
	/**
	 * Gets the OpenInviter's internal error
	 * 
	 * Gets the OpenInviter's base class or the plugin's
	 * internal error message
	 * 
	 * @return mixed The error message or FALSE if there is no error.s
	 */
	public function getInternalError()
		{
		if (isset($this->internalError)) return $this->internalError;
		if (isset($this->plugin->internalError)) return $this->plugin->internalError;
		return false;
		}
	
	/**
	 * Get the current OpenInviter session ID
	 * 
	 * Acts as a wrapper function for the plugin's
	 * getSessionID function.
	 * 
	 * @return mixed The result of the plugin's getSessionID function.
	 */
	public function getSessionID()
		{
		return $this->plugin->getSessionID();
		}
	
	}
?>