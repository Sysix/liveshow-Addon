<?php

abstract class liveshow
{

    public $orgWidth = 0;
    public $orgHeight = 0;
    public $apiUrl = '';
    public $config;
    public $id = '';
    public $name = '';
    public $type = 'json';

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Die Breite und Höhe des Players passend zum Content bekommen
     *
     * @return array
     */
    public function getPlayerSize()
    {

        $width = liveshowAction::getStreamWidh();
        $height = ($width * 100) / $this->orgWidth;
        $height = ($height * $this->orgHeight) / 100;

        if (!$height || !$width) {
            $width = $this->orgWidth;
            $height = $this->orgHeight;
        }

        return array('width' => $width, 'height' => $height);

    }

    /**
     * Die Wichtigen Informationen rauslesen und $config speichern
     */
    public function getApi()
    {

        $ch = curl_init(sprintf($this->apiUrl, $this->id));

        $port = (strpos($this->apiUrl, 'https') === 0) ? 443 : 80;

        curl_setopt($ch, CURLOPT_PORT, $port);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla (Statuscheck-Script)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl = curl_exec($ch);

        if (!curl_errno($ch)) {

            $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);


            if ($info == 200) {

                if ($this->type == 'json') {
                    $this->config = json_decode($curl, true);
                } else {
                    $this->config = simplexml_load_string($curl);
                }
            }
        }

        curl_close($ch);
    }

    /**
     * Den Embed Code rausbekommen
     *
     * @return string
     */
    abstract public function getEmbedCode();

    /**
     * Stream online?
     *
     * @return bool
     */
    abstract public function isLive();

    /**
     * Anzahl der Zuschauer
     *
     * @return int
     */
    abstract public function getViews();

    /**
     * @return mixed
     */
    abstract public function getPreviewImage();

    /**
     * Rückgabe des Streamnamen
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


}

?>