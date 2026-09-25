# TAMIYOUZ-WP-DEVELOPER-HUB-V1_0_3-SAFE-BOOTSTRAP

Safe recovery build after V1/V1.0.1/V1.0.2 caused fatal errors on the live WordPress runtime.

This build intentionally separates a tiny MU-plugin bootstrap from the heavier admin module so wp-login/wp-admin bootstrap stays safe.

Includes:
- Developer Hub admin menu
- GitHub PAT verification and encrypted storage
- repository + branch verification
- remote HEAD refresh
- write/sync actions intentionally disabled until runtime stability is confirmed

Safety:
- no homepage/theme/Elementor/DB-schema changes
- no GitHub push/pull/sync in this recovery build
- no shell requirement
- no modern PHP-only syntax in the bootstrap

Both PHP files were linted successfully with PHP 8.4 before commit.
