<?php
require __DIR__ . '/common.php';
tyz_site_v1_header('WORK · CASE STUDIES', 'أعمال حقيقية <em>مصممة لتعيش.</em>', 'مشاريع ودراسات حالة توضح كيف نحول التحديات إلى أنظمة وتجارب قابلة للتشغيل والنمو.');
?>
<section class="tyz-site-section"><div class="tyz-site-shell">
<div class="tyz-site-work-grid">
<?php if (have_posts()): while (have_posts()): the_post(); ?>
<article class="tyz-site-card tyz-site-card--work">
<?php if (has_post_thumbnail()): ?><a class="tyz-site-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a><?php endif; ?>
<small>CASE STUDY</small><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html(get_the_excerpt() ?: wp_trim_words(get_the_content(), 24)); ?></p><a class="tyz-site-link" href="<?php the_permalink(); ?>">عرض المشروع ←</a></article>
<?php endwhile; else: ?>
<div class="tyz-site-empty"><h2>دراسات الحالة قادمة.</h2><p>سنضيف مشاريع تميز الحقيقية مع الصور والتفاصيل والنتائج الموثقة.</p></div>
<?php endif; ?>
</div>
</div></section>
<?php tyz_site_v1_footer();