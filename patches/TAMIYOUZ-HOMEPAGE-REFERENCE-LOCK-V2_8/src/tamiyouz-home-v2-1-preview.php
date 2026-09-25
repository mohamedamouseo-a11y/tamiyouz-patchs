<?php
/**
 * Plugin Name: Tamiyouz Homepage V2.1 Light Editorial Preview
 * Description: Preview-only premium light editorial homepage inspired by the approved Tamiyouz reference.
 * Version: 2.8.0
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-HOMEPAGE-REFERENCE-LOCK-V2_8
 * Asset Delivery: INLINE_HOST_SAFE
 */

if (!defined('ABSPATH')) { exit; }

define('TAMIYOUZ_HOME_V21_DIR', __DIR__ . '/tamiyouz-homepage-v2-1');
define('TAMIYOUZ_HOME_V21_VERSION', '2.8.0');

function tamiyouz_home_v21_is_preview() {
    return is_front_page();
}

add_filter('template_include', function ($template) {
    if (!tamiyouz_home_v21_is_preview()) { return $template; }
    $custom = TAMIYOUZ_HOME_V21_DIR . '/template.php';
    if (is_readable($custom)) {
        nocache_headers();
        return $custom;
    }
    return $template;
}, 1001);

add_action('wp_head', function () {
    if (!tamiyouz_home_v21_is_preview()) { return; }
    $path = TAMIYOUZ_HOME_V21_DIR . '/assets/home.css';
    if (!is_readable($path)) { return; }
    $css = file_get_contents($path);
    if ($css === false) { return; }
    echo "\n<style id=\"tamiyouz-home-v21-css\" data-version=\"" . esc_attr(TAMIYOUZ_HOME_V21_VERSION) . "\">\n";
    echo $css;
    echo "\n</style>\n";
}, 100);

add_action('wp_footer', function () {
    if (!tamiyouz_home_v21_is_preview()) { return; }
    $path = TAMIYOUZ_HOME_V21_DIR . '/assets/home.js';
    if (!is_readable($path)) { return; }
    $js = file_get_contents($path);
    if ($js === false) { return; }
    echo "\n<script id=\"tamiyouz-home-v21-js\" data-version=\"" . esc_attr(TAMIYOUZ_HOME_V21_VERSION) . "\">\n";
    echo $js;
    echo "\n</script>\n";
}, 100);

add_filter('body_class', function ($classes) {
    if (tamiyouz_home_v21_is_preview()) { $classes[] = 'tamiyouz-home-v21-preview'; }
    return $classes;
});


/* TAMIYOUZ-HOMEPAGE-BRAND-LOCK-V2_3 */
if (!function_exists('tamiyouz_home_v21_brand_asset_url')) {
    function tamiyouz_home_v21_brand_asset_url() {
        return add_query_arg(
            array('tamiyouz_brand_asset' => 'logo', 'v' => '28'),
            home_url('/')
        );
    }
}

add_action('template_redirect', function () {
    if (!isset($_GET['tamiyouz_brand_asset']) || $_GET['tamiyouz_brand_asset'] !== 'logo') {
        return;
    }

    $asset = TAMIYOUZ_HOME_V21_DIR . '/assets/tamiyouz-logo.svg';
    if (!is_readable($asset)) {
        status_header(404);
        exit;
    }

    header('Content-Type: image/svg+xml; charset=utf-8');
    header('Cache-Control: public, max-age=31536000, immutable');
    header('X-Content-Type-Options: nosniff');
    readfile($asset);
    exit;
}, 0);

if (!function_exists('tamiyouz_home_v21_brand_head')) {
    function tamiyouz_home_v21_brand_head() {
        $url = esc_url(tamiyouz_home_v21_brand_asset_url());
        echo "\n<link rel=\"icon\" type=\"image/svg+xml\" href=\"" . $url . "\">";
        echo "\n<link rel=\"apple-touch-icon\" href=\"" . $url . "\">\n";
    }
}
add_action('wp_head', 'tamiyouz_home_v21_brand_head', 2);
add_action('admin_head', 'tamiyouz_home_v21_brand_head', 2);
add_action('login_head', 'tamiyouz_home_v21_brand_head', 2);
