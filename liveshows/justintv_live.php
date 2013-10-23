<?php
		if(!isset($id)) { $id = 0; }
		
		$orginal_width = 400; /* Orginale Breite vom Playereinbindung */
		$orginal_height = 300; /* Orginale Höhe vom Playereinbindung */
		$width = get_streamwidth();
		$height = ($width*100)/$orginal_width;
		$height = ($height*$orginal_height)/100;
		
		if(!$height OR !$width) { $width = $orginal_width; $height = $orginal_height; }
		$stream_name = 'Justin.tv';  /*Stream name */
		$file = 'http://api.justin.tv/api/stream/list.xml?channel='.$id;
        $xml = simplexml_load_file($file);
		if($xml->stream->format == 'live') $live = true; 
		else $live = false;
		$live_stream = $live;
		$views_stream = $xml->stream->channel_count;
		$embed = '<object type="application/x-shockwave-flash" height="'.$height.'" width="'.$width.'" id="live_embed_player_flash" data="http://de.justin.tv/widgets/live_embed_player.swf?channel='.$id.'" bgcolor="#000000"><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="movie" value="http://de.justin.tv/widgets/live_embed_player.swf" /><param name="flashvars" value="hostname=de.justin.tv&channel='.$id.'&auto_play=false&start_volume=25" /></object>';
		
?>
