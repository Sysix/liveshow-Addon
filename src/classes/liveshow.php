<?php

abstract class liveshow {
	
	public $orgWidth = 0;
	public $ordHeight = 0;
	public $apiUrl = '';
	public $config = array();
	public $id = '';
	public $name = '';
	public $type = 'json';

	public function __construct($id) {
		$this->id = $id;
	}
	
	/*
	 * Die Breite und Höhe des Players passend zum Content bekommen
	 *
	 * @return array
	 */
	public function getPlayerSize() {
	
		$width = self::getStreamWidh();
        $height = ($width*100)/$orginal_width;
        $height = ($height*$orginal_height)/100;
		
		if(!$height || !$width) { 
			$width = $orginal_width; 
			$height = $orginal_height; 
		}
		
		return array('width'=>$width, 'height'=>$height);
		
	}
	
	/*
	 * Die Wichtigen Informationen rauslesen und $config speichern
	 */
	public function getApi() {
		
		$ch = curl_init(sprintf($this->apiUrl, $this->id));
		curl_setopt($ch, CURLOPT_PORT, 80);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla (Statuscheck-Script)');
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 300);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$curl = curl_exec($ch);
		curl_close($ch);
		
		if($this->type == 'json') {
			$this->config = json_decode($curl, false);
		} else {
			$this->config = simplexml_load_string($curl);
		}
		
	}
	
	/*
	 * Den Embed Code rausbekommen
	 *
	 * @return string
	 */
	public function getEmbedCode() {	
		return;	
	}
	
	/*
	 * Stream online?
	 *
	 * @return bool
	 */
	public function isLive() {
		return;
	}
	
	/*
	 * Anzahl der Zuschauer
	 *
	 * @return int
	 */
	public function getViews() {
		return;
	}
	
	/*
	 * Rückgabe des Streamnamen
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;	
	}
	
	/*
	 * Die gespeicherte Breite von der DB
	 *
	 * @return int
	 */
	static public function getStreamWidh() {
		
		$search = safe_query("SELECT width FROM `".PREFIX."liveshow_settings` WHERE lID=1");
		$ds = mysql_fetch_array($search);
		return $ds['width'];
		
	}
	
	/*
	 * Alle Streams als Option rausbekommen
	 *
	 * @return string
	 */
	static public function getStreamList() {
		
		$streams = '';
        $handle=opendir ('liveshows/');
        while ($stream = readdir ($handle)) {
                        
                if(in_array($stream, array('.', '..')))
                        continue;						
				
				include('liveshows/'.$stream);
				$classname =  self::getClassName($stream);
				
				$obj = new $classname(0);                      
                
                $streams .= '<option value="'.$stream.'">'.$obj->getName().'</option>';        
                        
        }
		
		closedir($handle);
		
		return $streams;
		
	}
	
	
	static public function getStreamAccess() {
		global $loggedin;
		global $userID;
		
		$search = safe_query("SELECT access FROM `".PREFIX."liveshow_settings` WHERE lID='1'");
		$ds = mysql_fetch_array($search);
		
		switch($ds['access']) {
			case 1:
				return $loggedin;
			case 2:
				return isclanmember($userID);
			case 3:
				return isanyadmin($userID);
			default:
				return $loggedin;
				
		}
		
	}
	
	static public function getStreamActive($id) {
		global $userID;
		
        $suche = safe_query("SELECT active FROM `".PREFIX."liveshow_settings` WHERE lID='1'");
		$ds=mysql_fetch_array($suche);
        $active = $ds['active'];
		
		if($active) {
			if($id != 0) {
				if($active == 1 AND !isanyadmin($userID)) { 
					return 2; 
				} else {
					return 1; 
				}
			} else {
				return 0;
			}
		} else {
			return $id;
		}
	}
	
	static public function getClassName($file) {
		
		$class = explode('_live', $stream);
		if($class[1] != '.php')
			continue;
					
		return 'liveshow_'.$class[0];
		
	}
	
}

?>