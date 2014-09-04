<?php
set_time_limit(0);
if (!is_writable(dirname(__FILE__))) { echo "<b>OpenInviter</b> folder is not writable. Updates will not be posible<br>";exit; }
if (!is_writable(dirname(__FILE__).'/plugins')) { echo "<b>OpenInviter/plugins</b> folder is not writable. Updates will not be posible<br>";exit; }
if (file_exists(dirname(__FILE__)."/postinstall.php")) { echo "Delete <b>postinstall.php</b> before running the autoupdater";exit; }
include(dirname(__FILE__).'/openinviter.php');
$inviter=new openinviter();
class update extends openinviter_base
	{
	public $plugins;
	
	public $settings;
	
	protected $timeout=30;
	
	public function makeUpdate()
		{
		$xml=$this->checkVersions();
		if (!empty($xml))
			{
			$update_files=$this->parseXmlUpdates($xml);
			$update=true;$newFiles=array();
			foreach($update_files as $name_file=>$arrayfile)
				if ($arrayfile['type']=='new')
					{
					if(!empty($this->settings['update_files']))
						{ 
						if (isset($this->plugins[$arrayfile['plugin_type']][$name_file]))
							{
							if (!empty($this->plugins[$arrayfile['plugin_type']][$name_file]['autoupdate']))  $newFiles[$name_file]=array('sum'=>$arrayfile['sum'],'plugin_type'=>$arrayfile['plugin_type']); 
							elseif($arrayfile['plugin_type']=='base') $newFiles[$name_file]=array('sum'=>$arrayfile['sum'],'plugin_type'=>$arrayfile['plugin_type']);
							}
						}
					else
						{
						if($arrayfile['plugin_type']=='base') $newFiles[$name_file]=array('sum'=>$arrayfile['sum'],'plugin_type'=>$arrayfile['plugin_type']);
						else $newFiles[$name_file]=array('sum'=>$arrayfile['sum'],'plugin_type'=>$arrayfile['plugin_type']); 	
						}
					}
		
			foreach ($newFiles as $name_file=>$arrayFile)
				{
				$headers=array('Content-Type'=>'application/xml','X_USER'=>$this->settings['username'],'X_SIGNATURE'=>$this->makeSignature($this->settings['private_key'],$this->xmlFile($name_file)));					
				$res=$this->getNewFile(gzcompress($this->xmlFile($name_file),9),$headers);
				if (!empty($res))
					{
					$fileDeCmp=gzuncompress($res);$elementsDownload=$this->getElementsDownload($fileDeCmp);
					$file_content=$elementsDownload['fileStrip'];$signatureBulk=$elementsDownload['signatureBulk'];
					$this->verifySignature($signatureBulk,$file_content);
					if ($arrayFile['sum']!=md5($file_content)) $update=false;
					elseif (!file_put_contents($this->getUpdateFilePath($name_file).".tmp",$file_content)) $this->ers("Unable to write new updates");
					}
				else $update=false;
				}
			if ($update)
				{
				foreach($newFiles as $name_file=>$arrayfile)
					{		
					file_put_contents($this->getUpdateFilePath($name_file),file_get_contents($this->getUpdateFilePath($name_file).".tmp"));
					unlink($this->getUpdateFilePath($name_file).".tmp");	
					$this->writeConf($name_file,$arrayfile['plugin_type']);
					}
				$this->array2Log($update_files);
				}
			else
				{
				foreach($newFiles as $name_file=>$arrayfile) if (file_exists($this->getUpdateFilePath($name_file).".tmp")) unlink($this->getUpdateFilePath($name_file).".tmp");																
				if (!$update) $this->ers("Unable to download updates");
				}
			}
		else $this->ers("Unable to connect to Server");
		}
		
	public function writeConf($name_file,$type=false)
		{
		if (!file_exists(dirname(__FILE__)."/conf")) mkdir(dirname(__FILE__)."/conf",0755,true);
		if ($type=='social') { if (!file_exists(dirname(__FILE__)."/conf/{$name_file}.conf")) file_put_contents(dirname(__FILE__)."/conf/{$name_file}.conf",'<?php $enable=true;$autoUpdate=true;$messageDelay=1;$maxMessages=10;?>'); }	
		elseif($type=='email') { if (!file_exists(dirname(__FILE__)."/conf/{$name_file}.conf")) file_put_contents(dirname(__FILE__)."/conf/{$name_file}.conf",'<?php $enable=true;$autoUpdate=true; ?>'); }
		}
		
	private function verifySignature($signatureBulk,$fileContent)
		{
		if (strpos($signatureBulk,'X_SIGNATURE:')===false) $this->ers("INVALID SIGNATURE");
		else
			{
			$start=strpos($signatureBulk,'X_SIGNATURE:')+strlen('X_SIGNATURE:');$end=strlen($signatureBulk);
			$signature=trim(substr($signatureBulk,$start,$end-$start));
			$signature_check=$this->makeSignature($this->settings['private_key'],$fileContent);
			if($signature!=$signature_check) $this->ers("Invalid SIGNATURE");
			else return true;
			}
		}
	
	public function getElementsDownload($res)
		{
		$start=0;$end=strpos($res,"<?");$signatureBulk=trim(substr($res,$start,$end));$fileStriped=str_replace($signatureBulk,'',$res);
		if (empty($signatureBulk)) $this->ers("Invalid Signature");
		if (empty($fileStriped)) $this->ers("Unable to download");
		return array('signatureBulk'=>$signatureBulk,'fileStrip'=>$fileStriped);
		}
	
	protected function getUpdateFilePath($plugin)
		{
		if ($plugin=='openinviter') return dirname(__FILE__)."/{$plugin}.php";
		elseif($plugin=='_base') return dirname(__FILE__)."/plugins/{$plugin}.php";
		else return dirname(__FILE__)."/plugins/{$plugin}.plg.php";
		} 	
	
	public function xmlFile($file_name)
		{
		return "<request><api_version>1.8</api_version><file>{$file_name}</file></request>";
		}
	
	public function xmlVersions()
		{
		$xml="<request><api_version>1.8</api_version><services>";
		if (!empty($this->plugins))
			foreach ($this->plugins as $type=>$dummy)
				foreach ($dummy as $plugin=>$details)
					$xml.="<service name='{$details['name']}'>
								<version>{$details['version']}</version>
							</service>";	
		else $xml.="<service name=''>
								<version></version>
							</service>
				   ";
		return $xml.="</services></request>";
		}
	
	public function checkVersions()
		{
		$this->init();
		$xml=$this->xmlVersions();
		$headers=array('Content-Type'=>'application/xml','X_USER'=>$this->settings['username'],'X_SIGNATURE'=>$this->makeSignature($this->settings['private_key'],$xml));
		$res=gzuncompress($this->post("http://openinviter.com/service_download.php",gzcompress($xml,9),false,false,false,$headers,true));
		$this->stopPlugin();
		if ((empty($res))) $this->ers("Unable to Connect to Server");	
		elseif (strpos($res,'<error>')!==false) {$res=str_replace("<error>","",str_replace("</error>","",$res));$this->ers($res);}
		return $res;
		}
		
	public function parseXmlUpdates($xml)
		{
		$versions=array();
		$doc=new DOMDocument();libxml_use_internal_errors(true);if (!empty($xml)) $doc->loadHTML($xml);libxml_use_internal_errors(false);
		$xpath=new DOMXPath($doc);$query="//service";$data=$xpath->query($query);
		foreach($data as $node) $versions[(string)$node->childNodes->item(0)->nodeValue]=array('type'=>(string)$node->childNodes->item(2)->nodeValue,'sum'=>(string)$node->childNodes->item(1)->nodeValue,'plugin_type'=>(string)$node->childNodes->item(3)->nodeValue);
		return $versions;	
		}	
	
	public function getNewFile($xml,$headers)
		{
		$this->init();
		$res=$this->post("http://openinviter.com/service_download.php",$xml,false,false,false,$headers,true);
		$this->stopPlugin();
		return $res;
		}
	
	private function makeSignature($var1,$var2)
		{
		return md5(md5($var1).md5($var2));
		}
		
	private function array2Log($array)
		{
		$date=date("Y-m-d H:i:s");$updateCount=0;
		$string="[$date] UPDATE STARTED\r\n";
		foreach($array as $key=>$values) if ($values['type']=='new') { $string.="\tUPDATED: {$key}.php\r\n";$updateCount++; }
		$string.="\tUPDATE DONE. {$updateCount} FILES UPDATED\r\n";
		$this->writeLog($string);
		}
	
	public function ers($contents)
		{
		$string="[".date("Y-m-d H:i:s")."] ERROR ".$contents."\r\n";
		$this->writeLog($string);
		echo $string; 
		exit;
		}
	
	public function writeLog($contents)
		{
		$fp=fopen($this->settings['cookie_path'].'/oi_update_log.txt','a+');if ($fp) { fwrite($fp,$contents);fclose($fp); }
		}
	
	public function login($user,$pass)
		{
		return;
		}
		
	public function getMyContacts()
		{
		return;
		}
		
	public function logout()
		{
		return;
		}
		
	}
$plugins=$inviter->getPlugins(true);
$files_base['base']=array('openinviter'=>array('name'=>'openinviter','version'=>$inviter->getVersion()),'_base'=>array('name'=>'_base','version'=>$inviter->getVersion()));
$update=new update();
$update->settings=$inviter->settings;
$update->plugins=(!empty($plugins)?array_merge($files_base,$plugins):$files_base);
$update->service_user='updater';
$update->service_pass='updater';
$update->service='updater';
$update->makeUpdate();
?>