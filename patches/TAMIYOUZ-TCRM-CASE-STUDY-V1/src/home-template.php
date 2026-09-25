<?php
/**
 * Tamiyouz Homepage V2.1 — Light Editorial Preview
 * Marker: TAMIYOUZ-HOMEPAGE-BRAND-LOCK-V2_3 | TAMIYOUZ-SERVICES-CONTENT-V1 | TAMIYOUZ-TCRM-CASE-STUDY-V1
 */
if (!defined('ABSPATH')) { exit; }

$hero_video = get_option('tamiyouz_home_v2_hero_video', '');
$site_name  = get_bloginfo('name') ?: 'Tamiyouz';
$brand_logo = function_exists('tamiyouz_home_v21_brand_asset_url') ? tamiyouz_home_v21_brand_asset_url() : '';
?>
<!doctype html>
<html <?php language_attributes(); ?> dir="rtl" data-tyz-theme="light">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fbfaf7">
    <script>
    (function(){
        try {
            var saved = localStorage.getItem('tamiyouz-theme');
            document.documentElement.setAttribute('data-tyz-theme', saved === 'dark' ? 'dark' : 'light');
        } catch(e) {
            document.documentElement.setAttribute('data-tyz-theme', 'light');
        }
    })();
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="tyz-home-v21">
<header class="tyz-header" data-tyz-header>
    <div class="tyz-shell tyz-header__inner">
        <a class="tyz-brand tyz-brand--official" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($site_name); ?>">
            <?php if ($brand_logo) : ?><img class="tyz-brand__logo" src="<?php echo esc_url($brand_logo); ?>" alt="تميز"><?php endif; ?>
            <span class="tyz-brand__word">تميز<small>TAMIYOUZ</small></span>
        </a>

        <nav class="tyz-nav" data-tyz-nav aria-label="القائمة الرئيسية">
            <a href="#about">من نحن</a>
            <a href="#services">خدماتنا</a>
            <a href="#ai">الذكاء الاصطناعي</a>
            <a href="#work">أعمالنا</a>
            <a href="#process">كيف نعمل</a>
            <a href="#contact">تواصل معنا</a>
        </nav>

        <div class="tyz-header__actions">
            <button class="tyz-theme-toggle" type="button" aria-label="تبديل المظهر" data-tyz-theme-toggle><span>☀</span><b>◐</b></button>
            <a class="tyz-button tyz-button--small" href="#contact">ابدأ الآن <span>←</span></a>
            <button class="tyz-menu" type="button" aria-label="فتح القائمة" aria-expanded="false" data-tyz-menu><span></span><span></span></button>
        </div>
    </div>
</header>

<main>
<section class="tyz-hero">
    <div class="tyz-shell tyz-hero__grid">
        <div class="tyz-hero__copy tyz-reveal">
            <span class="tyz-eyebrow">AI · SOFTWARE · DIGITAL GROWTH</span>
            <h1>نبني نموًا رقميًا<br><em>بذكاء.</em></h1>
            <p>نصمم حلولًا رقمية متكاملة تجمع البرمجيات والذكاء الاصطناعي والتسويق، لتمنح أعمالك تجربة أوضح ونظامًا أسرع ونموًا قابلًا للتوسع.</p>
            <div class="tyz-hero__actions">
                <a class="tyz-button" href="#contact">ابدأ مشروعك الآن <span>←</span></a>
                <a class="tyz-text-link" href="#showreel">اكتشف كيف نعمل <span>↙</span></a>
            </div>
            <div class="tyz-hero__meta">
                <div><b>استراتيجية واضحة</b><small>نبدأ من الهدف الحقيقي</small></div>
                <div><b>تنفيذ متكامل</b><small>تقنية وتجربة وتشغيل</small></div>
                <div><b>تحسين مستمر</b><small>قرارات مبنية على البيانات</small></div>
            </div>
        </div>

        <div class="tyz-hero__visual tyz-reveal">
            <div class="tyz-hero__media">
                <?php if ($hero_video) : ?>
                    <video class="tyz-hero__video" autoplay muted loop playsinline preload="metadata"><source src="<?php echo esc_url($hero_video); ?>"></video>
                <?php else : ?>
                    <canvas id="tyz-ai-canvas" class="tyz-ai-canvas" aria-hidden="true"></canvas>
                <?php endif; ?>
                <div class="tyz-hero__veil"></div>
                <div class="tyz-hero__window">
                    <div class="tyz-windowbar"><i></i><i></i><i></i><span>TAMIYOUZ OPERATING SYSTEM</span></div>
                    <div class="tyz-dashboard">
                        <aside><b></b><span></span><span></span><span></span><span></span></aside>
                        <div>
                            <div class="tyz-dashboard__cards"><i></i><i></i><i></i></div>
                            <div class="tyz-dashboard__chart"><span></span></div>
                        </div>
                    </div>
                </div>
                <div class="tyz-float tyz-float--one"><small>AI ASSISTANT</small><strong>كيف يمكنني مساعدتك اليوم؟</strong></div>
                <div class="tyz-float tyz-float--two"><small>AUTOMATION</small><strong>من البيانات إلى إجراء</strong></div>
            </div>
        </div>
    </div>

    <div class="tyz-capabilities">
        <div class="tyz-shell tyz-capabilities__inner">
            <span>AI SOLUTIONS</span><i></i><span>SOFTWARE ENGINEERING</span><i></i><span>CRM & AUTOMATION</span><i></i><span>DIGITAL GROWTH</span><i></i><span>DATA & INSIGHTS</span>
        </div>
    </div>
