<?php
/**
 * Plugin Name: Tamiyouz Homepage Theme Toggle Hard Fix V2.9.6
 * Description: Deterministic front-page Light/Dark toggle independent of duplicated/broken legacy listeners.
 * Version: 2.9.6
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-HOMEPAGE-THEME-TOGGLE-HARD-FIX-V2_9_6
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_footer', function () {
    if (!is_front_page()) { return; }
    ?>
    <script id="tamiyouz-theme-toggle-hard-fix-v2-9-6">
    (function(){
      var root = document.documentElement;
      var meta = document.querySelector('meta[name="theme-color"]');

      function applyTheme(theme, persist) {
        theme = theme === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-tyz-theme', theme);

        if (meta) {
          meta.setAttribute('content', theme === 'dark' ? '#0d0e10' : '#fbfaf7');
        }

        if (persist !== false) {
          try { localStorage.setItem('tamiyouz-theme', theme); } catch (e) {}
        }

        try {
          window.dispatchEvent(new CustomEvent('tamiyouz:v21theme', { detail: theme }));
        } catch (e) {}
      }

      // Reconcile initial state once at footer time.
      try {
        var saved = localStorage.getItem('tamiyouz-theme');
        if (saved === 'light' || saved === 'dark') {
          applyTheme(saved, false);
        }
      } catch (e) {}

      // Capture phase makes this deterministic even if old/duplicate bubble listeners exist.
      document.addEventListener('click', function(e){
        var target = e.target && e.target.closest ? e.target.closest('[data-tyz-theme-toggle]') : null;
        if (!target) { return; }

        e.preventDefault();
        e.stopImmediatePropagation();

        var current = root.getAttribute('data-tyz-theme') === 'dark' ? 'dark' : 'light';
        var next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next, true);

        target.setAttribute('aria-pressed', next === 'dark' ? 'true' : 'false');
      }, true);

      window.tamiyouzSetThemeV296 = applyTheme;
    })();
    </script>
    <?php
}, PHP_INT_MAX);
