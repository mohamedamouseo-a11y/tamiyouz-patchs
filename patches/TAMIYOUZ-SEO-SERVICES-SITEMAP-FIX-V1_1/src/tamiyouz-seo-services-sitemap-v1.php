<?php
/**
 * Plugin Name: Tamiyouz SEO Services Sitemap V1.1
 * Description: Registers the canonical Tamiyouz services sitemap on WordPress core sitemap initialization.
 * Version: 1.1.0
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-SEO-SERVICES-SITEMAP-FIX-V1_1
 */
if (!defined('ABSPATH')) { exit; }

function tyz_services_sitemap_v11_slugs() {
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

add_action('wp_sitemaps_init', function ($wp_sitemaps) {
    if (!class_exists('WP_Sitemaps_Provider')) {
        return;
    }

    if (!class_exists('Tyz_Services_Sitemap_Provider_V11')) {
        class Tyz_Services_Sitemap_Provider_V11 extends WP_Sitemaps_Provider {
            public function __construct() {
                $this->name = 'tyzservices';
                $this->object_type = 'tamiyouz-services';
            }

            public function get_url_list($page_num, $object_subtype = '') {
                if ((int) $page_num !== 1) {
                    return array();
                }

                $urls = array();
                foreach (tyz_services_sitemap_v11_slugs() as $slug) {
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

    if (isset($wp_sitemaps->registry)) {
        $wp_sitemaps->registry->add_provider(
            'tyzservices',
            new Tyz_Services_Sitemap_Provider_V11()
        );
    }
}, 20);
