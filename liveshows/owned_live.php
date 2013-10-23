<?php
		if(!isset($id)) { $id = 0; }
		
		$orginal_width = 640; /* Orginale Breite vom Playereinbindung */
		$orginal_height = 360; /* Orginale Höhe vom Playereinbindung */
		$width = get_streamwidth();
		$height = ($width*100)/$orginal_width;
		$height = ($height*$orginal_height)/100;
		
		if(!$height OR !$width) { $width = $orginal_width; $height = $orginal_height; }
		$stream_name = 'own3d.tv';  /*Stream name */
		$file = 'http://api.own3d.tv/liveCheck.php?live_id='.$id;
        $xml = simplexml_load_file($file);		
		$live_stream = $xml->liveEvent->isLive;
		$views_stream = $xml->liveEvent->liveViewers;
		if($live_stream == 'false') { $live_stream = false; }
		else { $live_stream = true; }
		
		$embed = '<iframe height="'.$height.'" width="'.$width.'" frameborder="0" src="http://www.own3d.tv/liveembed/'.$id.'"></iframe>';
		
?>
