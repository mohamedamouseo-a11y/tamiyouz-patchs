<?php
/**
 * Plugin Name: Tamiyouz SEO Services Sitemap V1.2
 * Description: Robust direct registration of the canonical Tamiyouz services sitemap provider.
 * Version: 1.2.0
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-SEO-SERVICES-SITEMAP-FIX-V1_2
 */
if (!defined('ABSPATH')) { exit; }

function tyz_services_sitemap_v12_slugs() {
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

add_action('init', function () {
    if (!function_exists('wp_register_sitemap_provider') || !class_exists('WP_Sitemaps_Provider')) {
        return;
    }

    if (!class_exists('Tyz_Services_Sitemap_Provider_V12')) {
        class Tyz_Services_Sitemap_Provider_V12 extends WP_Sitemaps_Provider {
            public function __construct() {
                $this->name = 'tyzservices';
                $this->object_type = 'tamiyouz-services';
            }

            public function get_url_list($page_num, $object_subtype = '') {
                if ((int) $page_num !== 1) {
                    return array();
                }

                $urls = array();
                foreach (tyz_services_sitemap_v12_slugs() as $slug) {
                    $urls[] = array(
                        'loc' => home_url('/services/' . $slug . '/'),
                    );
                }
                return $urls;
            }

            public function get_max_num_pages($object_subtype = '') {
                return 1;
            }
        }
    }

    wp_register_sitemap_provider(
        'tyzservices',
        new Tyz_Services_Sitemap_Provider_V12()
    );
}, 0);
