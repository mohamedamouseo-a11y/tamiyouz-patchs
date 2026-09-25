<?php
/**
 * Plugin Name: Tamiyouz Developer Hub
 * Description: Safe TCRM-style GitHub review hub for Tamiyouz WordPress custom code.
 * Version: 1.1.0
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-WP-DEVELOPER-HUB-V1_1-READONLY-REVIEW
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('tamiyouz_devhub_110_menu')) {
    function tamiyouz_devhub_110_menu() {
        add_menu_page(
            'Developer Hub',
            'Developer Hub',
            'manage_options',
            'tamiyouz-developer-hub',
            'tamiyouz_devhub_110_page',
            'dashicons-editor-code',
            3
        );
    }
}

if (!function_exists('tamiyouz_devhub_110_page')) {
    function tamiyouz_devhub_110_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized.');
        }

        $admin_file = __DIR__ . '/tamiyouz-developer-hub/admin.php';
        if (!is_file($admin_file)) {
            echo '<div class="wrap"><h1>Developer Hub</h1><div class="notice notice-error"><p>Developer Hub admin module is missing.</p></div></div>';
            return;
        }

        require $admin_file;
    }
}

add_action('admin_menu', 'tamiyouz_devhub_110_menu');
