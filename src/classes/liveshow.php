<?php

abstract class liveshow {
	
	public $orgWidth = 400;
	public $ordHeight = 300;
	public $apiUrl = '';
	public $config = '';
	public $id = '';
	public $name = '';

	public function __construct() {
		
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
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$curl = curl_exec($ch);
		curl_close($ch);
		
	}
	
	/*
	 * Den Embed Code rausbekommen
	 *
	 * @return string
	 */
	public function getEmbedCode() {		
	}
	
	/*
	 * Stream online?
	 *
	 * @return bool
	 */
	public function isLive() {
		
	}
	
	/*
	 * Anzahl der Zuschauer
	 *
	 * @return int
	 */
	public function getViews() {
		
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
                $streams .= '<option value="'.$stream.'">'.$stream_name.'</option>';        
                        
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
						}
                        else {
							return 1; 
						}
                } else {
					return 0;
                }
        }else {
			return $id;
        }
	}
	
}

?>