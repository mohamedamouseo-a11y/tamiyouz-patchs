<?php
/**
 * Plugin Name: Tamiyouz Homepage Render Recovery V2.9.2
 * Description: Fail-open visibility recovery for homepage reveal elements after V2.9.1 visual regression.
 * Version: 2.9.2
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-HOMEPAGE-RENDER-RECOVERY-V2_9_2
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_head', function () {
    if (!is_front_page()) { return; }
    echo "\n<style id=\"tamiyouz-home-render-recovery-v2-9-2\">
    /* V2.9.2 fail-open render recovery: content must never depend on JS to be visible. */
    #tyz-home-v21 .tyz-reveal{
        opacity:1!important;
        transform:none!important;
        transition:none!important;
        visibility:visible!important;
    }
    </style>\n";
}, PHP_INT_MAX);
