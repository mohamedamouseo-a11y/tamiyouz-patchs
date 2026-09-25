# TAMIYOUZ-WP-DEVELOPER-HUB-V1_2-CONTROLLED-BASELINE-PUSH

V1.2 enables the first controlled GitHub source write after a successful read-only review.

## Enabled
- Review Push / Pull / Sync
- exact Local ↔ GitHub comparison
- 10-minute reviewed Push fingerprint
- local + remote state recheck immediately before execution
- baseline Push of **local_only** managed files only
- GitHub non-force branch ref update
- small audit log

## Still disabled
- Pull execution
- Sync execution
- remote deletes
- local deletes
- force push
- Developer Hub self-sync

## Scope
Local managed files:
`wp-content/mu-plugins/tamiyouz-*`

Remote paths:
`site/wp-content/mu-plugins/tamiyouz-*`

The Developer Hub itself is excluded.

No homepage/theme/Elementor/DB-schema changes.
