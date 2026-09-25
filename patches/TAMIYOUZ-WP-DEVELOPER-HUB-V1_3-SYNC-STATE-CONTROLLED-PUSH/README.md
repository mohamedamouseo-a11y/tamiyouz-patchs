# TAMIYOUZ-WP-DEVELOPER-HUB-V1_3-SYNC-STATE-CONTROLLED-PUSH

V1.3 converts the successful first Baseline Push into a persistent three-way sync state.

## What it adds
- persistent base manifest after the repository and live managed files are confirmed identical
- statuses: synced, local_only, remote_only, local_change, remote_change, local_deleted, remote_deleted, conflict
- controlled Push for local_only + local_change
- strict blocking on remote drift, deletes, or conflict
- 10-minute reviewed fingerprint
- immediate recheck of local + baseline + GitHub state before every Push
- non-force GitHub ref update
- baseline refresh after a successful Push
- audit state

## Still disabled
- Pull execution
- Sync execution
- deletes
- force push
- Developer Hub self-sync

The first V1.3 Review on the already-synchronized site automatically adopts the identical Local/GitHub state as the baseline.
