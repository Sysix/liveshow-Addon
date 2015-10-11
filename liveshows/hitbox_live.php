<?php

class hitbox_livestream extends liveshow
{

    public $orgWidth = 640;
    public $orgHeight = 360;
    public $apiUrl = 'https://api.hitbox.tv/media/live/%s';
    public $name = 'hitbox.tv';
    public $type = 'json';

    public function getEmbedCode()
    {
        $size = $this->getPlayerSize();
        return '<iframe width="' . $size['height'] . '" height="360" src="http://www.hitbox.tv/embed/' . $this->id . '" frameborder="0" allowfullscreen></iframe>';
    }

    public function isLive()
    {
        if(!is_null($this->config)) {
            if(isset($this->config['livestream']) && is_array($this->config['livestream'])) {
                return (bool)$this->config['livestream'][0]['media_is_live'];
            }
        }

        return false;
    }

    public function getViews()
    {
        if ($this->isLive()) {
            return (int) $this->config['livestream'][0]['media_views'];
        }

        return 0;
    }

    public function getPreviewImage()
    {
        if($this->isLive()) {
            return 'http://edge.sf.hitbox.tv' . $this->config['livestream'][0]['media_thumbnail_large'];
        }

        return '';
    }
}

?>
