<?php
/**
 * Plugin Name: Tamiyouz Developer Hub
 * Description: Safe bootstrap for the Tamiyouz Developer Hub.
 * Version: 1.0.3
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-WP-DEVELOPER-HUB-V1_0_3-SAFE-BOOTSTRAP
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('tamiyouz_devhub_103_menu')) {
    function tamiyouz_devhub_103_menu() {
        add_menu_page(
            'Developer Hub',
            'Developer Hub',
            'manage_options',
            'tamiyouz-developer-hub',
            'tamiyouz_devhub_103_page',
            'dashicons-editor-code',
            3
        );
    }
}

if (!function_exists('tamiyouz_devhub_103_page')) {
    function tamiyouz_devhub_103_page() {
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

add_action('admin_menu', 'tamiyouz_devhub_103_menu');
