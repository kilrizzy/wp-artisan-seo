<?php
/**
 * Plugin Name: ArtisanSEO
 * Plugin URI: http://artisanseo.com
 * Description: Pull ArtisanSEO Templates
 * Version: 0.1.7
 * Author: Kilroy Web Development
 * Author URI: http://kilroyweb.com
 * License: GPL2
 */

require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
$className = PucFactory::getLatestClassVersion('PucGitHubChecker');
$myUpdateChecker = new $className(
    'https://github.com/kilrizzy/wp-artisan-seo/',
    __FILE__,
    'master'
);

if (!class_exists('ArtisanSEO')) {
    require_once __DIR__ . '/classes/ArtisanSEO.php';
}
if (!class_exists('ArtisanSEOPage')) {
    require_once __DIR__ . '/classes/ArtisanSEOPage.php';
}
if (!class_exists('ArtisanSEOClient')) {
    require_once __DIR__ . '/classes/ArtisanSEOClient.php';
}
$artisanSEO = new ArtisanSEO();