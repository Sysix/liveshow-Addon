<?php

class liveshow_livestream extends liveshow
{

    public $orgWidth = 560;
    public $orgHeight = 340;
    public $apiUrl = 'http://x%sx.api.channel.livestream.com/2.0/livestatus.json';
    public $name = 'livestream.com';
    public $type = 'json';

    public function __construct($id)
    {
        // http://www.livestream.com/userguide/index.php?title=Channel_API#Forming_Your_Request
        $this->id = str_replace('_', '-', $id);
    }

    public function getEmbedCode()
    {
        $size = $this->getPlayerSize();
        return '<iframe width="' . $size['width'] . '" height="' . $size['height'] . '" src="http://cdn.livestream.com/embed/' . $this->id . '?layout=4&amp;height=' . $size['height'] . '&amp;width=' . $size['width'] . '&amp;autoplay=false" style="border:0;outline:0" frameborder="0" scrolling="no"></iframe>';
    }

    public function isLive()
    {
        if (!is_null($this->config)) {
            return (bool)$this->config['channel']['isLive'];
        }

        return false;
    }

    public function getViews()
    {
        if ($this->isLive()) {
            return (int)$this->config['channel']['currentViewerCount'];
        }

        return 0;
    }

    public function getPreviewImage()
    {
        if($this->isLive()) {
            return 'http://thumbnail.api.livestream.com/thumbnail?name=' . $this->id;
        }

        return '';
    }
}

?>
