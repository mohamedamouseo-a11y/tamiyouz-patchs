<?php
if (!defined('ABSPATH')) { exit; }
if (!function_exists('tyz_site_v1_header')) {
function tyz_site_v1_header($eyebrow, $title, $lead) {
    $logo = function_exists('tamiyouz_home_v21_brand_asset_url') ? tamiyouz_home_v21_brand_asset_url() : '';
    ?>
<!doctype html>
<html <?php language_attributes(); ?> dir="rtl" data-tyz-theme="light">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>(function(){try{var t=localStorage.getItem('tamiyouz-theme');document.documentElement.setAttribute('data-tyz-theme',t==='dark'?'dark':'light')}catch(e){}})();</script>
<?php wp_head(); ?>
</head>
<body <?php body_class('tyz-site-v1'); ?>><?php wp_body_open(); ?>
<div id="tyz-site-v1">
<header class="tyz-site-header">
<div class="tyz-site-shell tyz-site-header__in">
<a class="tyz-site-brand" href="<?php echo esc_url(home_url('/')); ?>">
<?php if ($logo): ?><img src="<?php echo esc_url($logo); ?>" alt="تميز"><?php endif; ?>
<span>تميز<small>TAMIYOUZ</small></span>
</a>
<nav><a href="<?php echo esc_url(home_url('/services/')); ?>">الخدمات</a><a href="<?php echo esc_url(home_url('/work/')); ?>">أعمالنا</a><a href="<?php echo esc_url(home_url('/blog/')); ?>">المقالات</a><a href="<?php echo esc_url(home_url('/#contact')); ?>">تواصل معنا</a></nav>
<button type="button" data-tyz-site-theme aria-label="تبديل المظهر">◐</button>
</div>
</header>
<main>
<section class="tyz-site-hero"><div class="tyz-site-shell"><span><?php echo esc_html($eyebrow); ?></span><h1><?php echo wp_kses_post($title); ?></h1><p><?php echo esc_html($lead); ?></p></div></section>
<?php
}}
if (!function_exists('tyz_site_v1_footer')) {
function tyz_site_v1_footer() { ?>
</main>
<footer class="tyz-site-footer"><div class="tyz-site-shell"><b>تميز</b><span>AI · SOFTWARE · DIGITAL GROWTH</span><small>© <?php echo esc_html(date('Y')); ?> Tamiyouz</small></div></footer>
</div>
<?php wp_footer(); ?></body></html>
<?php }}
