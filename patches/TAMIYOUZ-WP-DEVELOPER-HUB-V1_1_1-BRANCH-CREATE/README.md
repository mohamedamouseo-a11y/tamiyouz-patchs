# TAMIYOUZ-WP-DEVELOPER-HUB-V1_1_1-BRANCH-CREATE

Small safety-focused patch for the Tamiyouz Developer Hub.

Problem:
- A newly created/empty GitHub repository has no branch yet.
- "Verify & Save" returns "Branch not found".

Adds:
- explicit **Create Branch & Save** button.
- if the branch already exists, it is simply selected/saved.
- if the repository already has a default branch, the new branch is created from its HEAD.
- if the repository is empty, the Hub creates one initial empty Git commit and then creates the requested branch.
- stores the new remote HEAD in Developer Hub state.

Safety:
- requires GitHub push/Contents-write permission.
- branch creation happens only after the admin explicitly clicks Create Branch & Save.
- no force push.
- no file upload, Pull, Push, Sync, delete, theme, Elementor or DB-schema changes.
- V1.1 read-only Review remains unchanged.
