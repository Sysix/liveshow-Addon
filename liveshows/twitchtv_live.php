<?php

class liveshow_twitchtv extends liveshow {
	
	public $orgWidth = 620;
	public $ordHeight = 378;
	public $apiUrl = 'https://api.twitch.tv/kraken/streams/%s';
	public $name = 'Twitch.tv';
	public $type = 'json';
	
	public function getEmbedCode() {
		$size = $this->getPlayerSize();
		return '
		<object type="application/x-shockwave-flash" height="'.$size['height'].'" width="'.$size['width'].'" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel='.$this->id.'" bgcolor="#000000">
		<param name="allowFullScreen" value="true" />
		<param name="allowScriptAccess" value="always" />
		<param name="allowNetworking" value="all" />
		<param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" />
		<param name="flashvars" value="hostname=www.twitch.tv&channel='.$this->id.'&auto_play=true&start_volume=25" />
		</object>';	
	}
	
	public function isLive() {
		
		return !is_null($this->config['stream']);
		
	}
	
	public function getViews() {
		
		if($this->isLive()) {
			return (int)$this->config['stream']['viewers'];
		} 
		
		return 0;
	}
	
}
		
?>
