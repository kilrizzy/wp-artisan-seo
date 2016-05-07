<?php

class ArtisanSEO
{

    public $apiURL;
    public $apiToken;
    public $optionSections;

    public function __construct()
    {
        $this->apiURL = 'http://artisanseo.app';//'https://artisanseo.com';
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
        add_menu_page(
            'ArtisanSEO Settings',
            'ArtisanSEO',
            'manage_options',
            'artisan_seo',
            array($this, 'displaySettingsPage'),
            null,
            99
        );
    }

    public function override404()
    {
        $template = new ArtisanSEOTemplate(array(
            'apiURL' => $this->apiURL,
            'apiToken' => $this->apiToken,
        ));
        $template->updateAttributesFromPath($this->getCurrentPath());
        echo $template->display();
        die();
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
        return $path;
    }

}