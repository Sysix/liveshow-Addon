<?php

include_once('src/classes/liveshow.php');
include_once('src/classes/liveshowAction.php');

$_language->read_module('liveshow');
$ergebnis = safe_query("SELECT * FROM ".PREFIX."liveshow WHERE active='1' ORDER BY livID DESC LIMIT 0,5");
if(mysql_num_rows($ergebnis)){
	echo'<table width="100%" cellspacing="0" cellpadding="2">';
  $n=1;
	while($ds = mysql_fetch_array($ergebnis)) {
		$title = $ds['title'];
		$livID = $ds['livID'];
    
    if($n%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}
		$online = get_streaminfo($ds['id'], $ds['type'], 'live');
		$views = get_streaminfo($ds['id'], $ds['type'], 'views');
		if($online) { $icon = 'online.png'; }
		else { $icon = 'offline.png'; }
    
	
		eval("\$sc_liveshow = \"".gettemplate("sc_liveshow")."\";");
		echo $sc_liveshow;
    $n++;
	}
	echo'</table>';
}	
?>
