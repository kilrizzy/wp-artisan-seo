<?php

class ArtisanSEO
{

    public $apiURL;
    public $apiToken;
    public $optionSections;

    public function __construct()
    {
        $this->apiURL = 'https://artisanseo.com/api';
        if (defined('ARTISAN_SEO_URL')) {
            $newAPIURL = ARTISAN_SEO_URL;
            if (!empty($newAPIURL)) {
                $this->apiURL = $newAPIURL;
            }
        }
        $this->apiToken = get_option('artisanseo_token');
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'initAdmin'));
        add_action('admin_menu', array($this, 'adminMenu'));
        add_action('whitelist_options', array($this, 'whitelistCustomOptions'), 11);
        add_filter('template_redirect', array($this, 'override404'));
        $this->optionSections = array(
            'artisanseo_api_details' => array(
                'title' => 'API Details',
                'callback' => array($this, 'displaySectionAPIDetails'),
                'page' => 'artisan_seo',
                'fields' => array(
                    'artisanseo_token' => array(
                        'label' => 'API Token',
                        'callback' => array($this, 'displayFieldToken'),
                    ),
                ),
            ),
        );
    }

    public function init()
    {
        add_shortcode('artisanseo_states_list', array($this, 'displayStatesList'));
        add_shortcode('artisanseo_cities_list', array($this, 'displayCitiesList'));
    }

    public function initAdmin()
    {
        foreach ($this->optionSections as $optionSectionId => $optionSection) {
            add_settings_section(
                $optionSectionId,
                $optionSection['title'],
                $optionSection['callback'],
                $optionSection['page']
            );
            foreach ($optionSection['fields'] as $optionFieldId => $optionField) {
                add_settings_field(
                    $optionFieldId,
                    $optionField['label'],
                    $optionField['callback'],
                    $optionSection['page'],
                    $optionSectionId
                );
                register_setting($optionSectionId, $optionFieldId);
            }
        }
    }

    public function adminMenu()
    {
        $icon = 'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDguOTcgMTQ2LjAyIj48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6I2VlYWM0NTt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPmljb248L3RpdGxlPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTMyNCwzMjJjMS40MSw0LjE2LDMuMjIsNy4zLDguMzEsOC42MSwzLjgsMSw1LjM5LDUuNjMsNi42Myw5LjQ5LDQuMDUsMTIuNTksNS41NSwyNS42Niw3LDM4LjczLDEuMjQsMTAuOTEsMi41OCwyMS44MiwzLjQ1LDMyLjc2LDAuMzUsNC4zOCwyLjE0LDUuNyw2LjIyLDUuMzksMS42Ny0uMTMsNS4xNC4yMyw0LjkzLDAuNi0xLjU0LDIuNzEsMS44OCw1LjY5LS45LDguMjktMi4xMSwyLTMuMDksNS4wOS02LjMsNi4wNi0yLjE1LjY1LTEuNzMsMi42MS0xLjU4LDQuMjJhMTYuOSwxNi45LDAsMCwxLTEuOTMsMTAuNTUsMTAuNjgsMTAuNjgsMCwwLDAtMS4zMSw4LjE0YzEuNjQsNi41NywxLjQ4LDYuNi0yLjE3LDEzLjE3LTMuNjItMy4xMS01LTcuMzktNi4zNy0xMS42MXEtMi42OS04LjMtNS0xNi43M2MtMC43Mi0yLjY3LTEuOTEtMy44MS00Ljg3LTMuNTUtMTQuOTEsMS4zNC0yOS44MiwyLjc3LTQ0LjgyLDIuNy0yLjY2LDAtNC43My41OC02LDMuMzQtMy40OCw3LjQ3LTEwLjE2LDExLjQxLTE3LjEzLDE1LTEuMzcuNy0yLjE3LDAuMTktMy4wOS0uNTctMS41MS0xLjI1LTIuNTYtMi43MS0xLjc4LTQuNzdhNjAuODgsNjAuODgsMCwwLDEsMy40MS04LjNjMi4zMy00LjE1LDEtNi42My0yLjc2LTguOTEtMi41Ni0xLjU0LTcuMTgtMS43OS01LjkxLTYuNjMsMS4yLTQuNTksNS4zNC0zLjkyLDguNjYtMy45NCw2Ljg2LS4wNSwxMC42OS0yLjg4LDEzLjY3LTkuNTEsMTIuMDgtMjYuODcsMjMuOTUtNTMuODYsMzguMjMtNzkuNjcsMi40Ny00LjQ3LDUuNTgtOC41OSw4LjQtMTIuODdoM1ptLTM1LjgzLDk5YzEyLjc4LS43LDI0LjUzLTEuMzMsMzYuMjgtMiw2Ljg2LS40LDcuMTctMC45NCw2LjIzLTcuODctMS42OS0xMi40OS0zLjEtMjUtNC45Mi0zNy41LTEuMDktNy40NC0xLjMzLTE1LTQtMjIuNTZDMzExLDM3NC4zNCwyOTguMjQsMzk2LjUyLDI4OC4xNyw0MjFaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMjUxLjgzIC0zMjIpIi8+PC9zdmc+';
        add_menu_page(
            'ArtisanSEO Settings',
            'ArtisanSEO',
            'manage_options',
            'artisan_seo',
            array($this, 'displaySettingsPage'),
            $icon,
            99
        );
    }

    public function override404()
    {
        $pathRaw = $this->getCurrentPath();
        //performance - don't run if a 404 file
        if (!empty($pathRaw) && !strstr($pathRaw, '.')) {
            $pathParts = $this->parsePathParts($pathRaw);
            $path = $pathParts['path'];
            $page = new ArtisanSEOPage(array(
                'apiURL' => $this->apiURL,
                'apiToken' => $this->apiToken,
            ));
            $page->findByPath($path);
            if ($page->valid) {
                $query = $this->getQuery();
                echo $page->display($query);
                die();
            }
        }
    }

    public function getQuery(){
        parse_str($_SERVER['QUERY_STRING'], $query);
        $pathRaw = $this->getCurrentPath();
        $pathParts = $this->parsePathParts($pathRaw);
        $query = array_merge($query,$pathParts['query']);
        return $query;
    }

    public function parsePathParts($path)
    {
        $pathParts = [];
        $query = [];
        $pathSegments = explode('/', $path);
        $queryKeys = ['state', 'city', 'zipcode', 'keyword'];
        $realPathSegmentSelect = true;
        $realPathSegments = [];
        foreach ($pathSegments as $key => $pathSegment) {
            //Check if query mask
            if (in_array($pathSegment, $queryKeys) && isset($pathSegments[$key + 1])) {
                $realPathSegmentSelect = false;
                $query[$pathSegment] = $pathSegments[$key + 1];
            }
            if ($realPathSegmentSelect) {
                $realPathSegments[] = $pathSegment;
            }
        }
        //The path might start with a keyword ie: url.com/state/state/NJ...
        if (empty($realPathSegments) && !empty($pathSegments)) {
            $realPathSegments[] = $pathSegments[0];
        }
        $realPath = implode('/', $realPathSegments);
        $pathParts['path'] = $realPath;
        $pathParts['query'] = $query;
        return $pathParts;
    }

    public function whitelistCustomOptions($whiteListOptions)
    {
        foreach ($this->optionSections as $optionSectionId => $optionSection) {
            $newWhitelistOptions = array();
            $newWhitelistOptions[$optionSection['page']] = array();
            foreach ($optionSection['fields'] as $optionFieldId => $optionField) {
                $newWhitelistOptions[$optionSection['page']][] = $optionFieldId;
            }
            $whiteListOptions = add_option_whitelist($newWhitelistOptions, $whiteListOptions);
        }
        return $whiteListOptions;
    }

    function displaySectionAPIDetails()
    {
        echo '';
    }

    function displayFieldToken()
    {
        $output = array();
        $output[] = '<input type="text" name="artisanseo_token" id="artisanseo_token" value="' . get_option('artisanseo_token') . '" />';
        $output = implode("\n", $output);
        echo $output;
    }

    public function displaySettingsPage()
    {
        $output = array();
        $output[] = '<div class="wrap">';
        $output[] = '<h2>ArtisanSEO Settings</h2>';
        $output[] = '<form method="post" action="options.php">';
        $output = implode("\n", $output);
        echo $output;
        settings_fields("artisanseo_api_details");
        do_settings_sections("artisan_seo");
        submit_button();
        $output = array();
        $output[] = '</form>';
        $output[] = '</div>';
        $output = implode("\n", $output);
        echo $output;
    }

    private function getCurrentPath()
    {
        $request = parse_url($_SERVER['REQUEST_URI']);
        $path = $request["path"];
        $path = rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $path), '/');
        $path = trim($path, '/');
        return $path;
    }

    public function displayStatesList($atts)
    {
        $a = shortcode_atts(array(
            'prefix' => false,
        ), $atts);
        $output = array();
        $content = $this->getStatesList($a['prefix']);
        $output[] = $content;
        $output = implode("\n", $output);
        return $output;
    }

    private function getStatesList($urlPrefix = false)
    {
        $endpoint = $this->apiURL . '/state/list';
        $data = array(
            'token' => $this->apiToken,
            'r' => time(),
            'url_prefix' => $urlPrefix,
        );
        $client = new ArtisanSEOClient();
        $responseJSON = $client->call('GET', $endpoint, $data);
        $response = json_decode($responseJSON);
        if (isset($response->output)) {
            $content = $response->output;
        } else {
            $content = '<span style="color:#FF0000;">' . print_r($responseJSON, true) . '</span>';
        }
        return $content;
    }

    public function displayCitiesList($atts)
    {
        $a = shortcode_atts(array(
            'prefix' => false,
            'state' => false,
        ), $atts);
        $output = array();
        $content = $this->getCitiesList($a['state'], $a['prefix']);
        $output[] = $content;
        $output = implode("\n", $output);
        return $output;
    }

    private function getCitiesList($state, $urlPrefix = false)
    {
        $endpoint = $this->apiURL . '/city/list';
        $data = array(
            'token' => $this->apiToken,
            'r' => time(),
            'url_prefix' => $urlPrefix,
            'state' => $state,
        );
        $client = new ArtisanSEOClient();
        $responseJSON = $client->call('GET', $endpoint, $data);
        $response = json_decode($responseJSON);
        if (isset($response->output)) {
            $content = $response->output;
        } else {
            $content = '<span style="color:#FF0000;">' . print_r($responseJSON, true) . '</span>';
        }
        return $content;
    }

}