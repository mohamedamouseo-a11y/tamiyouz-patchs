<?php
/**
 * Plugin Name: Tamiyouz Homepage Luxury V1
 * Description: Preview-first premium RTL homepage for Tamiyouz with light/dark mode, AI/software direction and hero video support.
 * Version: 1.1.0
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-HOMEPAGE-LUXURY-DUAL-THEME-V1
 * Asset Fix: INLINE_ASSETS_V1_1
 */

if (!defined('ABSPATH')) {
    exit;
}

define('TAMIYOUZ_HOME_V1_DIR', __DIR__ . '/tamiyouz-homepage-v1');
define('TAMIYOUZ_HOME_V1_VERSION', '1.1.0');

function tamiyouz_home_v1_is_preview(): bool {
    return isset($_GET['tamiyouz_preview'])
        && $_GET['tamiyouz_preview'] === '1'
        && current_user_can('manage_options');
}

function tamiyouz_home_v1_is_enabled(): bool {
    return get_option('tamiyouz_home_v1_enabled', '0') === '1';
}

function tamiyouz_home_v1_is_active(): bool {
    return is_front_page() && (tamiyouz_home_v1_is_enabled() || tamiyouz_home_v1_is_preview());
}

add_filter('template_include', function ($template) {
    if (!tamiyouz_home_v1_is_active()) {
        return $template;
    }

    $custom = TAMIYOUZ_HOME_V1_DIR . '/template.php';

    if (is_readable($custom)) {
        if (tamiyouz_home_v1_is_preview()) {
            nocache_headers();
        }
        return $custom;
    }

    return $template;
}, 999);

/*
 * Host-safe asset delivery.
 *
 * Some managed WordPress hosts block direct HTTP access to wp-content/mu-plugins.
 * The homepage therefore injects its own trusted CSS/JS from disk only when the
 * preview/published homepage is active, avoiding external /mu-plugins asset URLs.
 */
add_action('wp_head', function () {
    if (!tamiyouz_home_v1_is_active()) {
        return;
    }

    $css_path = TAMIYOUZ_HOME_V1_DIR . '/assets/home.css';
    if (!is_readable($css_path)) {
        return;
    }

    $css = file_get_contents($css_path);
    if ($css === false) {
        return;
    }

    echo "\n<style id=\"tamiyouz-home-v1-css\" data-tamiyouz-version=\"" . esc_attr(TAMIYOUZ_HOME_V1_VERSION) . "\">\n";
    echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted local patch asset.
    echo "\n</style>\n";
}, 100);

add_action('wp_footer', function () {
    if (!tamiyouz_home_v1_is_active()) {
        return;
    }

    $js_path = TAMIYOUZ_HOME_V1_DIR . '/assets/home.js';
    if (!is_readable($js_path)) {
        return;
    }

    $js = file_get_contents($js_path);
    if ($js === false) {
        return;
    }

    echo "\n<script id=\"tamiyouz-home-v1-js\" data-tamiyouz-version=\"" . esc_attr(TAMIYOUZ_HOME_V1_VERSION) . "\">\n";
    echo $js; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted local patch asset.
    echo "\n</script>\n";
}, 100);

add_filter('body_class', function ($classes) {
    if (tamiyouz_home_v1_is_active()) {
        $classes[] = 'tamiyouz-home-v1-active';
    }
    return $classes;
});

add_action('admin_menu', function () {
    add_options_page(
        'Tamiyouz Homepage V1',
        'Tamiyouz Homepage V1',
        'manage_options',
        'tamiyouz-home-v1',
        'tamiyouz_home_v1_settings'
    );
});

function tamiyouz_home_v1_settings(): void {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['tamiyouz_home_v1_save'])) {
        check_admin_referer('tamiyouz_home_v1_save');

        update_option(
            'tamiyouz_home_v1_enabled',
            isset($_POST['tamiyouz_home_v1_enabled']) ? '1' : '0'
        );

        $video = isset($_POST['tamiyouz_home_v1_hero_video'])
            ? esc_url_raw(wp_unslash($_POST['tamiyouz_home_v1_hero_video']))
            : '';

        update_option('tamiyouz_home_v1_hero_video', $video);

        echo '<div class="notice notice-success is-dismissible"><p>تم حفظ إعدادات صفحة Tamiyouz الجديدة.</p></div>';
    }

    $enabled = tamiyouz_home_v1_is_enabled();
    $video   = get_option('tamiyouz_home_v1_hero_video', '');
    $preview = add_query_arg('tamiyouz_preview', '1', home_url('/'));
    ?>
    <div class="wrap" dir="rtl">
        <h1>Tamiyouz Homepage V1</h1>
        <p>صفحة رئيسية Premium محايدة بصريًا، Light/Dark، مع Hero Video وطابع AI + Software.</p>

        <form method="post">
            <?php wp_nonce_field('tamiyouz_home_v1_save'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">النشر</th>
                    <td>
                        <label>
                            <input type="checkbox" name="tamiyouz_home_v1_enabled" value="1" <?php checked($enabled); ?>>
                            تفعيل الصفحة الجديدة كواجهة الموقع العامة.
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="tamiyouz_home_v1_hero_video">Hero Video URL</label></th>
                    <td>
                        <input
                            type="url"
                            class="regular-text code"
                            id="tamiyouz_home_v1_hero_video"
                            name="tamiyouz_home_v1_hero_video"
                            value="<?php echo esc_attr($video); ?>"
                            placeholder="https://example.com/uploads/tamiyouz-hero.mp4"
                        >
                        <p class="description">MP4/WebM موصى به. عند تركه فارغًا يظهر مشهد AI متحرك بدل الفيديو.</p>
                    </td>
                </tr>
            </table>

            <?php submit_button('حفظ', 'primary', 'tamiyouz_home_v1_save'); ?>
        </form>

        <p>
            <a class="button button-secondary" href="<?php echo esc_url($preview); ?>" target="_blank" rel="noopener">
                فتح المعاينة الخاصة
            </a>
        </p>
    </div>
    <?php
}
