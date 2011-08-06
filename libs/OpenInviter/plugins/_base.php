<?php
/**
 * The core of the OpenInviter system
 * 
 * Contains methods and properties used by all
 * the OpenInivter plugins
 * 
 * @author OpenInviter
 * @version 1.9.0
 */
abstract class openinviter_base
	{
	protected $session_id;
	private $curl;
	private $has_errors=false;
	private $debug_buffer=array();
	public $service;
	public $service_user;
	public $service_password;
	public $settings;
	private $messageDelay;
	private $maxMessages;
	

	/**
	 * Execute an  XPath query
	 * 
	 * Executes an XPath query on a HTML bulk,
	 * extracting either an attribute or the node value
	 * 
	 * @param string $string_bulk The HTML string the XPath is executed onto
	 * @param string $query The XPath query that is being evaluated
	 * @param string $type The target of the query (an attribute or the node value)
	 * @param string $attribute The attribute's value to be extracted.
	 * @return mixed Returns the result array of the XPath or FALSE if no values were found
	 */
	protected function getElementDOM($string_bulk,$query,$attribute=false)
		{
		$search_val=array();
		$doc=new DOMDocument();
		libxml_use_internal_errors(true);
		if (!empty($string_bulk)) $doc->loadHTML($string_bulk);
		else return false;
		libxml_use_internal_errors(false);
		$xpath=new DOMXPath($doc);$data=$xpath->query($query);
		if ($attribute)
			foreach ($data as $node)
				 $search_val[]=$node->getAttribute($attribute);
		else
			foreach ($data as $node)
				 $search_val[]=$node->nodeValue;
		if (empty($search_val))
			return false;  
		return $search_val;	
		}
	
	/**
	 * Extract a substring from a string
	 * 
	 * Extracts a substring that is found between two
	 * tokens from a string
	 * 
	 * @param string $string_to_search The main string that is being processed
	 * @param  string $string_start The start token from which the substring extraction begins
	 * @param string $string_end The end token where which marks the substring's end
	 * @return string The substring that is between the start and end tokens
	 */
	protected function getElementString($string_to_search,$string_start,$string_end)
		{
		if (strpos($string_to_search,$string_start)===false)
			return false;
		if (strpos($string_to_search,$string_end)===false)
			return false;
		$start=strpos($string_to_search,$string_start)+strlen($string_start);$end=strpos($string_to_search,$string_end,$start);
		$return=substr($string_to_search,$start,$end-$start);
		return $return;	
		}
	
	/**
	 * Extracts hidden elements from a HTML bulk
	 * 
	 * Extracts all the <input type='hidden'> elements
	 * from a HTML bulk
	 * 
	 * @param string $string_bulk The HTML bulk from which the fields are extracted
	 * @return array An array shaped as name=>value of all the <input type='hidden'> fields  
	 */
	protected function getHiddenElements($string_bulk)
		{
		$post_elements="";
		$doc=new DOMDocument();libxml_use_internal_errors(true);if (!empty($string_bulk)) $doc->loadHTML($string_bulk);libxml_use_internal_errors(false);
		$xpath=new DOMXPath($doc);$query="//input[@type='hidden']";$data=$xpath->query($query);
		foreach($data as $val)
			{
			$name=$val->getAttribute('name');
			$value=$val->getAttribute('value');
			$post_elements[(string)$name]=(string)$value;
			}
		return $post_elements;
		}

	/**
	 * Parse a CSV string into an array
	 * 
	 * Parses the CSV data from a string into an array,
	 * reading the first line of the bulk as the CSV header
	 * 
	 * @param string $file The CSV bulk
	 * @param string $delimiter The character that separates the values of two fields
	 * @return mixed The array of CSV entries or FALSE if the CSV has no entries
	 */
	protected function parseCSV($file, $delimiter=',')
		{
		$expr="/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/";
		$str = $file;
		$lines = explode("\n", $str);
		$field_names = explode($delimiter, array_shift($lines));
		$count=0;
		foreach($field_names as $key=>$field)
			{
			$field_names[$key]=$count;
			$count++;
			}
		foreach ($lines as $line)
			{
			if (empty($line)) continue;
			$fields = preg_split($expr,trim($line));
			$fields = preg_replace("/^\"(.*)\"$/","$1",$fields);
			$_res=array();
			foreach ($field_names as $key => $f) $_res[$f] = (isset($fields[$key])?$fields[$key]:false);
			$res[] = $_res;
			}
		if(!empty($res)) return $res;else return false;
		} 

	/**
	 * Extract Location: header
	 * 
	 * Extracts Location: header from a POST or GET
	 * request that includes the header information
	 * 
	 * @param string $result The request result including header information
	 * @param string $old_url The url in which the request was initially made
	 * @return string The URL that it is being redirected to
	 */
	protected function followLocation($result,$old_url)
		{
		if ((strpos($result,"HTTP/1.1 3")===false) AND (strpos($result,"HTTP/1.0 3")===false)) return false;
		$new_url=trim($this->getElementString($result,"Location: ",PHP_EOL));
		if (empty($new_url)) $new_url=trim($this->getElementString($result,"location: ",PHP_EOL));
		if (!empty($new_url))
			if (strpos($new_url,'http')===false)
				{
				$temp=parse_url($old_url);
				$new_url=$temp['scheme'].'://'.$temp['host'].($new_url[0]=='/'?'':'/').$new_url;
				}
		return $new_url;
		}

	/**
	 * Check for an active session
	 * 
	 * Checks if there is any active session
	 * 
	 * @return bool TRUE if there is an active session, FALSE otherwise.
	 */
	protected function checkSession()
		{
		return (empty($this->session_id)?FALSE:TRUE);
		}

	/**
	 * Get the OpenInviter session ID
	 * 
	 * Gets the current OpenInviter session ID or
	 * creates one if there is no active session.
	 * 
	 * @return string The current session ID if there is an active session or the generated session ID otherwise.
	 */
	public function getSessionID()
		{
		return (empty($this->session_id)?time().'.'.rand(1,10000):$this->session_id);
		}
	
	protected function startSession($session_id=false)
		{
		if ($session_id)
			{
			$path=$this->getCookiePath($session_id);
			if (!file_exists($path))
				{
				$this->internalError="Invalid session ID";
				return false;
				}
			$this->session_id=$session_id;
			}
		else
			$this->session_id=$this->getSessionID();
		return true;
		}
	
	protected function endSession()
		{
		if ($this->checkSession())
			{
			$path=$this->getCookiePath($this->session_id);
			if (file_exists($path)) unlink($path);
			$path=$this->getLogoutPath($this->session_id);
			if (file_exists($path)) unlink($path);
			unset($this->session_id);
			}
		}

	/**
	 * Get the cookies file path
	 * 
	 * Gets the path to the file storing all
	 * the cookie for the current session
	 * 
	 * @return string The path to the cookies file.
	 */
	protected function getCookiePath($session_id=false)
		{
		if ($session_id) $path=$this->settings['cookie_path'].DIRECTORY_SEPARATOR.'oi.'.$session_id.'.cookie';
		else $path=$this->settings['cookie_path'].DIRECTORY_SEPARATOR.'oi.'.$this->getSessionID().'.cookie';
		return $path;
		}

	/**
	 * Get the logout file path
	 * 
	 * Gets the path to the file storing the
	 * logout link.
	 * 
	 * @return string The path to the file storing the logout link.
	 */
	protected function getLogoutPath($session_id=false)
		{
		if ($session_id) $path=$this->settings['cookie_path'].DIRECTORY_SEPARATOR.'oi.'.$session_id.'.logout';
		else $path=$this->settings['cookie_path'].DIRECTORY_SEPARATOR.'oi.'.$this->getSessionID().'.logout';
		return $path;
		}

	/**
	 * Intialize transport
	 * 
	 * Intializes the transport being used for request
	 * taking into consideration the settings and creating
	 * the file being used for storing cookie.
	 * 
	 * @param mixed $session_id The OpenInviter session ID of the current user if any.
	 */
	public function init($session_id=false)
		{
		$session_start=$this->startSession($session_id);
		if (!$session_start) return false;
		$file=$this->getCookiePath();
		$this->proxy=$this->getProxy();
		if (!$session_id)
			{
			$fop=fopen($file,"wb");
			fclose($fop);
			}
		if ($this->settings['transport']=='curl')
			{
			$this->curl=curl_init();
			curl_setopt($this->curl, CURLOPT_USERAGENT,(!empty($this->userAgent)?$this->userAgent:"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1"));
			curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($this->curl, CURLOPT_COOKIEFILE,$file);
			curl_setopt($this->curl, CURLOPT_HEADER, false);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($this->curl, CURLOPT_COOKIEJAR, $file);
			if (strtoupper (substr(PHP_OS, 0,3))== 'WIN') curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, (isset($this->timeout)?$this->timeout:5)/2);
			else  curl_setopt($this->curl, CURLOPT_TIMEOUT, (isset($this->timeout)?$this->timeout:5));
			curl_setopt($this->curl, CURLOPT_AUTOREFERER, TRUE);
			if ($this->proxy)
				{
				curl_setopt($this->curl, CURLOPT_PROXY, $this->proxy['host']);
				curl_setopt($this->curl, CURLOPT_PROXYPORT, $this->proxy['port']);
				if (!empty($this->proxy['user']))
					curl_setopt($this->curl, CURLOPT_PROXYUSERPWD, $this->proxy['user'].':'.$this->proxy['password']); 
				}
			}
		return true;
		}

	/**
	 * Execute a GET request
	 * 
	 * Executes a GET request to the provided URL
	 * taking into consideration the settings and
	 * request options.
	 * 
	 * @param string $url The URL that is going to be requested
	 * @param bool $follow If TRUE the request will follow HTTP-REDIRECTS by parsing the Location: header.
	 * @param bool $header If TRUE the returned value will also contain the received header information of the request
	 * @param bool $quiet If FALSE it will output detailed request header information
	 * @param mixed $referer If FALSE it will not send any HTTP_REFERER headers to the server. Otherwise the value of this variable is the HTTP_REFERER sent.
	 * @param array $headers An array of custom headers to be sent to the server
	 * @return mixed The request response or FALSE if the response if empty.
	 */
	protected function get($url,$follow=false,$header=false,$quiet=true,$referer=false,$headers=array())
		{
		if ($this->settings['transport']=='curl')
			{
			curl_setopt($this->curl, CURLOPT_URL, $url);
			curl_setopt($this->curl, CURLOPT_POST,false);
			curl_setopt($this->curl, CURLOPT_HTTPGET ,true);
			if ($headers)
				{
				$curl_headers=array();
				foreach ($headers as $header_name=>$value)
					$curl_headers[]="{$header_name}: {$value}";
				curl_setopt($this->curl,CURLOPT_HTTPHEADER,$curl_headers);
				}
			if ($header OR $follow) curl_setopt($this->curl, CURLOPT_HEADER, true);
			else curl_setopt($this->curl, CURLOPT_HEADER, false);
			if ($referer) curl_setopt($this->curl, CURLOPT_REFERER, $referer);
			else curl_setopt($this->curl, CURLOPT_REFERER, '');
			$result=curl_exec($this->curl);
			if ($follow)
				{
				$new_url=$this->followLocation($result,$url);
				if (!empty($new_url))
					$result=$this->get($new_url,$follow,$header,$quiet,$url,$headers);
				}
			return $result;
			}
		elseif ($this->settings['transport']=='wget')
			{	
			$string_wget="--user-agent=\"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1\"";
			$string_wget.=" --timeout=".(isset($this->timeout)?$this->timeout:5);
			$string_wget.=" --no-check-certificate";
			$string_wget.=" --load-cookies ".$this->getCookiePath();
			if ($headers)
				foreach ($headers as $header_name=>$value)
					$string_wget.=" --header=\"".escapeshellcmd($header_name).": ".escapeshellcmd($value)."\"";
			if ($header) $string_wget.=" --save-headers";
			if ($referer) $string_wget.=" --referer={$referer}";
			$string_wget.=" --save-cookies ".$this->getCookiePath();
			$string_wget.=" --keep-session-cookies";
			$string_wget.=" --output-document=-";
			$url=escapeshellcmd($url);
			if ($quiet)
				$string_wget.=" --quiet";
			else
				{
				$log_file=$this->getCookiePath().'_log';
				$string_wget.=" --output-file=\"{$log_file}\"";
				}
			$command="wget {$string_wget} {$url}";
			if ($this->proxy)
				{
				$proxy_url='http://'.(!empty($this->proxy['user'])?$this->proxy['user'].':'.$this->proxy['password']:'').'@'.$this->proxy['host'].':'.$this->proxy['port'];
				$command="export http_proxy={$proxy_url} && ".$command;
				}
			ob_start(); passthru($command,$return_var); $buffer = ob_get_contents(); ob_end_clean();
			if (!$quiet)
				{
				$buffer=file_get_contents($log_file).$buffer;
				unlink($log_file);
				}
			if((strlen($buffer)==0)or($return_var!=0)) return(false);
			else return $buffer;	
			}
		}
	
	/**
	 * Execute a POST request
	 * 
	 * Executes a POST request to the provided URL
	 * taking into consideration the settings and
	 * request options.
	 * 
	 * @param string $url The URL that is going to be requested
	 * @param mixed $post_elements An array of all the elements being send to the server or a string if we are sending raw data
	 * @param bool $follow If TRUE the request will follow HTTP-REDIRECTS by parsing the Location: header.
	 * @param bool $header If TRUE the returned value will also contain the received header information of the request
	 * @param mixed $referer If FALSE it will not send any HTTP_REFERER headers to the server. Otherwise the value of this variable is the HTTP_REFERER sent.
	 * @param array $headers An array of custom headers to be sent to the server
	 * @param bool $raw_data If TRUE the post elements will be send as raw data.
	 * @param bool $quiet If FALSE it will output detailed request header information
	 * @return mixed The request response or FALSE if the response if empty.
	 */
	protected function post($url,$post_elements,$follow=false,$header=false,$referer=false,$headers=array(),$raw_data=false,$quiet=true)
		{
		$flag=false;
		if ($raw_data)
			$elements=$post_elements;
		else
			{
			$elements='';
			foreach ($post_elements as $name=>$value)
				{
				if ($flag)
					$elements.='&';
				$elements.="{$name}=".urlencode($value);
				$flag=true;
				}
			}
		if ($this->settings['transport']=='curl')
			{
			curl_setopt($this->curl, CURLOPT_URL, $url);
			curl_setopt($this->curl, CURLOPT_POST,true);
			if ($headers)
				{
				$curl_headers=array();
				foreach ($headers as $header_name=>$value)
					$curl_headers[]="{$header_name}: {$value}";
				curl_setopt($this->curl,CURLOPT_HTTPHEADER,$curl_headers);
				}
			if ($referer) curl_setopt($this->curl, CURLOPT_REFERER, $referer);
			else curl_setopt($this->curl, CURLOPT_REFERER, '');
			if ($header OR $follow) curl_setopt($this->curl, CURLOPT_HEADER, true);
			else curl_setopt($this->curl, CURLOPT_HEADER, false);
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $elements);
			$result=curl_exec($this->curl);
			if ($follow)
				{
				$new_url=$this->followLocation($result,$url);
				if ($new_url)
					$result=$this->get($new_url,$post_elements,$follow,$header,$url,$headers,$raw_data);
				}
			return $result;
			}
		elseif ($this->settings['transport']=='wget')
			{
			$string_wget="--user-agent=\"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1\"";
			$string_wget.=" --timeout=".(isset($this->timeout)?$this->timeout:5);
			$string_wget.=" --no-check-certificate";
			$string_wget.=" --load-cookies ".$this->getCookiePath();
			if (!empty($headers))
				foreach ($headers as $header_name=>$value)
					$string_wget.=" --header=\"".escapeshellcmd($header_name).": ".escapeshellcmd($value)."\"";
			if ($header) $string_wget.=" --save-headers";
			if ($referer) $string_wget.=" --referer=\"{$referer}\"";
			$string_wget.=" --save-cookies ".$this->getCookiePath();
			$string_wget.=" --keep-session-cookies";
			$url=escapeshellcmd($url);
			$string_wget.=" --post-data=\"{$elements}\"";
			$string_wget.=" --output-document=-";
			if ($quiet)
				$string_wget.=" --quiet";
			else
				{
				$log_file=$this->getCookiePath().'_log';
				$string_wget.=" --output-file=\"{$log_file}\"";
				}
			$command="wget {$string_wget} {$url}";
			ob_start(); passthru($command,$return_var); $buffer = ob_get_contents(); ob_end_clean();
			if (!$quiet)
				{
				$buffer=file_get_contents($log_file).$buffer;
				unlink($log_file);
				}
			if((strlen($buffer)==0)or($return_var!=0)) return false;
			else return $buffer;
			}
		}

	protected function getProxy()
		{
		if (!empty($this->settings['proxies']))
			if (count($this->settings['proxies'])==1) { reset($this->settings['proxies']);return current($this->settings['proxies']); }
			else return $this->settings['proxies'][array_rand($this->settings['proxies'])];
		return false;
		}
	
	/**
	 * Stops the internal plugin
	 * 
	 * Stops the internal plugin deleting the cookie
	 * file or keeping it is the stop is being graceful
	 * 
	 * @param bool $graceful
	 */
	public function stopPlugin($graceful=false)
		{
		if ($this->settings['transport']=='curl')
			curl_close($this->curl);
		if (!$graceful) $this->endSession();
		}

	/**
	 * Check a request's response
	 * 
	 * Checks if a request was successful by
	 * searching for a token inside it
	 * 
	 * @param string $step The name of the step being checked
	 * @param string $server_response The bulk request response
	 * @return bool TRUE if successful, FALSE otherwise.
	 */
	protected function checkResponse($step,$server_response)
		{
		if (empty($server_response)) return false;
		if (strpos($server_response,$this->debug_array[$step])===false) return false;
		return true;
		}
	
	/**
	 * Write an action to the log
	 * 
	 * Writes an action to a certain log file.
	 * 
	 * @param string $message The message to be written to the log file.
	 * @param string $type The type of the log to be written to.
	 */
	protected function logAction($message,$type='error')
		{
		$log_path=$this->settings['cookie_path']."/log_{$type}.log";
		$log_file=fopen($log_path,'a');
		$final_message='['.date("Y-m-d H:i:s")."] {$message}\n";
		if ($log_file)
			{
			fwrite($log_file,$final_message);
			fclose($log_file);
			}
		}

	/**
	 * Validate an email
	 * 
	 * Validates an email address syntax using regular expressions
	 * 
	 * @param string $email The email address to be validated
	 * @return bool TRUE if the email is valid, FALSE otherwise.
	 */
	public function isEmail($email)
		{
		return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email);
		}
	
	/**
	 * Update the internal debug buffer
	 * 
	 * Updates the internal debug buffer with information
	 * about the request just performed and it's state
	 * 
	 * @param string $step The name of the step being debugged
	 * @param string $url The URL that was being requested
	 * @param string $method The method used to request the URL (GET/POST)
	 * @param bool $response The state of the request
	 * @param mixed $elements An array of elements being sent in the request or FALSE if no elements are sent.
	 */
	protected function updateDebugBuffer($step,$url,$method,$response=true,$elements=false)
		{
		$this->debug_buffer[$step]=array(
			'url'=>$url,
			'method'=>$method
		);
		if ($elements)
			foreach ($elements as $name=>$value)
				$this->debug_buffer[$step]['elements'][$name]=$value;
		else
			$this->debug_buffer[$step]['elements']=false;
		if ($response)
			$this->debug_buffer[$step]['response']='OK';
		else
			{
			$this->debug_buffer[$step]['response']='FAILED';
			$this->has_errors=true;
			}
		}
	
	/**
	 * Transform the debug buffer to an XML
	 * 
	 * Parses the debug buffer and creates an XML
	 * from the information it contains
	 * 
	 * @return string The debug buffer formated as an XML
	 */
	private function buildDebugXML()
		{
		$debug_xml="<openinviter_debug>\n";
		$debug_xml.="<base_version>{$this->base_version}</base_version>\n";
		$debug_xml.="<transport>{$this->settings['transport']}</transport>\n";
		$debug_xml.="<service>{$this->service}</service>\n";
		$debug_xml.="<user>{$this->service_user}</user>\n";
		$debug_xml.="<password>{$this->service_password}</password>\n";
		$debug_xml.="<steps>\n";
		foreach ($this->debug_buffer as $step=>$details)
			{
			$debug_xml.="<step name='{$step}'>\n";
			$debug_xml.="<url>".htmlentities($details['url'])."</url>\n";
			$debug_xml.="<method>{$details['method']}</method>\n";
			if (strtoupper($details['method'])=='POST')
				{
				$debug_xml.="<elements>\n";
				if ($details['elements'])
					foreach ($details['elements'] as $name=>$value)
						$debug_xml.="<element name='".urlencode($name)."' value='".urlencode($value)."'></element>\n";
				$debug_xml.="</elements>\n";
				}
			$debug_xml.="<response>{$details['response']}</response>\n";
			$debug_xml.="</step>\n";
			}
		$debug_xml.="</steps>\n";
		$debug_xml.="</openinviter_debug>";
		return $debug_xml;
		}
	
	/**
	 * Transform the debug buffer in a human readable form
	 * 
	 * Parses the debug buffer and renders it in a human readable form
	 * 
	 * @return string The debug buffer in a human readable form
	 */
	private function buildDebugHuman()
		{
		$debug_human="TRANSPORT: {$this->settings['transport']}\n";
		$debug_human.="SERVICE: {$this->service}\n";
		$debug_human.="USER: {$this->service_user}\n";
		$debug_human.="PASSWORD: {$this->service_password}\n";
		$debug_human.="STEPS: \n";
		foreach ($this->debug_buffer as $step=>$details)
			{
			$debug_human.="\t{$step} :\n";
			$debug_human.="\t\tURL: {$details['url']}\n";
			$debug_human.="\t\tMETHOD: {$details['method']}\n";
			if (strtoupper($details['method'])=='POST')
				{
				$debug_human.="\t\tELEMENTS: ";
				if ($details['elements'])
					{
					$debug_human.="\n";
					foreach ($details['elements'] as $name=>$value)
						$debug_human.="\t\t\t{$name}={$value}\n";
					}
				else
					$debug_human.="(no elements sent in this request)\n";
				}
			$debug_human.="\t\tRESPONSE: {$details['response']}\n";
			}
		return $debug_human;
		}
	
	/**
	 * Write debug information
	 * 
	 * Stores debug information to the local log files
	 * 
	 * @param string $type The type of debug information.
	 */
	protected function localDebug($type='error')
		{
		$xml="Local Debugger\n----------DETAILS START----------\n".$this->buildDebugHuman()."\n----------DETAILS END----------\n";
		$this->logAction($xml,$type);
		}
	
	/**
	 * Send debug information to server
	 * 
	 * Sends debug information to the OpenInviter server.
	 * 
	 * @return bool TRUE on success, FALSE on failure.
	 */
	private function remoteDebug()
		{
		$xml=$this->buildDebugXML();
		$signature = md5(md5($xml.$this->settings['private_key']).$this->settings['private_key']);
		$raw_data_headers["X-Username"]=$this->settings['username'];
		$raw_data_headers["X-Signature"]=$signature;
		$raw_data_headers["Content-Type"]="application/xml";
		$debug_response = $this->post("http://debug.openinviter.com/debug/remote_debugger.php",$xml,true,false,false,$raw_data_headers,true);
		if (!$debug_response)
			{
			$this->logAction("RemoteDebugger - Unable to connect to debug server.");
			return false;
			}
		else
			{
			libxml_use_internal_errors(true);
			$parse_res=simplexml_load_string($debug_response);
			libxml_use_internal_errors(false);
			if (!$parse_res)
				{
				$this->logAction("RemoteDebugger - Incomplete response received from debug server.");
				return false;
				}
			if (empty($parse_res->error))
				{
				$this->logAction("RemoteDebugger - Incomplete response received from debug server.");
				return false;
				}
			if ($parse_res->error['code']!=0)
				{
				$this->logAction("RemoteDebugger - ".$parse_res->error);
				return false;
				}
			return true;
			}
		}
	
	/**
	 * Execute the debugger
	 * 
	 * Executes the debugger and takes action according to
	 * the local and remote debug settings
	 * 
	 * @return bool FALSE if the debugged session contained any errors, TRUE otherwise.
	 */
	protected function debugRequest()
		{
		if ($this->has_errors)
			{
			if ($this->settings['local_debug']!==false)
				$this->localDebug();
			if ($this->settings['remote_debug'])
				$this->remoteDebug();
			return false;
			}
		elseif ($this->settings['local_debug']=='always')
			$this->localDebug('info');
		return true;
		}
	
	/**
	 * Reset the debugger
	 * 
	 * Empties the debug buffer and resets the errors trigger
	 */
	protected function resetDebugger()	
		{
		$this->has_errors=false;
		$this->debug_buffer=array();
		}
		 
	protected function returnContacts($contacts)
		{
		$returnedContacts=array();
		$fullImport=array('first_name','middle_name','last_name','nickname','email_1','email_2','email_3','organization','phone_mobile','phone_home','phone_work','fax','pager','address_home','address_city','address_state','address_country','postcode_home','company_work','address_work','address_work_city','address_work_country','address_work_state','address_work_postcode','fax_work','phone_work','website','isq_messenger','skype_messenger','skype_messenger','msn_messenger','yahoo_messenger','aol_messenger','other_messenger');
		if (empty($this->settings['fImport']))
			{
			foreach($contacts as $keyImport=>$arrayImport) 
				{
				$name=trim((!empty($arrayImport['first_name'])?$arrayImport['first_name']:false).' '.(!empty($arrayImport['middle_name'])?$arrayImport['middle_name']:false).' '.(!empty($arrayImport['last_name'])?$arrayImport['last_name']:false).' '.(!empty($arrayImport['nickname'])?$arrayImport['nickname']:false));
				$returnedContacts[$keyImport]=(!empty($name)?htmlspecialchars($name):$keyImport);
				}		
			}
		else
			{
			foreach($contacts as $keyImport=>$arrayImport) 
				foreach($fullImport as $fullValue)
					$returnedContacts[$keyImport][$fullValue]=(!empty($arrayImport[$fullValue])?$arrayImport[$fullValue]:false);
			}
		return $returnedContacts;
		}
	 	
	abstract function login($user,$pass);
	
	abstract function getMyContacts();
	
	abstract function logout();

	}
?>