<?php

class liveshow_justintv extends liveshow {
	
	public $orgWidth = 400;
	public $ordHeight = 300;
	public $apiUrl = 'http://api.justin.tv/api/stream/list.xml?channel=%s';
	public $name = 'Justin.tv';
	public $type = 'xml';
	
	public function getEmbedCode() {
		$size = $this->getPlayerSize();
		return '
		<object type="application/x-shockwave-flash" height="'.$size['height'].'" width="'.$size['width'].'" id="live_embed_player_flash" data="http://de.justin.tv/widgets/live_embed_player.swf?channel='.$this->id.'" bgcolor="#000000">
		<param name="allowFullScreen" value="true" />
		<param name="allowScriptAccess" value="always" />
		<param name="allowNetworking" value="all" />
		<param name="movie" value="http://de.justin.tv/widgets/live_embed_player.swf" />
		<param name="flashvars" value="hostname=de.justin.tv&channel='.$this->id.'&auto_play=false&start_volume=25" />
		</object>';	
	}
	
	public function isLive() {
		
		return $this->config->stream->format == 'live';
		
	}
	
	public function getViews() {
		
		return (int)$this->config->stream->channel_count;
		
	}
	
}
		
?>
