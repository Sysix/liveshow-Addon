<?php
		if(!isset($id)) { $id = 0; }
				
		$orginal_width = 560; /* Orginale Breite vom Playereinbindung */
		$orginal_height = 340; /* Orginale Höhe vom Playereinbindung */
		$width = get_streamwidth();
		$height = ($width*100)/$orginal_width;
		$height = ($height*$orginal_height)/100;
		
		if(!$height OR !$width) { $width = $orginal_width; $height = $orginal_height; }
		$stream_name = 'livestream.com'; /*Stream name */
		$file = 'http://x'.$id.'x.api.channel.livestream.com/2.0/livestatus.xml';
        $xml = simplexml_load_file($file)->asXML();
		$xml = str_replace("ls:","",$xml);
		$xml = simplexml_load_string($xml); 
		$live_stream = $xml->isLive;
		$views_stream = $xml->currentViewerCount;
		if($live_stream == 'false') { $live_stream = false; }
		else { $live_stream = true; }
		$embed = '<iframe width="'.$width.'" height="'.$height.'" src="http://cdn.livestream.com/embed/'.$id.'?layout=4&amp;height='.$height.'&amp;width='.$width.'&amp;autoplay=false" style="border:0;outline:0" frameborder="0" scrolling="no"></iframe>';
		
?>