</section>

<section class="tyz-about" id="about">
    <div class="tyz-shell tyz-about__grid">
        <div class="tyz-about__copy">
            <span class="tyz-eyebrow tyz-reveal">من نحن</span>
            <h2 class="tyz-reveal">لسنا مجرد وكالة رقمية.<br><em>نصمم أنظمة نمو.</em></h2>
            <p class="tyz-reveal">نربط الاستراتيجية بالتجربة والتقنية والتنفيذ. من الواجهة التي يراها عميلك إلى الأنظمة والأتمتة والبيانات التي تدير العمل خلف الكواليس.</p>
            <div class="tyz-about__points tyz-reveal">
                <div><b>حلول مخصصة</b><small>حول طريقة عملك الفعلية</small></div>
                <div><b>فريق متعدد الخبرات</b><small>تقنية وتسويق وتجربة</small></div>
                <div><b>منظومة واحدة</b><small>بدل أدوات متفرقة</small></div>
            </div>
        </div>
        <div class="tyz-about__visual tyz-reveal">
            <div class="tyz-office-card">
                <div class="tyz-office-card__screen"><span></span><b></b><b></b><b></b></div>
                <div class="tyz-office-card__badge"><small>GROWTH SIGNAL</small><strong>Insight → Action</strong></div>
            </div>
        </div>
    </div>
</section>

<section class="tyz-services" id="services">
    <div class="tyz-shell">
        <div class="tyz-section-head">
            <div><span class="tyz-eyebrow tyz-reveal">خدماتنا</span><h2 class="tyz-reveal">حلول متكاملة <em>لنمو أعمالك.</em></h2></div>
            <p class="tyz-reveal">بدل التعامل مع أجزاء منفصلة، نبني منظومة رقمية مترابطة تخدم العميل والفريق والنمو في نفس الوقت.</p>
        </div>
        <div class="tyz-service-grid">
            <article class="tyz-service tyz-reveal"><span>01</span><i>AI</i><h3>حلول الذكاء الاصطناعي</h3><p>وكلاء أذكياء، بحث معرفي وأتمتة تساعد فريقك على العمل بذكاء أكبر.</p><a href="<?php echo esc_url(home_url('/services/ai-solutions/')); ?>">اكتشف المزيد ←</a></article>
            <article class="tyz-service tyz-reveal"><span>02</span><i>SW</i><h3>البرمجيات والأنظمة</h3><p>أنظمة مخصصة، CRM ومنصات تشغيل مبنية حول احتياج عملك.</p><a href="<?php echo esc_url(home_url('/services/custom-software/')); ?>">اكتشف المزيد ←</a></article>
            <article class="tyz-service tyz-reveal"><span>03</span><i>DX</i><h3>التجارب الرقمية</h3><p>مواقع ومنتجات رقمية تجمع الوضوح بالأداء والتحويل.</p><a href="<?php echo esc_url(home_url('/services/web-design-development/')); ?>">اكتشف المزيد ←</a></article>
            <article class="tyz-service tyz-reveal"><span>04</span><i>GR</i><h3>التسويق والنمو</h3><p>استراتيجية وأداء وقياس موحد يساعدك على اتخاذ قرارات أفضل.</p><a href="<?php echo esc_url(home_url('/services/performance-marketing/')); ?>">اكتشف المزيد ←</a></article>
            <article class="tyz-service tyz-reveal"><span>05</span><i>AT</i><h3>الأتمتة والتكاملات</h3><p>ربط الأدوات والعمليات لتقليل العمل المتكرر ورفع الكفاءة.</p><a href="<?php echo esc_url(home_url('/services/automation-integrations/')); ?>">اكتشف المزيد ←</a></article>
            <article class="tyz-service tyz-reveal"><span>06</span><i>DA</i><h3>البيانات والتحليلات</h3><p>لوحات وتقارير تربط الأداء بالتشغيل وتوضح أين تتحرك بعد ذلك.</p><a href="<?php echo esc_url(home_url('/services/data-analytics/')); ?>">اكتشف المزيد ←</a></article>
        </div>
    </div>
