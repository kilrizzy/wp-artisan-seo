<?php

class ArtisanSEOPage
{

    public $valid;
    public $path;
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

    public function setAPIToken($value){
        $this->apiToken = $value;
    }

    public function setAPIURL($value){
        $this->apiURL = $value;
    }

    public function setDefaultAttributes()
    {
        $this->path = '';
        $this->valid = false;
    }

    public function findByPath($path)
    {
        $pages = $this->getPages();
        foreach ($pages as $page) {
            if($page->fullURI == $path){
                $this->path = $path;
                $this->valid = true;
                return true;
            }
        }
        return false;
    }

    public function getPages(){
        $endpoint = $this->apiURL.'/page';
        $data = array(
            'token' => $this->apiToken,
            'r' => time(),
        );
        $client = new ArtisanSEOClient();
        $responseJSON = $client->call('GET', $endpoint, $data);
        $response = json_decode($responseJSON);
        if (isset($response->pages)) {
            $pages = $response->pages;
        } else {
            $pages = [];
        }
        return $pages;
    }

    public function display($query){
        $content = $this->getContent($query);
        return $content;
    }

    private function getContent($query)
    {
        $endpoint = $this->apiURL.'/page/find';
        $data = array(
            'token' => $this->apiToken,
            'r' => time(),
            'uri' => $this->path,
        );
        $data = array_merge($query,$data);
        $client = new ArtisanSEOClient();
        $responseJSON = $client->call('GET', $endpoint, $data);
        $response = json_decode($responseJSON);
        if (isset($response->output)) {
            $content = $response->output;
        } else {
            $content = print_r($response, true);
        }
        return $content;
    }



}