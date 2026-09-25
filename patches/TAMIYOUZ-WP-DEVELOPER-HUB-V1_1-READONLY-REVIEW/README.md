# TAMIYOUZ-WP-DEVELOPER-HUB-V1_1-READONLY-REVIEW

Safe V1.1 upgrade for the Tamiyouz WordPress Developer Hub.

Adds a TCRM-style read-only review layer while keeping execution disabled:

- Review Push
- Review Pull
- Review Sync
- local ↔ GitHub comparison
- changed-file table
- conservative conflict/different detection
- counts + actionable count
- remote HEAD display
- existing GitHub PAT/repository/branch verification preserved

## Safety

V1.1 contains no GitHub write request and no live file write operation.
It cannot push, pull, sync, delete, force-push, or change the homepage/theme/Elementor/DB schema.

Managed source scope is restricted to Tamiyouz custom MU-plugin code under wp-content/mu-plugins/tamiyouz-*, excluding the Developer Hub itself.

Remote repository scope is currently fixed to the site/ prefix.