</section>

<section class="tyz-showreel" id="showreel">
    <div class="tyz-shell">
        <div class="tyz-showreel__panel tyz-reveal">
            <div class="tyz-showreel__copy"><span>MORE THAN A DIGITAL AGENCY</span><h2>من الفكرة إلى نظام<br><em>يعمل كل يوم.</em></h2><p>نحوّل الرؤية إلى تجربة ومنظومة قابلة للتشغيل والقياس والتطوير.</p></div>
            <a href="#ai" class="tyz-play" aria-label="استكشف طبقة الذكاء"><b>▶</b><small>EXPLORE THE SYSTEM</small></a>
        </div>
    </div>
</section>

<section class="tyz-ai" id="ai">
    <div class="tyz-shell tyz-ai__grid">
        <div class="tyz-ai__copy tyz-reveal">
            <span class="tyz-eyebrow">AI · OPERATING LAYER</span>
            <h2>الذكاء الاصطناعي ليس إضافة.<br><em>هو طبقة تشغيل.</em></h2>
            <p>نضع الذكاء الاصطناعي داخل سير العمل نفسه: معرفة، متابعة، تحليل وأتمتة مرتبطة بالبيانات والعمليات الفعلية.</p>
            <ul><li>وكلاء أذكياء داخل العمليات</li><li>تحليل وفرز المعرفة والبيانات</li><li>أتمتة القرارات المتكررة</li><li>تكامل مع الأنظمة الحالية</li></ul>
        </div>
        <div class="tyz-console tyz-reveal">
            <div class="tyz-console__top"><b>TAMIYOUZ AI</b><span>● SYSTEM ONLINE</span></div>
            <div class="tyz-console__body">
                <aside><b>Overview</b><span>Agents</span><span>Knowledge</span><span>Automations</span><span>Analytics</span></aside>
                <div class="tyz-console__main">
                    <div class="tyz-mini-cards"><div><small>Agents</small><b>06</b></div><div><small>Automations</small><b>24</b></div><div><small>Knowledge</small><b>∞</b></div></div>
                    <div class="tyz-prompt"><small>اسأل منظومة العمل</small><strong>ما الفرص التي تحتاج متابعة اليوم؟</strong></div>
                    <div class="tyz-chart"><span></span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="tyz-work" id="work">
    <div class="tyz-shell">
        <div class="tyz-section-head">
            <div><span class="tyz-eyebrow tyz-reveal">أعمالنا</span><h2 class="tyz-reveal">أعمال رقمية <em>مصممة لتعيش.</em></h2></div>
            <p class="tyz-reveal">نماذج توضح كيف نجمع بين المنتج والتشغيل والذكاء والنمو داخل تجربة واحدة متماسكة.</p>
        </div>
        <div class="tyz-work-grid">
            <article class="tyz-work-card tyz-work-card--wide tyz-reveal">
                <div class="tyz-work-card__visual tyz-work-card__visual--crm"><div class="tyz-crm-ui"><aside></aside><main><i></i><i></i><i></i><b></b></main></div></div>
                <div class="tyz-work-card__copy"><small>BUSINESS SOFTWARE · TCRM</small><h3>منظومة CRM وتشغيل مخصصة</h3><p>واجهة واحدة للعملاء والمهام والمتابعة والأتمتة والبيانات.</p><a class="tyz-text-link" href="<?php echo esc_url(home_url('/work/tcrm/')); ?>">عرض دراسة الحالة <span>←</span></a></div>
            </article>
            <article class="tyz-work-card tyz-reveal"><div class="tyz-work-card__visual tyz-work-card__visual--ai"><div class="tyz-ai-sphere"></div></div><div class="tyz-work-card__copy"><small>INTELLIGENT PRODUCT</small><h3>مساعد ذكي للفريق</h3><p>معرفة وتنفيذ أسرع داخل تجربة تشغيل يومية واضحة.</p></div></article>
            <article class="tyz-work-card tyz-reveal"><div class="tyz-work-card__visual tyz-work-card__visual--growth"><div class="tyz-growth-bars"><i></i><i></i><i></i><i></i><i></i></div></div><div class="tyz-work-card__copy"><small>DIGITAL GROWTH</small><h3>تجربة رقمية تقود للتحويل</h3><p>رحلة عميل مترابطة من الزيارة إلى الإجراء والقياس.</p></div></article>
        </div>
    </div>
