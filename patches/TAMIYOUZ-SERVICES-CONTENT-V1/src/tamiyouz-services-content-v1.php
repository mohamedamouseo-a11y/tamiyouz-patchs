<?php
/**
 * Plugin Name: Tamiyouz Services Content V1
 * Description: Initial SEO-ready service pages and service schema for Tamiyouz.
 * Version: 1.0.0
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-SERVICES-CONTENT-V1
 */
if (!defined('ABSPATH')) { exit; }

function tyz_services_v1_content($slug) {
    $items = array(
        'ai-solutions' => array(
            'title' => 'حلول الذكاء الاصطناعي للأعمال',
            'excerpt' => 'نصمم حلول ذكاء اصطناعي عملية تربط المعرفة والبيانات وسير العمل لتساعد الفرق على التنفيذ بشكل أسرع وأكثر اتساقًا.',
            'order' => 10,
            'content' => '<div class="tyz-service-copy">
<section class="tyz-content-lead"><h2>ذكاء اصطناعي داخل العمل، وليس مجرد إضافة.</h2><p>نبني حلولًا تساعد فريقك على الوصول للمعلومة، تنفيذ المهام المتكررة، تحليل البيانات، وتحويل المعرفة الموجودة داخل الشركة إلى تجربة تشغيل يومية أكثر كفاءة.</p></section>
<section class="tyz-content-block"><h2>ما الذي يمكن أن نبنيه؟</h2><div class="tyz-content-cards"><div><h3>AI Agents</h3><p>وكلاء أذكياء لتنفيذ خطوات واضحة داخل المبيعات وخدمة العملاء والتشغيل.</p></div><div><h3>Knowledge AI</h3><p>بحث ذكي داخل المستندات والسياسات والمعلومات الداخلية بدل البحث اليدوي.</p></div><div><h3>AI Automation</h3><p>ربط الذكاء الاصطناعي بالأنظمة وسير العمل لتنفيذ إجراءات حقيقية.</p></div></div></section>
<section class="tyz-content-block"><h2>كيف نبدأ؟</h2><p>نحدد نقطة الاحتكاك أو المهمة المتكررة، نراجع مصادر البيانات، نبني نموذجًا عمليًا، ثم نقيس أثره قبل التوسع.</p></section>
<section class="tyz-content-links"><a href="/work/">شاهد أعمالنا ←</a><a href="/blog/">اقرأ مقالات الذكاء الاصطناعي ←</a></section>
</div>'
        ),
        'custom-software' => array(
            'title' => 'تطوير البرمجيات والأنظمة المخصصة',
            'excerpt' => 'نطور أنظمة أعمال مخصصة ومنصات تشغيل ولوحات إدارة مبنية حول طريقة العمل الفعلية داخل شركتك.',
            'order' => 20,
            'content' => '<div class="tyz-service-copy">
<section class="tyz-content-lead"><h2>النظام يتكيّف مع عملك، وليس العكس.</h2><p>عندما تصبح الأدوات الجاهزة عائقًا، نبني نظامًا مخصصًا يجمع العمليات والبيانات والفرق في تجربة واحدة واضحة وقابلة للتوسع.</p></section>
<section class="tyz-content-block"><h2>حلول برمجية للأعمال</h2><div class="tyz-content-cards"><div><h3>Business Systems</h3><p>أنظمة تشغيل داخلية لإدارة المهام والعمليات والموافقات والمتابعة.</p></div><div><h3>CRM Platforms</h3><p>إدارة العملاء والفرص والعقود والمتابعة داخل دورة مبيعات مترابطة.</p></div><div><h3>Portals & Dashboards</h3><p>بوابات ولوحات تحكم تعرض المعلومة المناسبة لكل مستخدم.</p></div></div></section>
<section class="tyz-content-block"><h2>من الاكتشاف إلى الإطلاق</h2><p>نبدأ بفهم سير العمل، نحوله إلى Scope واضح، ثم نطور على مراحل مع اختبارات وتحقق قبل التوسع.</p></section>
<section class="tyz-content-links"><a href="/work/">شاهد دراسات الحالة ←</a><a href="/services/crm-sales-automation/">CRM وأتمتة المبيعات ←</a></section>
</div>'
        ),
        'crm-sales-automation' => array(
            'title' => 'CRM وأتمتة المبيعات',
            'excerpt' => 'نبني ونطور أنظمة CRM وسير متابعة يساعد فرق المبيعات على إدارة العملاء والفرص والمتابعة والقياس من مكان واحد.',
            'order' => 30,
            'content' => '<div class="tyz-service-copy">
<section class="tyz-content-lead"><h2>من أول Lead إلى العميل، كل خطوة واضحة.</h2><p>نربط التقاط العملاء المحتملين بالمتابعة والمهام والتنبيهات والتقارير حتى يعرف كل شخص ما المطلوب وما الخطوة التالية.</p></section>
<section class="tyz-content-block"><h2>ما الذي يشمله الحل؟</h2><div class="tyz-content-cards"><div><h3>Lead Management</h3><p>تجميع وتنظيم وتصنيف العملاء المحتملين ومصادرهم.</p></div><div><h3>Sales Pipeline</h3><p>مراحل وفرص ومهام ومتابعات واضحة لكل صفقة.</p></div><div><h3>Follow-up Automation</h3><p>تنبيهات وقواعد وأتمتة تقلل فرص نسيان المتابعة.</p></div></div></section>
<section class="tyz-content-block"><h2>القياس جزء من النظام.</h2><p>نجهز لوحات متابعة تساعد الإدارة على فهم سرعة الاستجابة، حركة الفرص، مصادر العملاء، وأداء دورة المبيعات.</p></section>
<section class="tyz-content-links"><a href="/work/">مشاريع CRM من تميز ←</a><a href="/blog/">مقالات المبيعات وCRM ←</a></section>
</div>'
        ),
        'web-design-development' => array(
            'title' => 'تصميم وتطوير المواقع والتجارب الرقمية',
            'excerpt' => 'نصمم ونطور مواقع وصفحات هبوط ومنتجات رقمية سريعة وواضحة ومهيأة للتحويل ومحركات البحث.',
            'order' => 40,
            'content' => '<div class="tyz-service-copy">
<section class="tyz-content-lead"><h2>تجربة رقمية تعبر عن العلامة وتخدم هدف العمل.</h2><p>نجمع بين UX/UI والأداء والمحتوى والتحويل حتى لا يكون الموقع مجرد واجهة جميلة، بل قناة نمو قابلة للقياس.</p></section>
<section class="tyz-content-block"><h2>ما الذي نهتم به؟</h2><div class="tyz-content-cards"><div><h3>UX / UI</h3><p>هيكل واضح وتجربة استخدام متناسقة عبر الأجهزة.</p></div><div><h3>Performance</h3><p>تحميل سريع وصور وفيديوهات محسنة وتجربة مستقرة.</p></div><div><h3>Conversion</h3><p>CTA ومسارات واضحة تربط الزيارة بالفعل المطلوب.</p></div></div></section>
<section class="tyz-content-block"><h2>جاهز للإعلانات وSEO.</h2><p>نبني الصفحات من البداية على أساس واضح للعناوين والروابط والـmetadata والقياس حتى تكون مناسبة للنمو العضوي والحملات المدفوعة.</p></section>
<section class="tyz-content-links"><a href="/services/seo-organic-growth/">SEO والنمو العضوي ←</a><a href="/services/performance-marketing/">الإعلانات والأداء ←</a></section>
</div>'
        ),
        'seo-organic-growth' => array(
            'title' => 'SEO والنمو العضوي',
            'excerpt' => 'نطور بنية ومحتوى SEO يساعد موقعك على الظهور للكلمات التي يبحث عنها عملاؤك وبناء نمو عضوي مستمر.',
            'order' => 50,
            'content' => '<div class="tyz-service-copy">
<section class="tyz-content-lead"><h2>SEO يبدأ من بنية الموقع قبل كتابة المقالات.</h2><p>نربط Technical SEO بهيكل الصفحات والمحتوى والروابط الداخلية والقياس، ثم نبني Content Clusters حول احتياجات البحث الحقيقية.</p></section>
<section class="tyz-content-block"><h2>منظومة SEO متكاملة</h2><div class="tyz-content-cards"><div><h3>Technical SEO</h3><p>الفهرسة والـsitemap والـcanonical والأداء والبيانات المنظمة.</p></div><div><h3>Content Strategy</h3><p>صفحات خدمات ومقالات مترابطة حسب نية البحث.</p></div><div><h3>Search Performance</h3><p>متابعة الظهور والنقرات والاستعلامات وتحسين الصفحات بناءً على البيانات.</p></div></div></section>
<section class="tyz-content-block"><h2>محتوى يبني Authority.</h2><p>بدل نشر مقالات منفصلة، نبني موضوعات مترابطة تربط المعرفة بالخدمة المناسبة ودراسات الحالة الحقيقية.</p></section>
<section class="tyz-content-links"><a href="/blog/">مقالات تميز ←</a><a href="/work/">دراسات الحالة ←</a></section>
</div>'
        ),
        'performance-marketing' => array(
            'title' => 'Google Ads والتسويق بالأداء',
            'excerpt' => 'نربط الإعلانات وصفحات الهبوط والقياس لتحويل الميزانية إلى مسار واضح من النقر إلى العميل المحتمل.',
            'order' => 60,
            'content' => '<div class="tyz-service-copy">
<section class="tyz-content-lead"><h2>الحملة لا تنتهي عند الإعلان.</h2><p>نجمع بين نية البحث، الإعلان، Landing Page، القياس، وتحسين التحويل حتى نستطيع فهم ما يعمل فعليًا وما يحتاج تعديلًا.</p></section>
<section class="tyz-content-block"><h2>من الإعلان إلى التحويل</h2><div class="tyz-content-cards"><div><h3>Campaign Structure</h3><p>تقسيم الحملات والكلمات والإعلانات حسب الهدف ونية العميل.</p></div><div><h3>Landing Pages</h3><p>صفحات مخصصة للرسالة والنية بدل إرسال كل الزيارات للصفحة الرئيسية.</p></div><div><h3>Measurement</h3><p>قياس الإجراءات المهمة وربط النتائج بمصدر الزيارة.</p></div></div></section>
<section class="tyz-content-block"><h2>تحسين مستمر.</h2><p>نراجع الاستعلامات والصفحات ومعدلات التحويل والبيانات الفعلية ثم نختبر التحسينات بدل الاعتماد على التخمين.</p></section>
<section class="tyz-content-links"><a href="/services/web-design-development/">صفحات الهبوط والتطوير ←</a><a href="/services/data-analytics/">البيانات والتحليلات ←</a></section>
</div>'
        ),
        'automation-integrations' => array(
            'title' => 'الأتمتة والتكاملات بين الأنظمة',
            'excerpt' => 'نربط الأدوات والأنظمة وسير العمل لتقليل الخطوات اليدوية وتحسين سرعة انتقال البيانات والمهام بين الفرق.',
            'order' => 70,
            'content' => '<div class="tyz-service-copy">
<section class="tyz-content-lead"><h2>خلّي الأنظمة تتكلم مع بعض.</h2><p>نحدد نقاط النقل اليدوي والتكرار ثم نربط التطبيقات والبيانات عبر APIs وWebhooks وقواعد تشغيل واضحة.</p></section>
<section class="tyz-content-block"><h2>أمثلة على الأتمتة</h2><div class="tyz-content-cards"><div><h3>Lead Routing</h3><p>توجيه العملاء والمهام تلقائيًا حسب القواعد.</p></div><div><h3>System Sync</h3><p>مزامنة البيانات بين CRM وأدوات التشغيل والخدمات الأخرى.</p></div><div><h3>Notifications</h3><p>إشعارات تلقائية مرتبطة بالحالات والمواعيد والتغييرات المهمة.</p></div></div></section>
<section class="tyz-content-block"><h2>أتمتة قابلة للمراقبة.</h2><p>نصمم التدفقات بحيث يمكن تتبعها ومعرفة أين نجحت وأين تحتاج تدخلًا بدل إنشاء عمليات غامضة يصعب صيانتها.</p></section>
<section class="tyz-content-links"><a href="/services/ai-solutions/">AI Automation ←</a><a href="/services/custom-software/">الأنظمة المخصصة ←</a></section>
</div>'
        ),
        'data-analytics' => array(
            'title' => 'البيانات والتحليلات ولوحات الأداء',
            'excerpt' => 'نوحد البيانات المهمة في لوحات وتقارير تساعد الإدارة والفرق على اتخاذ قرارات أسرع وأكثر وضوحًا.',
            'order' => 80,
            'content' => '<div class="tyz-service-copy">
<section class="tyz-content-lead"><h2>البيانات المفيدة هي التي تقود قرارًا.</h2><p>نرتب مصادر البيانات ونحدد المقاييس المهمة ثم نحولها إلى لوحات وتقارير مفهومة بدل أرقام متفرقة يصعب استخدامها.</p></section>
<section class="tyz-content-block"><h2>من البيانات إلى الرؤية</h2><div class="tyz-content-cards"><div><h3>Dashboards</h3><p>لوحات تشغيل وإدارة تعرض أهم المؤشرات في مكان واحد.</p></div><div><h3>Tracking</h3><p>قياس الأحداث والتحويلات وربطها بمصدرها.</p></div><div><h3>Decision Support</h3><p>مؤشرات واتجاهات تساعد على تحديد الأولويات والفرص.</p></div></div></section>
<section class="tyz-content-block"><h2>نربط التسويق بالتشغيل.</h2><p>عندما تتصل بيانات الحملات والمبيعات والتشغيل، يصبح من الأسهل معرفة أين تأتي الفرص وأين يحدث التسرب.</p></section>
<section class="tyz-content-links"><a href="/services/performance-marketing/">التسويق بالأداء ←</a><a href="/services/crm-sales-automation/">CRM والمبيعات ←</a></section>
</div>'
        ),
    );
    return isset($items[$slug]) ? $items[$slug] : null;
}

