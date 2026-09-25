<?php
require __DIR__ . '/common.php';
the_post();
tyz_site_v1_header('CASE STUDY', get_the_title(), get_the_excerpt() ?: 'دراسة حالة لمشروع حقيقي من تميز.');
?>
<?php if (has_post_thumbnail()): ?><div class="tyz-site-shell tyz-site-feature"><?php the_post_thumbnail('full'); ?></div><?php endif; ?>
<article class="tyz-site-article"><div class="tyz-site-shell tyz-site-article__body"><?php the_content(); ?></div></article>
<section class="tyz-site-cta"><div class="tyz-site-shell"><div><span>BUILD WITH TAMIYOUZ</span><h2>عايز نبني لك حاجة مشابهة؟</h2></div><a href="<?php echo esc_url(home_url('/#contact')); ?>">تواصل معنا ←</a></div></section>
<?php tyz_site_v1_footer();