<?php
	include('_mysql.php');
	include('_settings.php');
	
	mysql_query("DROP TABLE IF EXISTS `".PREFIX."liveshow`");
	mysql_query("CREATE TABLE `".PREFIX."liveshow` (
	    `livID` int(11) NOT NULL AUTO_INCREMENT,
		`title` varchar(255) NOT NULL default '',
		`id` varchar(255) NOT NULL default '',
		`type` varchar(255) NOT NULL default '',
		`userID` varchar(255) NOT NULL default '',
		`active` varchar(1) NOT NULL default ''	,
		 PRIMARY KEY  (`livID`)
	) AUTO_INCREMENT=1 ");
	
	mysql_query("DROP TABLE IF EXISTS `".PREFIX."liveshow_settings`");
	mysql_query("CREATE TABLE `".PREFIX."liveshow_settings` (
	    `lID` int(11) NOT NULL AUTO_INCREMENT,
		`active` varchar(255) NOT NULL default '0',
		`access` varchar(255) NOT NULL default '1',
		`width` varchar(255) NOT NULL default '300',
		 PRIMARY KEY  (`lID`)
	) AUTO_INCREMENT=1 ");
	
	mysql_query("INSERT IGNORE INTO `".PREFIX."liveshow_settings` (`lID`, `active`, `access`, `width`) VALUES (1, 0, 1, 300)");
	
	echo 'Erfolgreich';
	
	
?>