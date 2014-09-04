<?php
/*
 * Created on Sep 3, 2008
 *
 * Owner: DORU
 */
class notifications_response
	{	
	private $response="";
	
	private $ersArray=array(
		'method'=>array('code'=>'100','desc'=>'Invalid Method','fatal'=>false),
		'headers'=>array('code'=>'101','desc'=>'Incomplete headers','fatal'=>true),
		'post'=>array('code'=>'102','desc'=>'Invalid POST content','fatal'=>false),
		'auth'=>array('code'=>'103','desc'=>'Bad authentification data','fatal'=>true),
		'xml'=>array('code'=>'105','desc'=>'Invalid XML structure','fatal'=>true),
		'api_version'=>array('code'=>'106', 'desc'=>'Invalid Api version please upgrade manually','fatal'=>true),
	);

	private $privateKey;
	private $username; 
		
	private function getAuth()
		{
		include(dirname(__FILE__).'/config.php');
		$this->privateKey=$openinviter_settings['private_key'];
		$this->username=$openinviter_settings['username'];
		global $HTTP_RAW_POST_DATA;
		if ($_SERVER['REQUEST_METHOD']!='POST') $this->error('method');
		if (!isset($_SERVER['HTTP_X_USER'])) $this->error('headers');
		elseif (!isset($_SERVER['HTTP_X_SIGNATURE'])) $this->error('headers');
		if (empty($HTTP_RAW_POST_DATA)) $this->error('post');
		$this->user=htmlentities($_SERVER['HTTP_X_USER'],ENT_QUOTES);
		$xml=trim(gzuncompress($HTTP_RAW_POST_DATA));
		$signature=$_SERVER['HTTP_X_SIGNATURE'];
		if ($this->username!=$this->user) $this->error('auth');
		$signature_check=$this->makeSignature($this->privateKey,$xml); 
		if ($signature_check!=$signature) $this->error('auth');		
		if ($xml=='<notification>CHECK STATUS</notification>') $this->requestTypes='check';
		elseif ($xml=='<notification>UPDATE</notification>') $this->requestTypes='update';
		else $this->error['xml'];	
		return true;
		}
	
	public function response()
		{
		if ($this->getAuth()) 
			{
			if ($this->requestTypes=='update') { include('autoupdate.php'); return gzcompress("<response>NOTIFICATIONS OK</response>",9); }
			elseif($this->requestTypes=='check') return gzcompress("<response>WAITING FOR UPDATES</response>",9);
			} 
		else return false;
		}
		
	private function error($errorID,$header="HTTP/1.0 400 Bad Request")
		{
		$error=$this->ersArray[$errorID];
		header($header);echo (gzcompress("<error>{$error['desc']}</error>",9));exit;
		}

	private function makeSignature($var1,$var2)
		{
		return md5(md5($var1).md5($var2));
		}

	}
$notifications=new notifications_response();
echo $notifications->response();
?>