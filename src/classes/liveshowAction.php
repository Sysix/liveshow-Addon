<?php

class liveshowAction {
	
	static public $streamActive;
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
		
		if(is_null(self::$streamActive)) {
			
			$suche = safe_query("SELECT active FROM `".PREFIX."liveshow_settings` WHERE lID='1'");
			$ds=mysql_fetch_array($suche);
			self::$streamActive = $ds['active'];
			
		}
		
		if(self::$streamActive) {
			if($id != 0) {
				if($active == 1 && !isanyadmin($userID)) { 
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