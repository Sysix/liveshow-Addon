<?php

include_once('src/classes/liveshow.php');

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action == 'save') {
	$_language->read_module('liveshow');
	if(!$loggedin) die($_language->module['no_access']); #
	$title = $_POST['title'];
	$id = $_POST['id'];
	$type = $_POST['type'];
	$active = $_POST['active'];
	if($active == 'on') { $active = 1; }
	else { $active == 0; }
	$active = liveshow::getStreamActive($active);
	if(!isanyadmin($userID)) { $active = 2; }
	safe_query("INSERT INTO ".PREFIX."liveshow (title, id, type, active, userID) values ('".$title."', '".$id."', '".$type."', '".$active."', '".$userID."')");
	echo '<meta http-equiv="refresh" content="0; url=index.php?site=liveshow">';
	
} elseif($action == 'saveedit') {
	$_language->read_module('liveshow');	
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']); #
	$title = $_POST['title'];
	$id = $_POST['id'];
	$type = $_POST['type'];
	$u_id = $_POST['userID'];
	$livID = $_POST['livID'];
	$active = $_POST['active'];
	if($active == 'on') { $active = 1; }
	else { $active == 0; }
	$active = liveshow::getStreamActive($active);
	safe_query("UPDATE ".PREFIX."liveshow SET title='".$title."', id='".$id."', type='".$type."', active='".$active."', userID='".$u_id."' WHERE livID='".$livID."' ");
	echo '<meta http-equiv="refresh" content="0; url=index.php?site=liveshow">';
	
} elseif($action == 'saveactive') {
	$_language->read_module('liveshow');
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']); #
	$active_pst = $_POST['active'];
	
	if(isanyadmin($userID)) { safe_query("UPDATE ".PREFIX."liveshow SET active='0' WHERE active='1' "); }
	else { safe_query("UPDATE ".PREFIX."liveshow SET active='0' WHERE active='1' AND userID='".$userID."' "); }
	
	foreach($active_pst as $key => $active)  {
		$ds=mysql_fetch_array(safe_query("SELECT userID, active FROM ".PREFIX."liveshow WHERE livID='".$active."'"));
		$id = $ds['userID'];
		$actived = $ds['active'];
		if(isanyadmin($userID) OR ($userID == $id AND $actived != '2')) {
			 safe_query("UPDATE ".PREFIX."liveshow SET active='1' WHERE livID='".$active."' ");
		}
	}
	echo '<meta http-equiv="refresh" content="0; url=index.php?site=liveshow">';
} elseif($action == 'new') {
	$_language->read_module('liveshow');
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']); #
	$streams = liveshow::getStreamList();
	$active = '';
	if(isanyadmin($userID)) { $active = '<tr align="left">
        <td bgcolor="'.$bg1.'"><b>'.$_language->module['active'].'?</b></td>
		<td bgcolor="'.$bg1.'"><input type="checkbox" name="active" checked="checked" /></td>
      </tr>'; }
	eval ("\$liveshow_new = \"".gettemplate("liveshow_new")."\";");
	echo $liveshow_new;
	
} elseif($action == 'edit') {
	
	$_language->read_module('liveshow');	
	
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']);
	$sql_abfrage = "AND userID='".$userID."'";
	if(isanyadmin($userID)) { $sql_abfrage = ''; }
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."liveshow WHERE livID='".$_GET['livID']."' ".$sql_abfrage);
	while($ds=mysql_fetch_array($ergebnis)) {
		$title = $ds['title'];
		$streams = liveshow::getStreamList();
		$active_stream = $ds['type'];
		$streams = str_replace('value="'.$active_stream.'"', 'value="'.$active_stream.'" selected="selected"', $streams);
		$id = $ds['id'];
		$u_id = $ds['userID'];
		$livID = $_GET['livID'];
		$checked = '';
		if($ds['active'] == 1) { $checked = 'checked="checked"'; }
		eval ("\$liveshow_edit = \"".gettemplate("liveshow_edit")."\";");
		echo $liveshow_edit;		
	}
	
} elseif($action == 'settings') {
	$_language->read_module('liveshow');
	$title_head = '&raquo; '.$_language->module['settings'];
	eval ("\$title_liveshow = \"".gettemplate("title_liveshow")."\";");
	echo $title_liveshow;
	
	if(!isanyadmin($userID)) die($_language->module['no_access']);
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

} elseif($action == 'settingssave') {
	$_language->read_module('liveshow');
	if(!isanyadmin($userID)) die($_language->module['no_access']);	
		$access = $_POST['access'];
		$active = $_POST['active'];
		$width = $_POST['width'];
		safe_query("UPDATE ".PREFIX."liveshow_settings SET active='".$active."', access='".$access."', `width`='".$width."' WHERE lID='1' ");
echo '<meta http-equiv="refresh" content="0; url=index.php?site=liveshow">';

} elseif($action == 'delete') {
	$_language->read_module('liveshow');
	if(!liveshow::getStreamAccess()) die($_language->module['no_access']);
	$ds=mysql_fetch_array(safe_query("SELECT userID FROM ".PREFIX."liveshow WHERE livID='".$_GET['livID']."'"));
	if(isanyadmin($userID) || $userID == $ds['userID']) {
		safe_query("DELETE FROM ".PREFIX."liveshow WHERE livID='".$_GET['livID']."'");
	}
	echo '<meta http-equiv="refresh" content="0; url=index.php?site=liveshow">';
		
} elseif($action == 'show') {
	
	$_language->read_module('liveshow');
	
	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow\');return document.MM_returnValue;" value="Online Liveshow" /> ';
	
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."liveshow WHERE livID='".$_GET['livID']."'");
	while($ds=mysql_fetch_array($ergebnis)) {
		
		if(($userID == $ds['userID'] AND liveshow::getStreamAccess() AND $ds['active'] !=2) OR isanyadmin($userID)) { 
			echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow&amp;action=edit&amp;livID='.$_GET['livID'].'\');return document.MM_returnValue;" value="'.$_language->module['edit_liveshow'].'" /> 
			<input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'index.php?site=liveshow&amp;action=delete&amp;livID='.$_GET['livID'].'\');" value="'.$_language->module['delete'].'" />';  
		}
		
		$title_head = '&raquo; '.$ds['title'];
		
		eval ("\$title_liveshow = \"".gettemplate("title_liveshow")."\";");
		echo $title_liveshow;
		
		$title = $ds['title'];
		
		$code = get_streaminfo($ds['id'], $ds['type'], 'embed');
		
		eval ("\$liveshow_show = \"".gettemplate("liveshow_show")."\";");
		echo $liveshow_show;	
	}

} else {
	$_language->read_module('liveshow');
	$title_head = '';
		eval ("\$title_liveshow = \"".gettemplate("title_liveshow")."\";");
		echo $title_liveshow;	
	if($action == 'offline') {
	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow\');return document.MM_returnValue;" value="Online Liveshow" /> ';
	} else {
	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow&amp;action=offline\');return document.MM_returnValue;" value="Offline Liveshow" /> ';
	}
	if(liveshow::getStreamAccess()) { echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow&amp;action=new\');return document.MM_returnValue;" value="'.$_language->module['new_liveshow'].'" />'; }
	if(isanyadmin($userID)) { echo ' <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=liveshow&amp;action=settings\');return document.MM_returnValue;" value="'.$_language->module['settings'].'" />'; }
	echo '<br />';
	$sql_abfrage = "WHERE active='1'";
	if(liveshow::getStreamAccess()) { $sql_abfrage =  "WHERE active='1' OR userID='".$userID."'"; } 
	if(isanyadmin($userID)) { $sql_abfrage = ''; }
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."liveshow ".$sql_abfrage);
	eval ("\$liveshow_head = \"".gettemplate("liveshow_head")."\";");
	echo $liveshow_head;
	$n=1;
	$not_visible = array();
	while($ds=mysql_fetch_array($ergebnis)) {
			if($n%2) {
				$bg1=BG_1;
			}
			else {
				$bg1=BG_3;
			}
		$online = get_streaminfo($ds['id'], $ds['type'], 'live');
		$views = get_streaminfo($ds['id'], $ds['type'], 'views');
		if($online) { $live = '<font color="#00FF00">ONLINE</font>'; }
		else { $live =  '<font color="#FF0000">OFFLINE</font>'; }
		$livID = $ds['livID'];
		$checkbox_show = '';
		$checked = '';
		$active_bgcolor = '';
		if($ds['active'] == 2) { $bg1 = '#FF9900'; }
		if($ds['active'] == 1) { $checked = 'checked="checked"'; }
		if(($userID == $ds['userID'] AND liveshow::getStreamAccess() AND $ds['active'] !=2) OR isanyadmin($userID)) { $checkbox_show = '<input type="checkbox" name="active[]" value="'.$livID.'" '.$checked.' />';  }
		$title = '<a href="index.php?site=liveshow&amp;action=show&amp;livID='.$ds['livID'].'">'.$ds['title'].'</a>';
		
		
		if($action != 'offline') {
			if($online) {
				eval ("\$liveshow_content = \"".gettemplate("liveshow_content")."\";");
				echo $liveshow_content;	
			}
			else {
				$n--;
				$not_visible[] = $checkbox_show;
			}
		} else {
			if(!$online) {
				eval ("\$liveshow_content = \"".gettemplate("liveshow_content")."\";");
				echo $liveshow_content;	
			}
			else {
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