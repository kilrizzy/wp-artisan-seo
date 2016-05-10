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
        $this->template = false;
    }

    public function updateAttributesFromPath($path)
    {
        $templates = $this->getTemplates();
        $this->setDefaultAttributes();
        foreach ($templates as $template) {
            $path = trim($path,'/');
            if (substr($path, 0, strlen($template->url_prefix)) === $template->url_prefix) {
                $page = str_replace($template->url_prefix, '', $path);
                $this->template = $template;
                $this->name = $template->type;
                $this->page = trim($page,'/');
                $this->valid = true;
            }
        }
        return false;
    }

    public function display(){
        $content = $this->getContent();
        return $content;
    }

    private function getTemplates(){
        $endpoint = $this->apiURL.'/api/template';
        $data = array(
            'token' => $this->apiToken,
            'r' => time(),
        );
        $client = new ArtisanSEOClient();
        $response = $client->call('GET', $endpoint, $data);
        $responseJSON = json_decode($response);
        if (isset($responseJSON->project_templates)) {
            $templates = $responseJSON->project_templates;
        } else {
            $templates = [];
        }
        return $templates;
    }

    private function getContent()
    {
        $endpoint = $this->apiURL.'/api/template/'.$this->template->id.'/'.$this->page;
        $data = array(
            'token' => $this->apiToken,
            'r' => time(),
        );
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