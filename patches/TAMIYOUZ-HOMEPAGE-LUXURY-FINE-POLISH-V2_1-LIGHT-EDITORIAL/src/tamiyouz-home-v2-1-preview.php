<?php
/**
 * Plugin Name: Tamiyouz Homepage V2.1 Light Editorial Preview
 * Description: Preview-only premium light editorial homepage inspired by the approved Tamiyouz reference.
 * Version: 2.1.0
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-HOMEPAGE-LUXURY-FINE-POLISH-V2_1-LIGHT-EDITORIAL
 * Asset Delivery: INLINE_HOST_SAFE
 */

if (!defined('ABSPATH')) { exit; }

define('TAMIYOUZ_HOME_V21_DIR', __DIR__ . '/tamiyouz-homepage-v2-1');
define('TAMIYOUZ_HOME_V21_VERSION', '2.1.0');

function tamiyouz_home_v21_is_preview() {
    return is_front_page()
        && isset($_GET['tamiyouz_v21_preview'])
        && $_GET['tamiyouz_v21_preview'] === '1'
        && current_user_can('manage_options');
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
