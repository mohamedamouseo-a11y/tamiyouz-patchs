<?php
/**
 * Plugin Name: Tamiyouz SEO Services Sitemap V1.3
 * Description: Host-agnostic standalone sitemap for canonical Tamiyouz service URLs.
 * Version: 1.3.0
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-SEO-SERVICES-SITEMAP-FIX-V1_3
 */
if (!defined('ABSPATH')) { exit; }

function tyz_services_sitemap_v13_slugs() {
    return array(
        'ai-solutions',
        'custom-software',
        'crm-sales-automation',
        'web-design-development',
        'seo-organic-growth',
        'performance-marketing',
        'automation-integrations',
        'data-analytics',
    );
}

function tyz_services_sitemap_v13_url() {
    return home_url('/services-sitemap.xml');
}

add_action('template_redirect', function () {
    if (empty($_SERVER['REQUEST_URI'])) return;

    $path = parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH);
    $path = '/' . ltrim((string) $path, '/');

    if ($path !== '/services-sitemap.xml') {
        return;
    }

    status_header(200);
    nocache_headers();
    header('Content-Type: application/xml; charset=UTF-8');
    header('X-Robots-Tag: noindex, follow', true);

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach (tyz_services_sitemap_v13_slugs() as $slug) {
        $loc = home_url('/services/' . $slug . '/');
        echo "  <url><loc>" . esc_url($loc) . "</loc></url>\n";
    }

    echo '</urlset>';
    exit;
}, 0);

add_filter('robots_txt', function ($output, $public) {
    if (!$public) return $output;

    $line = 'Sitemap: ' . tyz_services_sitemap_v13_url();
    if (strpos($output, $line) === false) {
        $output = rtrim($output) . "\n" . $line . "\n";
    }
    return $output;
}, 99, 2);

add_action('wp_head', function () {
    if (is_front_page() || is_post_type_archive('tyz_service') || is_singular('tyz_service')) {
        echo '<link rel="sitemap" type="application/xml" href="' . esc_url(tyz_services_sitemap_v13_url()) . '">' . "\n";
    }
}, 3);
