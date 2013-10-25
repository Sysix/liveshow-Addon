<?php

include_once('src/classes/liveshow.php');
$_language->read_module('liveshow');

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Speichern eine neuen Streams
if($action == 'save') {
		
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']);
	
	$active = ($_POST['active'] == 'on') ? 1 : 0;
	$active = liveshow::getStreamActive($active);
	
	if(!isanyadmin($userID)) { 
		$active = 2; 
	}
	
	safe_query("INSERT INTO ".PREFIX."liveshow 
		(title, id, type, active, userID) values 
		('".$_POST['title']."', '".$_POST['id']."', '".$_POST['type']."', '".$active."', '".$userID."')");
	echo '<meta http-equiv="refresh" content="0; url=index.php?site=liveshow">';

// Speichern eine vorhandenen Streams	
} elseif($action == 'saveedit') {
	
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']);
	
	$active = ($_POST['active'] == 'on') ? 1 : 0;
	$active = liveshow::getStreamActive($active);
	
	safe_query("UPDATE ".PREFIX."liveshow SET title='".$_POST['title']."', 
											id='".$_POST['id']."', 
											type='".$_POST['type']."', 
											active='".$active."', 
											userID='".$_POST['userID']."' 
												WHERE livID='".$_POST['livID']."' ");
												
	echo '<meta http-equiv="refresh" content="0; url=index.php?site=liveshow">';

// Stream aktivieren/deaktivieren
} elseif($action == 'saveactive') {
	
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']);
	$active_pst = $_POST['active'];
	
	if(isanyadmin($userID)) { 
		safe_query("UPDATE ".PREFIX."liveshow SET active='0' WHERE active='1' "); 
	} else {
		safe_query("UPDATE ".PREFIX."liveshow SET active='0' WHERE active='1' AND userID='".$userID."' "); 
	}
	
	foreach($active_pst as $key => $active)  {
		
		$ds=mysql_fetch_array(safe_query("SELECT userID, active FROM ".PREFIX."liveshow WHERE livID='".$active."'"));
		$actived = $ds['active'];
		if(isanyadmin($userID) || ($userID == $ds['userID'] && $actived != 2)) {
			 safe_query("UPDATE ".PREFIX."liveshow SET active='1' WHERE livID='".$active."' ");
		}
		
	}
	echo '<meta http-equiv="refresh" content="0; url=index.php?site=liveshow">';
	
// Speicherung der Einstellungen
} elseif($action == 'settingssave') {
	
	if(!isanyadmin($userID)) die($_language->module['no_access']);	
	
		safe_query("UPDATE ".PREFIX."liveshow_settings SET active='".$_POST['active']."', access='".$_POST['access']."', `width`='".$_POST['width']."' WHERE lID='1' ");
		echo '<meta http-equiv="refresh" content="0; url=index.php?site=liveshow">';

// Stream löschen
} elseif($action == 'delete') {
	
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']);
	
	$ds=mysql_fetch_array(safe_query("SELECT userID FROM ".PREFIX."liveshow WHERE livID='".(int)$_GET['livID']."'"));
	if(isanyadmin($userID) || $userID == $ds['userID']) {
		safe_query("DELETE FROM ".PREFIX."liveshow WHERE livID='".(int)$_GET['livID']."'");
	}
	echo '<meta http-equiv="refresh" content="0; url=index.php?site=liveshow">';

// Erstellen eines neuen Streams
} elseif($action == 'new') {
	
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']);
	
	$streams = liveshow::getStreamList();
	$active = '';
	if(isanyadmin($userID)) {
		$active = '<tr align="left">
        <td bgcolor="'.$bg1.'"><b>'.$_language->module['active'].'?</b></td>
		<td bgcolor="'.$bg1.'"><input type="checkbox" name="active" checked="checked" /></td>
      </tr>'; 
	 }
	eval ("\$liveshow_new = \"".gettemplate("liveshow_new")."\";");
	echo $liveshow_new;

// Editieren eines vorhanden Streams
} elseif($action == 'edit') {
		
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']);
	
	$sql_abfrage = (isanyadmin($userID)) ? '' : "AND userID='".$userID."'";
	
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."liveshow WHERE livID='".(int)$_GET['livID']."' ".$sql_abfrage);
	while($ds=mysql_fetch_array($ergebnis)) {
		
		$title = $ds['title'];
		$streams = liveshow::getStreamList();
		$streams = str_replace('value="'.$active_stream.'"', 'value="'.$active_stream.'" selected="selected"', $streams);
		$active_stream = $ds['type'];		
		$id = $ds['id'];
		$u_id = $ds['userID'];
		$livID = $_GET['livID'];		
		$checked = ($ds['active'] == 1) ? 'checked="checked"' : ''; 
		
		eval ("\$liveshow_edit = \"".gettemplate("liveshow_edit")."\";");
		echo $liveshow_edit;
				
	}

// Einstellungen anzeigen
} elseif($action == 'settings') {
	
	if(!isanyadmin($userID)) die($_language->module['no_access']);
	
	$title_head = '&raquo; '.$_language->module['settings'];
	eval ("\$title_liveshow = \"".gettemplate("title_liveshow")."\";");
	echo $title_liveshow;	
	
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."liveshow_settings WHERE lID='1'");
	while($ds=mysql_fetch_array($ergebnis)) {
		
		$active = $ds['active'];
		$access_saved = $ds['access'];
		$width = $ds['width'];
		
		if($active) { 
			$checked_1 = 'checked="checked"'; 
			$checked_0 = ''; 
		} else {
			$checked_1 = ''; 
			$checked_0 = 'checked="checked"'; 
		}
		
		$access = '<option value="1">'.$_language->module['registered'].'</option>
			<option value="2">'.$_language->module['clanmembers'].'</option>
			<option value="3">'.$_language->module['admins'].'</option>';
		$access = str_replace('value="'.$access_saved.'"', 'value="'.$access_saved.'" selected="selected"', $access);
		
		eval ("\$liveshow_settings = \"".gettemplate("liveshow_settings")."\";");
		echo $liveshow_settings;	
	}
// Stream anzeigen
} elseif($action == 'show') {
	
	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow\');return document.MM_returnValue;" value="Online Liveshow" /> ';
	
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."liveshow WHERE livID='".(int)$_GET['livID']."'");
	while($ds=mysql_fetch_array($ergebnis)) {
		
		if(($userID == $ds['userID'] && liveshow::getStreamAccess() && $ds['active'] != 2) || isanyadmin($userID)) { 
			echo '
			<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow&amp;action=edit&amp;livID='.$_GET['livID'].'\');return document.MM_returnValue;" value="'.$_language->module['edit_liveshow'].'" /> 
			<input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'index.php?site=liveshow&amp;action=delete&amp;livID='.$_GET['livID'].'\');" value="'.$_language->module['delete'].'" />';  
		}
		
		$title_head = '&raquo; '.$ds['title'];
		
		eval ("\$title_liveshow = \"".gettemplate("title_liveshow")."\";");
		echo $title_liveshow;
		
		$title = $ds['title'];
		
		$classname = liveshow::getClassName($ds['type']);
		$ob = new $classname($ds['id']);
		$code = $ob->getEmbedCode();
		
		eval ("\$liveshow_show = \"".gettemplate("liveshow_show")."\";");
		echo $liveshow_show;	
	}

