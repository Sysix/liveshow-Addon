<?php

function get_streamlist() {
	$streams = '';
	$handle=opendir ('liveshows/'); 
	while ($stream = readdir ($handle)) { /*Alle Daten im Ordner werden gelesen */
		if (!is_dir($stream)) {
			include('liveshows/'.$stream);
			$streams .= '<option value="'.$stream.'">'.$stream_name.'</option>';		
		}
	}
	closedir($handle); /* Ordnerauslesung wird geschlossen */
	return $streams;
}

function get_streamwidth() {
	$ds=mysql_fetch_array(safe_query("SELECT width FROM `".PREFIX."liveshow_settings` WHERE lID='1'"));
	return $ds['width'];
}

function get_streaminfo($id, $type, $info) {
	include('liveshows/'.$type);
	if($info == 'live') { $return_back = $live_stream; }
	elseif($info == 'views') { if(!$views_stream) { $views_stream = 0; } $return_back = $views_stream;}
	elseif($info == 'embed') { $return_back = $embed; }
	else { $return_back = false; }
	return $return_back;	
}

function get_streamaccess() {
	global $loggedin;
	global $userID;
	$ds=mysql_fetch_array(safe_query("SELECT access FROM `".PREFIX."liveshow_settings` WHERE lID='1'"));
	$access = $ds['access'];
	if($access == 1) { $return_back = $loggedin; }
	elseif($access == 2) { $return_back = isclanmember($userID); }
	elseif($access == 3) { $return_back = isanyadmin($userID); }
	else { $return_back = $loggedin; }
	return $return_back;
}

function get_streamactive($id) {
	global $userID;
	$ds=mysql_fetch_array(safe_query("SELECT active FROM `".PREFIX."liveshow_settings` WHERE lID='1'"));
	$active = $ds['active'];
	if($active) {
		if($id != 0) {
			if($active == 1 AND !isanyadmin($userID)) { $return_back = 2; }
			else { $return_back = 1; }
		} else {
			$return_back = 0;
		}
	}else {
		$return_back = $id;
	}
	return $return_back;
}

?>