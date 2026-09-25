<?php
/**
 * Plugin Name: Tamiyouz Site Architecture
 * Description: SEO-ready Services, Work/Case Studies and Blog foundation.
 * Version: 1.0.0
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-SITE-ARCHITECTURE-V1
 */
if (!defined('ABSPATH')) { exit; }

define('TYZ_SITE_V1_DIR', __DIR__ . '/tamiyouz-site-v1');
define('TYZ_SITE_V1_VERSION', '1.0.0');

add_action('init', function () {
    register_post_type('tyz_service', array(
        'labels' => array(
            'name' => 'الخدمات',
            'singular_name' => 'خدمة',
            'add_new_item' => 'إضافة خدمة',
            'edit_item' => 'تعديل الخدمة',
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-admin-tools',
        'has_archive' => 'services',
        'rewrite' => array('slug' => 'services', 'with_front' => false),
        'supports' => array('title','editor','excerpt','thumbnail','revisions','page-attributes'),
        'menu_position' => 21,
    ));

    register_post_type('tyz_case_study', array(
        'labels' => array(
            'name' => 'أعمالنا',
            'singular_name' => 'دراسة حالة',
            'add_new_item' => 'إضافة دراسة حالة',
            'edit_item' => 'تعديل دراسة الحالة',
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-portfolio',
        'has_archive' => 'work',
        'rewrite' => array('slug' => 'work', 'with_front' => false),
        'supports' => array('title','editor','excerpt','thumbnail','revisions','page-attributes'),
        'menu_position' => 22,
    ));

    add_rewrite_rule('^blog/?$', 'index.php?tyz_blog=1', 'top');
    add_rewrite_rule('^blog/page/([0-9]+)/?$', 'index.php?tyz_blog=1&paged=$matches[1]', 'top');
}, 5);

add_filter('query_vars', function ($vars) {
    $vars[] = 'tyz_blog';
    return $vars;
});

add_action('init', function () {
    $key = 'tamiyouz_site_v1_rewrite_version';
    if (get_option($key) !== TYZ_SITE_V1_VERSION) {
        flush_rewrite_rules(false);
        update_option($key, TYZ_SITE_V1_VERSION, false);
    }
}, 99);

function tyz_site_v1_is_managed() {
    return is_post_type_archive(array('tyz_service','tyz_case_study'))
        || is_singular(array('tyz_service','tyz_case_study'))
        || (bool) get_query_var('tyz_blog');
}

add_filter('template_include', function ($template) {
    $map = array();
    if (is_post_type_archive('tyz_service')) $map[] = 'archive-services.php';
    elseif (is_post_type_archive('tyz_case_study')) $map[] = 'archive-work.php';
    elseif (is_singular('tyz_service')) $map[] = 'single-service.php';
    elseif (is_singular('tyz_case_study')) $map[] = 'single-work.php';
    elseif (get_query_var('tyz_blog')) $map[] = 'blog.php';
    if (!$map) return $template;

    $custom = TYZ_SITE_V1_DIR . '/templates/' . $map[0];
    return is_readable($custom) ? $custom : $template;
}, 1000);

add_action('wp_head', function () {
    if (!tyz_site_v1_is_managed()) return;
    $css = TYZ_SITE_V1_DIR . '/assets/site.css';
    if (is_readable($css)) {
        echo "\n<style id=\"tamiyouz-site-v1-css\">" . file_get_contents($css) . "</style>\n";
    }
}, 90);

add_action('wp_footer', function () {
    if (!tyz_site_v1_is_managed()) return;
    $js = TYZ_SITE_V1_DIR . '/assets/site.js';
    if (is_readable($js)) {
        echo "\n<script id=\"tamiyouz-site-v1-js\">" . file_get_contents($js) . "</script>\n";
    }
}, 90);

add_filter('document_title_parts', function ($parts) {
    if (is_post_type_archive('tyz_service')) $parts['title'] = 'خدمات تميز';
    elseif (is_post_type_archive('tyz_case_study')) $parts['title'] = 'أعمال تميز ودراسات الحالة';
    elseif (get_query_var('tyz_blog')) $parts['title'] = 'مقالات تميز';
    return $parts;
}, 30);

add_action('wp_head', function () {
    if (!tyz_site_v1_is_managed()) return;

    $desc = '';
    if (is_post_type_archive('tyz_service')) $desc = 'خدمات تميز في الذكاء الاصطناعي والبرمجيات والأتمتة والنمو الرقمي.';
    elseif (is_post_type_archive('tyz_case_study')) $desc = 'مشاريع ودراسات حالة من تميز توضح الأنظمة والحلول الرقمية التي نبنيها.';
    elseif (get_query_var('tyz_blog')) $desc = 'مقالات عملية من تميز عن المبيعات وCRM والذكاء الاصطناعي والأتمتة والنمو الرقمي.';
    elseif (is_singular()) {
        $desc = has_excerpt() ? get_the_excerpt() : wp_strip_all_tags(wp_trim_words(get_the_content(), 28, '…'));
    }
    if ($desc) echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";

    $canonical = '';
    if (is_post_type_archive('tyz_service')) $canonical = get_post_type_archive_link('tyz_service');
    elseif (is_post_type_archive('tyz_case_study')) $canonical = get_post_type_archive_link('tyz_case_study');
    elseif (get_query_var('tyz_blog')) $canonical = home_url('/blog/');
    elseif (is_singular()) $canonical = get_permalink();
    if ($canonical) echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";

    echo '<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">' . "\n";
}, 4);

add_filter('wp_sitemaps_post_types', function ($types) {
    if (isset($types['tyz_service'])) $types['tyz_service']->object_subtype = 'tyz_service';
    if (isset($types['tyz_case_study'])) $types['tyz_case_study']->object_subtype = 'tyz_case_study';
    return $types;
});

add_filter('wp_sitemaps_index', function ($sitemaps) {
    $sitemaps[] = array(
        'loc' => home_url('/?tyz_routes_sitemap=1'),
        'lastmod' => gmdate('c'),
    );
    return $sitemaps;
});

add_action('template_redirect', function () {
    if (!isset($_GET['tyz_routes_sitemap']) || $_GET['tyz_routes_sitemap'] !== '1') return;
    status_header(200);
    header('Content-Type: application/xml; charset=UTF-8');
    $routes = array('/services/','/work/','/blog/');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($routes as $route) {
        echo '<url><loc>' . esc_url(home_url($route)) . '</loc><lastmod>' . esc_html(gmdate('c')) . '</lastmod></url>';
    }
    echo '</urlset>';
    exit;
}, 0);
