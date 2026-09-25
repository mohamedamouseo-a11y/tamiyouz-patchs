# TAMIYOUZ-WP-DEVELOPER-HUB-V1

TCRM-inspired GitHub control plane for the Tamiyouz WordPress site.

## V1
- WordPress Admin → **Developer Hub**
- GitHub PAT verification and encrypted storage
- repository + branch selection
- Review Push / Review Pull / Review Sync
- local ↔ GitHub file comparison
- 10-minute reviewed-operation fingerprint
- explicit approval before execution
- remote-head/local-state recheck before execution
- safe local backup before Pull/Sync writes
- audit log
- no force push
- no remote/local deletes in V1
- no theme / Elementor / DB-schema changes

## Managed code scope
V1 intentionally manages only Tamiyouz custom MU-plugin source under:

`wp-content/mu-plugins/tamiyouz-*`

The Developer Hub itself is excluded from its own synchronization scope.

Default GitHub repository prefix: `site/`.

Example remote path:

`site/wp-content/mu-plugins/tamiyouz-home-v1.php`

## Install

```bash
WP_ROOT=/path/to/wordpress bash apply.sh
```

For the current FTP workflow upload:

`src/tamiyouz-developer-hub.php`

as:

`wp-content/mu-plugins/tamiyouz-developer-hub.php`

Then open **WordPress Admin → Developer Hub**.

## GitHub token
Use a fine-grained PAT limited to the chosen website repository with Contents read/write and Metadata read.

## Safety
Push/Pull/Sync are review-first. A reviewed operation expires after 10 minutes and is rejected when local or GitHub state changes before execution.
