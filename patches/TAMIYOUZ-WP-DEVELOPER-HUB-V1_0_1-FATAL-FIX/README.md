# TAMIYOUZ-WP-DEVELOPER-HUB-V1_0_1-FATAL-FIX

Emergency hotfix for the WordPress critical error introduced by Developer Hub V1.

Fixes:
- PHP syntax error in execute() in_array call
- malformed GitHub permission ternary
- Throwable exception method calls
- directory scan typo
- responsive CSS typo

Deployment: overwrite `wp-content/mu-plugins/tamiyouz-developer-hub.php` with `src/tamiyouz-developer-hub.php`.

No homepage, theme, Elementor, or DB-schema changes.