add_action('init', function () {
    if (!post_type_exists('tyz_service')) return;
    $version = '1.0.0';
    if (get_option('tamiyouz_services_content_v1') === $version) return;

    $slugs = array(
        'ai-solutions',
        'custom-software',
        'crm-sales-automation',
        'web-design-development',
        'seo-organic-growth',
        'performance-marketing',
        'automation-integrations',
        'data-analytics',
    );

    foreach ($slugs as $slug) {
        $data = tyz_services_v1_content($slug);
        if (!$data) continue;

        $existing = get_page_by_path($slug, OBJECT, 'tyz_service');
        if ($existing) {
            if (!get_post_meta($existing->ID, '_tyz_seeded_services_v1', true)) continue;
            $post_id = wp_update_post(wp_slash(array(
                'ID' => $existing->ID,
                'post_title' => $data['title'],
                'post_excerpt' => $data['excerpt'],
                'post_content' => $data['content'],
                'menu_order' => $data['order'],
                'post_status' => 'publish',
            )), true);
        } else {
            $post_id = wp_insert_post(wp_slash(array(
                'post_type' => 'tyz_service',
                'post_name' => $slug,
                'post_title' => $data['title'],
                'post_excerpt' => $data['excerpt'],
                'post_content' => $data['content'],
                'menu_order' => $data['order'],
                'post_status' => 'publish',
                'comment_status' => 'closed',
            )), true);
        }

        if (!is_wp_error($post_id) && $post_id) {
            update_post_meta($post_id, '_tyz_seeded_services_v1', '1');
        }
    }

    update_option('tamiyouz_services_content_v1', $version, false);
}, 30);

