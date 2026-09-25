<?php
require __DIR__ . '/common.php';
tyz_site_v1_header('INSIGHTS · BLOG', 'محتوى عملي <em>يبني معرفة ونموًا.</em>', 'مقالات عن المبيعات وCRM والذكاء الاصطناعي والأتمتة والنمو الرقمي، مبنية على خبرة عملية.');
$paged = max(1, get_query_var('paged'));
$q = new WP_Query(array('post_type'=>'post','post_status'=>'publish','paged'=>$paged,'posts_per_page'=>9));
?>
<section class="tyz-site-section"><div class="tyz-site-shell">
<div class="tyz-site-grid">
<?php if ($q->have_posts()): while ($q->have_posts()): $q->the_post(); ?>
<article class="tyz-site-card">
<?php if (has_post_thumbnail()): ?><a class="tyz-site-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a><?php endif; ?>
<small><?php echo esc_html(get_the_date('Y-m-d')); ?></small><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html(get_the_excerpt() ?: wp_trim_words(get_the_content(), 26)); ?></p><a class="tyz-site-link" href="<?php the_permalink(); ?>">اقرأ المقال ←</a>
</article>
<?php endwhile; wp_reset_postdata(); else: ?>
<div class="tyz-site-empty"><h2>المقالات قادمة.</h2><p>سنبدأ قريبًا بنشر المحتوى المتخصص.</p></div>
<?php endif; ?>
</div>
</div></section>
<?php tyz_site_v1_footer();