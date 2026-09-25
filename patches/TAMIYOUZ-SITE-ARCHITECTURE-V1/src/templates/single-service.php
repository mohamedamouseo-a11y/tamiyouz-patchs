<?php
require __DIR__ . '/common.php';
the_post();
tyz_site_v1_header('SERVICE', get_the_title(), get_the_excerpt() ?: 'حل رقمي مصمم حول احتياج العمل الفعلي.');
?>
<article class="tyz-site-article"><div class="tyz-site-shell tyz-site-article__body"><?php the_content(); ?></div></article>
<section class="tyz-site-cta"><div class="tyz-site-shell"><div><span>READY WHEN YOU ARE</span><h2>هل تحتاج حلًا مشابهًا؟</h2></div><a href="<?php echo esc_url(home_url('/#contact')); ?>">ابدأ مشروعك ←</a></div></section>
<?php tyz_site_v1_footer();