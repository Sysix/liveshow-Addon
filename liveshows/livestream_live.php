<?php

class liveshow_livestream extends liveshow {
	
	public $orgWidth = 560;
	public $orgHeight = 340;
	public $apiUrl = 'http://x%sx.api.channel.livestream.com/2.0/livestatus.json';
	public $name = 'livestream.com';
	public $type = 'json';
	
	public function __construct($id) {
		// http://www.livestream.com/userguide/index.php?title=Channel_API#Forming_Your_Request
		$this->id = strp_replace('_', '-', $id);
	}
	
	public function getEmbedCode() {
		$size = $this->getPlayerSize();
		return '<iframe width="'.$size['width'].'" height="'.$size['height'].'" src="http://cdn.livestream.com/embed/'.$this->id.'?layout=4&amp;height='.$size['height'].'&amp;width='.$size['width'].'&amp;autoplay=false" style="border:0;outline:0" frameborder="0" scrolling="no"></iframe>';	
	}
	
	public function isLive() {
		
		return (bool)$this->config['channel']['isLive'];
		
	}
	
	public function getViews() {
		
		return (int)$this->config['channel']['currentViewerCount'];
		
	}
	
}
		
?>
