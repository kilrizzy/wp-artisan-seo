<?php

class ArtisanSEOTemplate
{

    public $name;
    public $page;
    public $valid;
    private $apiToken;
    private $apiURL;

    public function __construct($attributes=array())
    {
        $this->setDefaultAttributes($attributes);
        if(!empty($attributes['apiURL'])){
            $this->apiURL = $attributes['apiURL'];
        }
        if(!empty($attributes['apiToken'])){
            $this->apiToken = $attributes['apiToken'];
        }
    }

    private function templatePrefixes() //TODO - make editable
    {
        return array(
            '/topic/' => 'keyword',
            '/location/' => 'locator',
        );
    }

    public function setAPIToken($value){
        $this->apiToken = $value;
    }

    public function setAPIURL($value){
        $this->apiURL = $value;
    }

    public function setDefaultAttributes()
    {
        $this->name = '';
        $this->page = '';
        $this->valid = false;
    }

    public function updateAttributesFromPath($path)
    {
        $templatePrefixes = $this->templatePrefixes();
        $this->setDefaultAttributes();
        foreach ($templatePrefixes as $templatePath => $templateName) {
            if (substr($path, 0, strlen($templatePath)) === $templatePath) {
                $page = str_replace($templatePath, '', $path);
                $this->name = $templateName;
                $this->page = $page;
                $this->valid = true;
            }
        }
        return false;
    }

    public function display(){
        $content = $this->getContent();
        return $content;
    }

    private function getContent()
    {
        $endpoint = $this->apiURL.'/api/template/'.$this->name.'/'.$this->page.'?token='.$this->apiToken;
        $data = array();
        $client = new ArtisanSEOClient();
        $response = $client->call('GET', $endpoint, $data);
        $responseJSON = json_decode($response);
        if (isset($responseJSON->output)) {
            $content = $responseJSON->output;
        } else {
            $content = print_r($responseJSON, true);
        }
        return $content;
    }



}