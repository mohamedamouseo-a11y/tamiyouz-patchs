<?php
require __DIR__ . '/common.php';
tyz_site_v1_header('SERVICES', 'حلول مصممة <em>لنمو أعمالك.</em>', 'خدمات مترابطة تجمع التقنية والذكاء والتجربة والنمو داخل منظومة واحدة.');
?>
<section class="tyz-site-section"><div class="tyz-site-shell">
<div class="tyz-site-grid">
<?php if (have_posts()): while (have_posts()): the_post(); ?>
<article class="tyz-site-card"><small>خدمة</small><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html(get_the_excerpt() ?: wp_trim_words(get_the_content(), 24)); ?></p><a class="tyz-site-link" href="<?php the_permalink(); ?>">اكتشف الخدمة ←</a></article>
<?php endwhile; else: ?>
<div class="tyz-site-empty"><h2>نبني صفحة الخدمات الآن.</h2><p>سيتم إضافة صفحات الخدمات المتخصصة هنا تباعًا.</p></div>
<?php endif; ?>
</div>
</div></section>
<?php tyz_site_v1_footer();