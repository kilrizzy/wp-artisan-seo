<?php
/**
 * Plugin Name: ArtisanSEO
 * Plugin URI: http://artisanseo.com
 * Description: Pull ArtisanSEO Templates
 * Version: 1.0.0
 * Author: Kilroy Web Development
 * Author URI: http://kilroyweb.com
 * License: GPL2
 */

if (!class_exists('ArtisanSEO')) {
    require_once __DIR__ . '/classes/ArtisanSEO.php';
}
if (!class_exists('ArtisanSEOTemplate')) {
    require_once __DIR__ . '/classes/ArtisanSEOTemplate.php';
}
if (!class_exists('ArtisanSEOClient')) {
    require_once __DIR__ . '/classes/ArtisanSEOClient.php';
}
$artisanSEO = new ArtisanSEO();