// Streamliste anzeigen
} else {
	
	$title_head = '';
	eval ("\$title_liveshow = \"".gettemplate("title_liveshow")."\";");
	echo $title_liveshow;
	
	if($action == 'offline') {
		echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow\');return document.MM_returnValue;" value="Online Liveshow" /> ';
	} else {
		echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow&amp;action=offline\');return document.MM_returnValue;" value="Offline Liveshow" /> ';
	}
	
	if(liveshow::getStreamAccess()) {
		echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow&amp;action=new\');return document.MM_returnValue;" value="'.$_language->module['new_liveshow'].'" />'; 
	}
	if(isanyadmin($userID)) {
		echo ' <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow&amp;action=settings\');return document.MM_returnValue;" value="'.$_language->module['settings'].'" />';
	}
	
	echo '<br />';
	
	if(isanyadmin($userID)) { 
		$sql_abfrage = '';
	}elseif(liveshow::getStreamAccess()) {
		$sql_abfrage =  "WHERE active='1' OR userID='".$userID."'"; 
	}  else {
		$sql_abfrage = "WHERE active='1'";
	}
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."liveshow ".$sql_abfrage);
	
	eval ("\$liveshow_head = \"".gettemplate("liveshow_head")."\";");
	echo $liveshow_head;
	
	$n=1;
	$not_visible = array();
	while($ds=mysql_fetch_array($ergebnis)) {
		
		$bg1 = ($n%2) ? BG_1 : BG_3;
		
		$classname = liveshow::getClassName($ds['type']);
		$ob = new $classname($ds['id']);
		$ob->getApi();
		$online = $ob->isLive();
		$views = $ob->getViews();
		
		if($online) {
			$live = '<font color="#00FF00">ONLINE</font>'; 
		} else {
			$live = '<font color="#FF0000">OFFLINE</font>'; 
		}
		
		$livID = $ds['livID'];
		$checkbox_show = '';
		$checked = '';
		$active_bgcolor = '';
		if($ds['active'] == 2) { $bg1 = '#FF9900'; }
		if($ds['active'] == 1) { $checked = 'checked="checked"'; }
		
		if(($userID == $ds['userID'] && liveshow::getStreamAccess() && $ds['active'] != 2) || isanyadmin($userID)) {
			$checkbox_show = '<input type="checkbox" name="active[]" value="'.$livID.'" '.$checked.' />';  
		}
		$title = '<a href="index.php?site=liveshow&amp;action=show&amp;livID='.$ds['livID'].'">'.$ds['title'].'</a>';
		
		
		if($action != 'offline') {
			
			if($online) {
				
				eval ("\$liveshow_content = \"".gettemplate("liveshow_content")."\";");
				echo $liveshow_content;	
				
			} else {
				
				$n--;
				$not_visible[] = $checkbox_show;
				
			}
			
		} else {
			
			if(!$online) {
				
				eval ("\$liveshow_content = \"".gettemplate("liveshow_content")."\";");
				echo $liveshow_content;	
				
			} else {
				
				$not_visible[] = $checkbox_show;
				$n--;
				
			}
			
		}
		$n++;
	}
	$adminaction = '';
	$vis_inputs = '<div style="display:none;">';
	foreach($not_visible as $not_vis) {
		$vis_inputs .= $not_vis;
	}	
	$vis_inputs .= '</div>';
	$sql_abfrage = "WHERE active='2' AND userID='".$userID."'";
	if(isanyadmin($userID)) { $sql_abfrage = "WHERE active='2'"; }
	$anz=mysql_num_rows(safe_query("SELECT livID FROM `".PREFIX."liveshow` ".$sql_abfrage));
	
	if($anz) {
		$info = '<tr>
			<td colspan="4" bgcolor="'.BG_2.'" align="center" style="padding:4px">'.$_language->module['info'].'</td>
		</tr>'; 
	} else {
		$info = '';	
	}
	if(liveshow::getStreamAccess()) { 
		$adminaction = '<tr>
			<td bgcolor="'.BG_2.'" colspan="5" align="right">
				'.$vis_inputs.'
				<input type="submit" value="'.$_language->module['save'].'" />
			</td>
		</tr>'; 
	}
	eval ("\$liveshow_foot = \"".gettemplate("liveshow_foot")."\";");
	echo $liveshow_foot;	
}

?>