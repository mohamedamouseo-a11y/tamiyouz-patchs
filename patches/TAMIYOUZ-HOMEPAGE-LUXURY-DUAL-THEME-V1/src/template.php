<?php
/**
 * Tamiyouz Homepage Luxury V1
 * Marker: TAMIYOUZ-HOMEPAGE-LUXURY-DUAL-THEME-V1
 */
if (!defined('ABSPATH')) { exit; }

$hero_video = get_option('tamiyouz_home_v1_hero_video', '');
$site_name  = get_bloginfo('name') ?: 'Tamiyouz';
?>
<!doctype html>
<html <?php language_attributes(); ?> dir="rtl" data-tyz-theme="light">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f4ed">
    <script>
      (function () {
        try {
          var saved = localStorage.getItem('tamiyouz-theme');
          var preferred = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
          document.documentElement.setAttribute('data-tyz-theme', preferred);
        } catch (e) {}
      })();
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="tyz-home-v1">
    <header class="tyz-header" data-tyz-header>
        <div class="tyz-shell tyz-header__inner">
            <a class="tyz-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($site_name); ?>">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <span class="tyz-brand__mark">ت</span>
                    <span class="tyz-brand__word">تميز<small>Tamiyouz</small></span>
                <?php endif; ?>
            </a>

            <nav class="tyz-nav" data-tyz-nav aria-label="القائمة الرئيسية">
                <a href="#about">من نحن</a>
                <a href="#services">خدماتنا</a>
                <a href="#ai">الذكاء الاصطناعي</a>
                <a href="#work">ما نبنيه</a>
                <a href="#process">كيف نعمل</a>
                <a href="#contact">تواصل معنا</a>
            </nav>

            <div class="tyz-header__actions">
                <button class="tyz-theme-toggle" type="button" aria-label="تبديل المظهر" title="Light / Dark" data-tyz-theme-toggle>
                    <span class="tyz-theme-toggle__sun">☀</span>
                    <span class="tyz-theme-toggle__moon">◐</span>
                </button>
                <a class="tyz-button tyz-button--small" href="#contact">ابدأ مشروعك <span>↗</span></a>
                <button class="tyz-menu" type="button" aria-label="فتح القائمة" aria-expanded="false" data-tyz-menu>
                    <span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <main>
        <section class="tyz-hero">
            <div class="tyz-shell tyz-hero__grid">
                <div class="tyz-hero__visual tyz-reveal">
                    <div class="tyz-hero__media">
                        <?php if ($hero_video) : ?>
                            <video class="tyz-hero__video" autoplay muted loop playsinline preload="metadata">
                                <source src="<?php echo esc_url($hero_video); ?>">
                            </video>
                        <?php else : ?>
                            <canvas id="tyz-ai-canvas" class="tyz-ai-canvas" aria-hidden="true"></canvas>
                        <?php endif; ?>
                        <div class="tyz-hero__veil"></div>
                        <div class="tyz-hero__gridlines"></div>

                        <div class="tyz-float tyz-float--one">
                            <small>AI OPERATING LAYER</small>
                            <strong>Agents + Automation</strong>
                            <span>ذكاء يعمل داخل منظومة شركتك</span>
                        </div>

                        <div class="tyz-float tyz-float--two">
                            <small>SOFTWARE SYSTEMS</small>
                            <strong>Built around your workflow</strong>
                        </div>

                        <div class="tyz-float tyz-float--three">
                            <small>GROWTH SIGNAL</small>
                            <strong>Data → Action</strong>
                        </div>

                        <div class="tyz-hero__badge">
                            <span></span>
                            DIGITAL SYSTEMS FOR REAL GROWTH
                        </div>
                    </div>
                </div>

                <div class="tyz-hero__copy">
                    <div class="tyz-kicker tyz-reveal"><i></i> AI · SOFTWARE · DIGITAL GROWTH</div>
                    <h1 class="tyz-reveal">
                        نبني نموًا رقميًا
                        <em>بذكاء.</em>
                    </h1>
                    <p class="tyz-hero__lead tyz-reveal">
                        في تميز نجمع بين الذكاء الاصطناعي، تطوير البرمجيات والتسويق الرقمي
                        لبناء تجارب وأنظمة أكثر ذكاءً، أسرع تنفيذًا وأسهل في التوسع.
                    </p>
                    <div class="tyz-hero__actions tyz-reveal">
                        <a class="tyz-button" href="#contact">ابدأ مشروعك الآن <span>←</span></a>
                        <a class="tyz-text-link" href="#showreel">شاهد الفكرة <span>↘</span></a>
                    </div>
                    <div class="tyz-hero__meta tyz-reveal">
                        <div><span>01</span><b>نفهم</b><small>الهدف والتحدي الحقيقي</small></div>
                        <div><span>02</span><b>نبني</b><small>تجربة ونظامًا متكاملًا</small></div>
                        <div><span>03</span><b>نحسن</b><small>بالبيانات والتعلم المستمر</small></div>
                    </div>
                </div>
            </div>

            <div class="tyz-capabilities" aria-label="قدرات تميز">
                <div class="tyz-capabilities__track">
                    <span>AI SOLUTIONS</span><i></i>
                    <span>SOFTWARE ENGINEERING</span><i></i>
                    <span>CRM & AUTOMATION</span><i></i>
                    <span>GROWTH SYSTEMS</span><i></i>
                    <span>DIGITAL EXPERIENCES</span><i></i>
                    <span>DATA & INSIGHTS</span><i></i>
                    <span>AI SOLUTIONS</span><i></i>
                    <span>SOFTWARE ENGINEERING</span><i></i>
                </div>
            </div>
        </section>

        <section class="tyz-statement" id="about">
            <div class="tyz-shell tyz-statement__grid">
                <div class="tyz-statement__aside tyz-reveal">
                    <span>WHY TAMIYOUZ</span>
                    <p>حلول مصممة حول طريقة عملك، لا حول قالب جاهز.</p>
                </div>
                <div class="tyz-statement__main">
                    <h2 class="tyz-display tyz-reveal">لسنا مجرد وكالة رقمية.<br><em>نصمم أنظمة نمو.</em></h2>
                    <p class="tyz-reveal">
                        نربط الاستراتيجية والتجربة والتقنية في مسار واحد. من الواجهة التي يراها عميلك
                        إلى الأتمتة والبيانات والبرمجيات التي تدير العمل خلف الكواليس.
                    </p>
                </div>
            </div>
        </section>

        <section class="tyz-services" id="services">
            <div class="tyz-shell">
                <div class="tyz-section-head">
                    <div>
                        <span class="tyz-eyebrow tyz-reveal">01 — WHAT WE DO</span>
                        <h2 class="tyz-reveal">حلول متكاملة <em>لنمو أعمالك.</em></h2>
                    </div>
                    <p class="tyz-reveal">بدل التعامل مع أجزاء منفصلة، نبني منظومة رقمية متماسكة تخدم التسويق والعمليات والعميل في نفس الوقت.</p>
                </div>

                <div class="tyz-service-grid">
                    <article class="tyz-service tyz-reveal">
                        <span class="tyz-service__index">01</span>
                        <div class="tyz-service__icon">AI</div>
                        <h3>حلول الذكاء الاصطناعي</h3>
                        <p>وكلاء AI، مساعدين، بحث معرفي، تحليل ذكي وتدفقات عمل مدعومة بالذكاء الاصطناعي.</p>
                        <small>AGENTS · RAG · INTELLIGENCE</small>
                    </article>
                    <article class="tyz-service tyz-reveal">
                        <span class="tyz-service__index">02</span>
                        <div class="tyz-service__icon">⌘</div>
                        <h3>البرمجيات والأنظمة</h3>
                        <p>منصات وأدوات أعمال وCRM وأنظمة داخلية مخصصة حول طريقة عمل فريقك.</p>
                        <small>WEB APPS · CRM · SYSTEMS</small>
                    </article>
                    <article class="tyz-service tyz-reveal">
                        <span class="tyz-service__index">03</span>
                        <div class="tyz-service__icon">↗</div>
                        <h3>التسويق والنمو</h3>
                        <p>استراتيجية وحملات ومحتوى وتحسين مستمر يربط النشاط التسويقي بنتائج واضحة.</p>
                        <small>GROWTH · PERFORMANCE · CONTENT</small>
                    </article>
                    <article class="tyz-service tyz-reveal">
                        <span class="tyz-service__index">04</span>
                        <div class="tyz-service__icon">◫</div>
                        <h3>المواقع والتجارب الرقمية</h3>
                        <p>واجهات ومواقع وصفحات هبوط سريعة، راقية وواضحة، مصممة للتحويل والاستخدام الحقيقي.</p>
                        <small>UX/UI · WEB · EXPERIENCE</small>
                    </article>
                    <article class="tyz-service tyz-reveal">
                        <span class="tyz-service__index">05</span>
                        <div class="tyz-service__icon">∞</div>
                        <h3>الأتمتة والتكاملات</h3>
                        <p>ربط الأنظمة وتدفقات المتابعة والإشعارات والعمليات المتكررة بطريقة موثوقة وقابلة للتوسع.</p>
                        <small>AUTOMATION · APIs · WORKFLOWS</small>
                    </article>
                    <article class="tyz-service tyz-reveal">
                        <span class="tyz-service__index">06</span>
                        <div class="tyz-service__icon">◇</div>
                        <h3>الهوية والمحتوى</h3>
                        <p>نظام بصري ورسائل ومحتوى يخلق حضورًا متماسكًا وواضحًا في كل نقطة تواصل.</p>
                        <small>BRAND · CREATIVE · CONTENT</small>
                    </article>
                </div>
            </div>
        </section>

        <section class="tyz-showreel" id="showreel">
            <div class="tyz-shell">
                <div class="tyz-showreel__frame tyz-reveal">
                    <?php if ($hero_video) : ?>
                        <video class="tyz-showreel__video" controls playsinline preload="metadata">
                            <source src="<?php echo esc_url($hero_video); ?>">
                        </video>
                    <?php else : ?>
                        <div class="tyz-showreel__fallback">
                            <div class="tyz-showreel__ring"></div>
                            <span>AI × SOFTWARE × GROWTH</span>
                        </div>
                    <?php endif; ?>
                    <div class="tyz-showreel__copy">
                        <span>THE TAMIYOUZ APPROACH</span>
                        <strong>من الفكرة إلى نظام يعمل كل يوم.</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="tyz-ai" id="ai">
            <div class="tyz-shell tyz-ai__grid">
                <div class="tyz-ai__copy">
                    <span class="tyz-eyebrow tyz-reveal">02 — AI × SOFTWARE</span>
                    <h2 class="tyz-reveal">الذكاء الاصطناعي ليس إضافة.<br><em>هو طبقة تشغيل.</em></h2>
                    <p class="tyz-reveal">
                        ندمج AI داخل سير العمل ليقرأ السياق، يساعد الفريق، يختصر الوقت وينفذ المهام
                        ضمن قواعد وصلاحيات واضحة.
                    </p>
                    <div class="tyz-ai__list tyz-reveal">
                        <span>وكلاء ومساعدون مخصصون</span>
                        <span>بحث داخل معرفة الشركة</span>
                        <span>أتمتة المهام والإجراءات</span>
                        <span>لوحات تنبيه وتحليل ذكية</span>
                    </div>
                </div>

                <div class="tyz-console tyz-reveal">
                    <div class="tyz-console__top">
                        <div><i></i><b>TAMIYOUZ AI</b></div>
                        <span>● SYSTEM ONLINE</span>
                    </div>
                    <div class="tyz-console__body">
                        <aside>
                            <b>Overview</b>
                            <span>Agents</span>
                            <span>Knowledge</span>
                            <span>Automations</span>
                            <span>Analytics</span>
                        </aside>
                        <div class="tyz-console__main">
                            <div class="tyz-prompt">
                                <small>اسأل منظومة العمل</small>
                                <strong>ما أهم الفرص التي تحتاج متابعة اليوم؟</strong>
                                <span>⌘ ↵</span>
                            </div>
                            <div class="tyz-mini-cards">
                                <div><small>Agents</small><b>06</b><span>جاهز</span></div>
                                <div><small>Automations</small><b>24</b><span>نشطة</span></div>
                                <div><small>Knowledge</small><b>∞</b><span>مترابطة</span></div>
                            </div>
                            <div class="tyz-chart">
                                <div class="tyz-chart__head"><span>Operational intelligence</span><b>LIVE</b></div>
                                <svg viewBox="0 0 680 190" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="tyzChartFill" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#c89b4c" stop-opacity=".33"/>
                                            <stop offset="100%" stop-color="#c89b4c" stop-opacity="0"/>
                                        </linearGradient>
                                    </defs>
                                    <path class="tyz-chart-area" d="M0 168 C70 150 78 112 145 128 S243 132 300 84 S393 111 455 62 S552 89 680 26 L680 190 L0 190 Z"/>
                                    <path class="tyz-chart-line" d="M0 168 C70 150 78 112 145 128 S243 132 300 84 S393 111 455 62 S552 89 680 26"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="tyz-work" id="work">
            <div class="tyz-shell">
                <div class="tyz-section-head">
                    <div>
                        <span class="tyz-eyebrow tyz-reveal">03 — WHAT WE BUILD</span>
                        <h2 class="tyz-reveal">أعمال رقمية <em>مصممة لتعيش.</em></h2>
                    </div>
                    <p class="tyz-reveal">نماذج لأنواع المنتجات والأنظمة التي يمكننا بناؤها وتطويرها حسب احتياج العمل.</p>
                </div>

                <div class="tyz-work-grid">
                    <article class="tyz-work-card tyz-work-card--wide tyz-reveal">
                        <div class="tyz-work-card__visual tyz-work-card__visual--crm">
                            <span>CRM / OPERATIONS</span>
                            <div class="tyz-window">
                                <i></i><i></i><i></i>
                                <b></b><b></b><b></b>
                            </div>
                        </div>
                        <div class="tyz-work-card__copy">
                            <small>BUSINESS SOFTWARE</small>
                            <h3>منظومة CRM وتشغيل مخصصة</h3>
                            <p>تجمع العملاء والمهام والمتابعة والأتمتة والبيانات في تجربة واحدة.</p>
                        </div>
                    </article>

                    <article class="tyz-work-card tyz-reveal">
                        <div class="tyz-work-card__visual tyz-work-card__visual--ai">
                            <span>AI ASSISTANT</span>
                            <div class="tyz-ai-sphere"></div>
                        </div>
                        <div class="tyz-work-card__copy">
                            <small>INTELLIGENT PRODUCT</small>
                            <h3>مساعد ذكي للفريق</h3>
                            <p>يبحث في المعرفة ويساعد في التنفيذ والقرارات اليومية.</p>
                        </div>
                    </article>

                    <article class="tyz-work-card tyz-reveal">
                        <div class="tyz-work-card__visual tyz-work-card__visual--growth">
                            <span>GROWTH EXPERIENCE</span>
                            <div class="tyz-growth-bars"><i></i><i></i><i></i><i></i><i></i></div>
                        </div>
                        <div class="tyz-work-card__copy">
                            <small>DIGITAL GROWTH</small>
                            <h3>تجربة رقمية تقود للتحويل</h3>
                            <p>موقع وحملات وقياس موحد حول رحلة عميل واضحة.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="tyz-process" id="process">
            <div class="tyz-shell">
                <div class="tyz-process__intro">
                    <span class="tyz-eyebrow tyz-reveal">04 — HOW WE WORK</span>
                    <h2 class="tyz-reveal">رحلة واضحة من التحدي <em>إلى نتيجة قابلة للقياس.</em></h2>
                </div>
                <div class="tyz-process__steps">
                    <article class="tyz-reveal"><span>01</span><h3>فهم</h3><p>نفهم العمل، المستخدم والمشكلة قبل القفز للحل.</p></article>
                    <article class="tyz-reveal"><span>02</span><h3>تصميم</h3><p>نحوّل الرؤية إلى تجربة وتدفق عمل ونموذج قابل للاختبار.</p></article>
                    <article class="tyz-reveal"><span>03</span><h3>تنفيذ</h3><p>نبني بجودة عالية وبنية واضحة قابلة للتطوير.</p></article>
                    <article class="tyz-reveal"><span>04</span><h3>تحسين</h3><p>نقيس، نتعلم ونطور باستمرار بعد الإطلاق.</p></article>
                </div>
            </div>
        </section>

        <section class="tyz-cta" id="contact">
            <div class="tyz-shell">
                <div class="tyz-cta__panel tyz-reveal">
                    <div>
                        <span>READY WHEN YOU ARE</span>
                        <h2>لديك فكرة أو تحدٍ؟<br><em>خلينا نبني شيئًا مميزًا.</em></h2>
                    </div>
                    <a class="tyz-button tyz-button--light" href="mailto:<?php echo esc_attr(sanitize_email(get_option('admin_email'))); ?>">
                        تواصل معنا
                        <span>↗</span>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="tyz-footer">
        <div class="tyz-shell tyz-footer__inner">
            <div class="tyz-footer__brand">
                <a class="tyz-brand" href="<?php echo esc_url(home_url('/')); ?>">
                    <?php if (has_custom_logo()) { the_custom_logo(); } else { echo '<span class="tyz-brand__mark">ت</span><span class="tyz-brand__word">تميز<small>Tamiyouz</small></span>'; } ?>
                </a>
                <p>AI · Software · Digital Growth</p>
            </div>
            <div class="tyz-footer__links">
                <a href="#services">الخدمات</a>
                <a href="#ai">الذكاء الاصطناعي</a>
                <a href="#work">ما نبنيه</a>
                <a href="#contact">تواصل معنا</a>
            </div>
            <small>© <?php echo esc_html(date('Y')); ?> Tamiyouz. All rights reserved.</small>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
