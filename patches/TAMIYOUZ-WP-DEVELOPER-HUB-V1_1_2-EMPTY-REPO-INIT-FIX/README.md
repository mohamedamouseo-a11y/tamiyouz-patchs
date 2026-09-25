# TAMIYOUZ-WP-DEVELOPER-HUB-V1_1_2-EMPTY-REPO-INIT-FIX

Fixes the "Git Repository is empty" error when creating the first branch from Developer Hub.

GitHub's low-level Git Database endpoints return HTTP 409 for an empty repository. V1.1.2 initializes an empty repository through the Contents API first, then resolves the default branch HEAD and creates the requested branch when needed.

Behavior:
- existing branch: save it
- non-empty repo + missing branch: create from default branch HEAD
- empty repo: create a minimal .gitkeep commit through Contents API
- if requested branch is the default branch, initialization completes it
- otherwise create requested branch from the new default branch HEAD

Safety:
- requires explicit Create Branch & Save click
- requires Contents write permission
- no force push
- no website source Push/Pull/Sync
- no local source writes
- no theme/Elementor/DB-schema changes