</section>

<section class="tyz-process" id="process">
    <div class="tyz-shell">
        <div class="tyz-process__intro"><span class="tyz-eyebrow tyz-reveal">كيف نعمل</span><h2 class="tyz-reveal">رحلة واضحة من التحدي <em>إلى نتيجة قابلة للقياس.</em></h2></div>
        <div class="tyz-process__steps">
            <article class="tyz-reveal"><span>01</span><h3>فهم</h3><p>نفهم العمل والمستخدم والمشكلة قبل اختيار الحل.</p></article>
            <article class="tyz-reveal"><span>02</span><h3>تصميم</h3><p>نحوّل الرؤية إلى تجربة وتدفق ونموذج قابل للاختبار.</p></article>
            <article class="tyz-reveal"><span>03</span><h3>تنفيذ</h3><p>نبني بجودة عالية وبنية واضحة قابلة للتطوير.</p></article>
            <article class="tyz-reveal"><span>04</span><h3>تحسين</h3><p>نقيس ونتعلم ونطوّر باستمرار بعد الإطلاق.</p></article>
        </div>
    </div>
</section>

<section class="tyz-cta" id="contact">
    <div class="tyz-shell">
        <div class="tyz-cta__panel tyz-reveal">
            <div><span>READY WHEN YOU ARE</span><h2>لديك فكرة أو تحدٍ؟<br><em>خلينا نبني شيئًا مميزًا.</em></h2><p>ابدأ من هدف واضح، ونحن نحوله إلى تجربة ونظام قابل للتشغيل والنمو.</p></div>
            <a class="tyz-button tyz-button--light" href="mailto:<?php echo esc_attr(sanitize_email(get_option('admin_email'))); ?>">تواصل معنا <span>←</span></a>
        </div>
    </div>
</section>
</main>

<footer class="tyz-footer">
    <div class="tyz-shell tyz-footer__inner">
        <div class="tyz-footer__brand">
            <a class="tyz-brand tyz-brand--official" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($site_name); ?>">
                <?php if ($brand_logo) : ?><img class="tyz-brand__logo" src="<?php echo esc_url($brand_logo); ?>" alt="تميز"><?php endif; ?>
                <span class="tyz-brand__word">تميز<small>TAMIYOUZ</small></span>
            </a>
            <p>AI · SOFTWARE · DIGITAL GROWTH</p>
        </div>
        <div class="tyz-footer__links"><a href="#services">الخدمات</a><a href="#ai">الذكاء الاصطناعي</a><a href="#work">أعمالنا</a><a href="#contact">تواصل معنا</a></div>
        <small>© <?php echo esc_html(date('Y')); ?> Tamiyouz</small>
    </div>
</footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
