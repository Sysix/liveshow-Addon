<?php

class liveshowAction {
	
	static public $active;
	static public $width;
	static public $access;
	
	
	/*
	 * Alle Einträge von der DB in die Klassen-Var's speichern
	 *
	 */
	static protected function setSettings() {
		
		$search = safe_query("SELECT * FROM `".PREFIX."liveshow_settings` WHERE lID=1");
		$ds = mysql_fetch_array($search);
		
		self::$width = $ds['width'];
		self::$active = $ds['active'];
		self::$access = $ds['access'];
		
	}
	
	/*
	 * Die gespeicherte Breite von der DB
	 *
	 * @return int
	 */
	static public function getStreamWidh() {
		
		if(is_null(self::$width)) {
			self::setSettings();	
		}
		
		return self::$width;
		
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
	
	/*
	 * Zugriffsberechtigung überprüfen
	 *
	 * @return bool
	 */
	static public function getStreamAccess() {
		global $loggedin;
		global $userID;
		
		if(is_null(self::$access)) {
			self::setSettings();	
		}
		
		switch(self::$access) {
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
	
	/*
	 * Alle Streams als Option rausbekommen
	 *
	 * @param	string	$id
	 * @return	int
	 */
	static public function getStreamActive($id) {
		global $userID;
		
		if(is_null(self::$active)) {
			self::setSettings();			
		}
		
		if(self::$active) {
			if($id != 0) {
				if(self::$active == 1 && !isanyadmin($userID)) {
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
	
	/*
	 * OOP-Klassennamen bekommen
	 *
	 * @param	string	$file	Der Dateiname
	 * @return	string
	 *
	 */
	static public function getClassName($file, $load = true) {
		
		$class = explode('_live', $file);
		if($class[1] != '.php')
			return '';

		if($load) {
			require_once 'liveshows/' . $file;
		}
					
		return 'liveshow_'.$class[0];
		
	}
	
}
?>