add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) return;
    if ($query->is_post_type_archive('tyz_service')) {
        $query->set('posts_per_page', 20);
        $query->set('orderby', array('menu_order' => 'ASC', 'date' => 'ASC'));
    }
});

add_action('wp_head', function () {
    if (!is_singular('tyz_service')) return;
    $post = get_queried_object();
    if (!$post || empty($post->ID)) return;

    $description = has_excerpt($post) ? get_the_excerpt($post) : wp_strip_all_tags(wp_trim_words($post->post_content, 28, '…'));
    $schema = array(
        '@context' => 'https://schema.org',
        '@graph' => array(
            array(
                '@type' => 'Service',
                '@id' => get_permalink($post) . '#service',
                'name' => get_the_title($post),
                'description' => $description,
                'url' => get_permalink($post),
                'provider' => array(
                    '@type' => 'Organization',
                    'name' => 'Tamiyouz',
                    'url' => home_url('/'),
                ),
            ),
            array(
                '@type' => 'BreadcrumbList',
                'itemListElement' => array(
                    array('@type'=>'ListItem','position'=>1,'name'=>'تميز','item'=>home_url('/')),
                    array('@type'=>'ListItem','position'=>2,'name'=>'الخدمات','item'=>home_url('/services/')),
                    array('@type'=>'ListItem','position'=>3,'name'=>get_the_title($post),'item'=>get_permalink($post)),
                ),
            ),
        ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}, 25);
