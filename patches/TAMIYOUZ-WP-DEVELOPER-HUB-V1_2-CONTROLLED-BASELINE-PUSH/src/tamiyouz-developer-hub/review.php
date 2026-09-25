<?php
if (!defined('ABSPATH') || !current_user_can('manage_options')) {
    exit;
}

// Marker: TAMIYOUZ-WP-DEVELOPER-HUB-V1_2-CONTROLLED-BASELINE-PUSH

if (!function_exists('tamiyouz_devhub_120_safe_path')) {
    function tamiyouz_devhub_120_safe_path($path) {
        $path = str_replace('\\', '/', ltrim((string) $path, '/'));
        if ($path === '' || strpos($path, '..') !== false) return false;
        if (preg_match('#(^|/)(\.env|uploads|cache|backups|storage)(/|$)#i', $path)) return false;
        if (preg_match('#(^|/)tamiyouz-developer-hub(?:\.php|/|$)#i', $path)) return false;
        if (!preg_match('#^wp-content/mu-plugins/tamiyouz-[A-Za-z0-9._/-]+$#', $path)) return false;
        return (bool) preg_match('#\.(php|css|js|json|md|txt|svg)$#i', $path);
    }
}

if (!function_exists('tamiyouz_devhub_120_blob_sha')) {
    function tamiyouz_devhub_120_blob_sha($data) {
        return sha1('blob ' . strlen($data) . chr(0) . $data);
    }
}

if (!function_exists('tamiyouz_devhub_120_scan')) {
    function tamiyouz_devhub_120_scan($absolute, $relative, &$files) {
        if (!file_exists($absolute) || is_link($absolute)) return;
        if (is_file($absolute)) {
            $logical = 'wp-content/mu-plugins/' . ltrim($relative, '/');
            if (!tamiyouz_devhub_120_safe_path($logical)) return;
            $size = filesize($absolute);
            if ($size === false || $size > 2000000) return;
            $data = file_get_contents($absolute);
            if ($data === false || strpos($data, chr(0)) !== false) return;
            $files[$logical] = array('sha' => tamiyouz_devhub_120_blob_sha($data), 'size' => strlen($data));
            return;
        }
        if (!is_dir($absolute)) return;
        $items = scandir($absolute);
        if (!is_array($items)) return;
        foreach ($items as $name) {
            if ($name === '.' || $name === '..') continue;
            tamiyouz_devhub_120_scan($absolute . '/' . $name, trim($relative . '/' . $name, '/'), $files);
        }
    }
}

if (!function_exists('tamiyouz_devhub_120_local')) {
    function tamiyouz_devhub_120_local() {
        $root = WP_CONTENT_DIR . '/mu-plugins';
        $files = array();
        $items = is_dir($root) ? scandir($root) : false;
        if (!is_array($items)) return $files;
        foreach ($items as $name) {
            if ($name === '.' || $name === '..') continue;
            if (strpos(strtolower($name), 'tamiyouz-') !== 0) continue;
            if (strpos(strtolower($name), 'tamiyouz-developer-hub') === 0) continue;
            tamiyouz_devhub_120_scan($root . '/' . $name, $name, $files);
        }
        ksort($files);
        return $files;
    }
}

if (!function_exists('tamiyouz_devhub_120_remote')) {
    function tamiyouz_devhub_120_remote($token, $repo, $branch) {
        if (!preg_match('#^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$#', $repo)) throw new Exception('Invalid repository.');
        $branch_meta = tamiyouz_devhub_111_github_request($token, 'GET', '/repos/' . $repo . '/branches/' . rawurlencode($branch), null, false);
        $head = isset($branch_meta['commit']['sha']) ? (string) $branch_meta['commit']['sha'] : '';
        if ($head === '') throw new Exception('GitHub branch HEAD missing.');
        $commit = tamiyouz_devhub_111_github_request($token, 'GET', '/repos/' . $repo . '/git/commits/' . $head, null, false);
        $tree_sha = isset($commit['tree']['sha']) ? (string) $commit['tree']['sha'] : '';
        if ($tree_sha === '') throw new Exception('GitHub tree SHA missing.');
        $tree = tamiyouz_devhub_111_github_request($token, 'GET', '/repos/' . $repo . '/git/trees/' . $tree_sha . '?recursive=1', null, false);
        if (!empty($tree['truncated'])) throw new Exception('GitHub tree is truncated; safe review stopped.');
        $files = array();
        $items = isset($tree['tree']) && is_array($tree['tree']) ? $tree['tree'] : array();
        foreach ($items as $item) {
            if (!isset($item['type']) || $item['type'] !== 'blob' || empty($item['path']) || empty($item['sha'])) continue;
            $repo_path = (string) $item['path'];
            if (strpos($repo_path, 'site/') !== 0) continue;
            $logical = substr($repo_path, 5);
            if (!tamiyouz_devhub_120_safe_path($logical)) continue;
            $files[$logical] = array('sha' => (string) $item['sha'], 'repo_path' => $repo_path);
        }
        ksort($files);
        return array('head' => $head, 'tree' => $tree_sha, 'files' => $files);
    }
}

if (!function_exists('tamiyouz_devhub_120_review')) {
    function tamiyouz_devhub_120_review($state, $action, $store) {
        if (!in_array($action, array('push', 'pull', 'sync'), true)) throw new Exception('Invalid review action.');
        if (empty($state['token']) || empty($state['repo']) || empty($state['branch'])) throw new Exception('Connect GitHub and save repository first.');
        $token = tamiyouz_devhub_103_decrypt($state['token']);
        $local = tamiyouz_devhub_120_local();
        $remote = tamiyouz_devhub_120_remote($token, $state['repo'], $state['branch']);
        $paths = array_unique(array_merge(array_keys($local), array_keys($remote['files'])));
        sort($paths);
        $counts = array('synced' => 0, 'local_only' => 0, 'remote_only' => 0, 'different' => 0);
        $rows = array();
        $blocked = array();
        $actionable = 0;
        foreach ($paths as $path) {
            $l = isset($local[$path]['sha']) ? $local[$path]['sha'] : null;
            $r = isset($remote['files'][$path]['sha']) ? $remote['files'][$path]['sha'] : null;
            if ($l !== null && $r !== null && hash_equals((string) $l, (string) $r)) $status = 'synced';
            elseif ($l !== null && $r === null) $status = 'local_only';
            elseif ($l === null && $r !== null) $status = 'remote_only';
            else $status = 'different';
            $counts[$status]++;
            if ($status === 'different') $blocked[] = $path;
            if ($action === 'push' && $status === 'local_only') $actionable++;
            if ($action === 'pull' && $status === 'remote_only') $actionable++;
            if ($action === 'sync' && ($status === 'local_only' || $status === 'remote_only')) $actionable++;
            $rows[] = array('path' => $path, 'status' => $status);
        }

        $local_manifest = array();
        foreach ($local as $path => $meta) $local_manifest[$path] = $meta['sha'];
        $remote_manifest = array();
        foreach ($remote['files'] as $path => $meta) $remote_manifest[$path] = $meta['sha'];

        $fingerprint = hash('sha256', wp_json_encode(array(
            $action,
            $state['repo'],
            $state['branch'],
            $remote['head'],
            $local_manifest,
            $remote_manifest
        )));

        if ($store && $action === 'push') {
            set_transient(
                'tamiyouz_devhub_120_push_' . get_current_user_id(),
                array(
                    'fingerprint' => $fingerprint,
                    'head' => $remote['head'],
                    'repo' => $state['repo'],
                    'branch' => $state['branch'],
                    'created_at' => time()
                ),
                600
            );
        }

        return array(
            'action' => $action,
            'head' => $remote['head'],
            'tree' => $remote['tree'],
            'counts' => $counts,
            'rows' => $rows,
            'blocked' => $blocked,
            'actionable' => $actionable,
            'fingerprint' => $fingerprint
        );
    }
}

if (!function_exists('tamiyouz_devhub_120_read_local')) {
    function tamiyouz_devhub_120_read_local($logical) {
        if (!tamiyouz_devhub_120_safe_path($logical)) throw new Exception('Unsafe local path.');
        $absolute = ABSPATH . $logical;
        if (!is_file($absolute) || is_link($absolute)) throw new Exception('Local source file is missing: ' . $logical);
        $size = filesize($absolute);
        if ($size === false || $size > 2000000) throw new Exception('Local source file is too large: ' . $logical);
        $data = file_get_contents($absolute);
        if ($data === false || strpos($data, chr(0)) !== false) throw new Exception('Local source file is not safe text: ' . $logical);
        return $data;
    }
}

if (!function_exists('tamiyouz_devhub_120_audit')) {
    function tamiyouz_devhub_120_audit($action, $result, $meta) {
        $key = 'tamiyouz_devhub_120_audit';
        $items = get_option($key, array());
        if (!is_array($items)) $items = array();
        array_unshift($items, array(
            'at' => gmdate('c'),
            'user' => wp_get_current_user()->user_login,
            'action' => $action,
            'result' => $result,
            'meta' => is_array($meta) ? $meta : array()
        ));
        update_option($key, array_slice($items, 0, 50), false);
    }
}

if (!function_exists('tamiyouz_devhub_120_execute_baseline_push')) {
    function tamiyouz_devhub_120_execute_baseline_push($state, $fingerprint) {
        $approval = get_transient('tamiyouz_devhub_120_push_' . get_current_user_id());
        if (!is_array($approval) || empty($approval['fingerprint']) || !hash_equals((string) $approval['fingerprint'], (string) $fingerprint)) {
            throw new Exception('Reviewed Push expired or does not match. Run Review Push again.');
        }
        if ($approval['repo'] !== $state['repo'] || $approval['branch'] !== $state['branch']) {
            throw new Exception('Repository or branch changed after review.');
        }

        $fresh = tamiyouz_devhub_120_review($state, 'push', false);
        if (!hash_equals((string) $fresh['fingerprint'], (string) $fingerprint)) {
            throw new Exception('Local or GitHub state changed after review. Run Review Push again.');
        }
        if (!empty($fresh['blocked'])) {
            throw new Exception('Push is blocked because managed files differ on both sides.');
        }
        if ((int) $fresh['actionable'] < 1) {
            throw new Exception('There are no local-only files to push.');
        }

        $token = tamiyouz_devhub_103_decrypt($state['token']);
        $repo = $state['repo'];
        $tree_entries = array();

        foreach ($fresh['rows'] as $row) {
            if ($row['status'] !== 'local_only') continue;
            $data = tamiyouz_devhub_120_read_local($row['path']);
            $blob = tamiyouz_devhub_111_github_request(
                $token,
                'POST',
                '/repos/' . $repo . '/git/blobs',
                array('content' => base64_encode($data), 'encoding' => 'base64'),
                false
            );
            if (empty($blob['sha'])) throw new Exception('GitHub blob creation failed for ' . $row['path']);
            $tree_entries[] = array(
                'path' => 'site/' . $row['path'],
                'mode' => '100644',
                'type' => 'blob',
                'sha' => $blob['sha']
            );
        }

        if (empty($tree_entries)) throw new Exception('No reviewed files were selected for Push.');

        $tree = tamiyouz_devhub_111_github_request(
            $token,
            'POST',
            '/repos/' . $repo . '/git/trees',
            array('base_tree' => $fresh['tree'], 'tree' => $tree_entries),
            false
        );
        if (empty($tree['sha'])) throw new Exception('GitHub tree creation failed.');

        $commit = tamiyouz_devhub_111_github_request(
            $token,
            'POST',
            '/repos/' . $repo . '/git/commits',
            array(
                'message' => 'Baseline Tamiyouz website source',
                'tree' => $tree['sha'],
                'parents' => array($fresh['head'])
            ),
            false
        );
        if (empty($commit['sha'])) throw new Exception('GitHub commit creation failed.');

        tamiyouz_devhub_111_github_request(
            $token,
            'PATCH',
            '/repos/' . $repo . '/git/refs/heads/' . rawurlencode($state['branch']),
            array('sha' => $commit['sha'], 'force' => false),
            false
        );

        $state['remote_head'] = sanitize_text_field($commit['sha']);
        $state['last_push_at'] = gmdate('c');
        update_option('tamiyouz_devhub_103_state', $state, false);
        delete_transient('tamiyouz_devhub_120_push_' . get_current_user_id());
        tamiyouz_devhub_120_audit('baseline_push', 'success', array('files' => count($tree_entries), 'commit' => $commit['sha']));

        return array('files' => count($tree_entries), 'commit' => $commit['sha']);
    }
}

$tyz120_review = null;
$tyz120_error = '';
$tyz120_notice = '';
$tyz120_state = tamiyouz_devhub_103_state();
$tyz120_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

if ($tyz120_method === 'POST') {
    $tyz120_action = isset($_POST['devhub_action']) ? sanitize_key(wp_unslash($_POST['devhub_action'])) : '';
    if (in_array($tyz120_action, array('review_push', 'review_pull', 'review_sync'), true)) {
        try {
            check_admin_referer('tamiyouz_devhub_103');
            $tyz120_review = tamiyouz_devhub_120_review($tyz120_state, substr($tyz120_action, 7), true);
            if ($tyz120_review['action'] === 'push') {
                tamiyouz_devhub_120_audit('review_push', 'success', array('actionable' => $tyz120_review['actionable'], 'blocked' => count($tyz120_review['blocked'])));
            }
        } catch (Exception $e) {
            $tyz120_error = $e->getMessage();
        }
    } elseif ($tyz120_action === 'execute_baseline_push') {
        try {
            check_admin_referer('tamiyouz_devhub_103');
            $fingerprint = isset($_POST['fingerprint']) ? sanitize_text_field(wp_unslash($_POST['fingerprint'])) : '';
            $result = tamiyouz_devhub_120_execute_baseline_push($tyz120_state, $fingerprint);
            $tyz120_notice = 'Baseline Push completed: ' . $result['files'] . ' files · ' . $result['commit'];
            $tyz120_state = tamiyouz_devhub_103_state();
        } catch (Exception $e) {
            $tyz120_error = $e->getMessage();
            tamiyouz_devhub_120_audit('baseline_push', 'failed', array('error' => $tyz120_error));
        }
    }
}
?>
<div class="wrap tyz-review120" dir="rtl">
    <div class="tyz120-head">
        <div>
            <h2>Synchronization Review</h2>
            <p>V1.2 — Review-first control. Baseline Push only can execute; Pull/Sync remain read-only.</p>
        </div>
        <span class="tyz120-badge ok">CONTROLLED PUSH ACTIVE</span>
    </div>

    <?php if ($tyz120_error !== '') : ?><div class="notice notice-error"><p><?php echo esc_html($tyz120_error); ?></p></div><?php endif; ?>
    <?php if ($tyz120_notice !== '') : ?><div class="notice notice-success"><p><?php echo esc_html($tyz120_notice); ?></p></div><?php endif; ?>

    <div class="tyz120-card">
        <div class="tyz120-actions">
            <form method="post"><?php wp_nonce_field('tamiyouz_devhub_103'); ?><input type="hidden" name="devhub_action" value="review_push"><button class="button">Review Push</button></form>
            <form method="post"><?php wp_nonce_field('tamiyouz_devhub_103'); ?><input type="hidden" name="devhub_action" value="review_pull"><button class="button">Review Pull</button></form>
            <form method="post"><?php wp_nonce_field('tamiyouz_devhub_103'); ?><input type="hidden" name="devhub_action" value="review_sync"><button class="button">Review Sync</button></form>
            <span class="tyz120-badge hold">PULL/SYNC EXECUTE DISABLED</span>
        </div>

        <p class="description">Remote scope: <code>site/wp-content/mu-plugins/tamiyouz-*</code>. Developer Hub files are excluded.</p>

        <?php if ($tyz120_review !== null) : ?>
            <div class="tyz120-summary">
                <strong><?php echo esc_html(strtoupper($tyz120_review['action'])); ?></strong>
                <span>Actionable: <?php echo esc_html((string) $tyz120_review['actionable']); ?></span>
                <span>Blocked: <?php echo esc_html((string) count($tyz120_review['blocked'])); ?></span>
                <code><?php echo esc_html($tyz120_review['head']); ?></code>
            </div>

            <div class="tyz120-stats">
                <?php foreach ($tyz120_review['counts'] as $name => $value) : ?>
                    <div><small><?php echo esc_html($name); ?></small><b><?php echo esc_html((string) $value); ?></b></div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($tyz120_review['blocked'])) : ?>
                <div class="tyz120-alert"><strong>Blocked:</strong> <?php echo esc_html(implode(', ', $tyz120_review['blocked'])); ?></div>
            <?php endif; ?>

            <div class="tyz120-table"><table class="widefat striped"><thead><tr><th>Direction</th><th>Status</th><th>File</th></tr></thead><tbody>
            <?php $shown = 0; foreach ($tyz120_review['rows'] as $row) : if ($row['status'] === 'synced') continue; $shown++; ?>
                <?php if ($row['status'] === 'local_only') { $dir = 'Local → GitHub'; $cls = 'local'; } elseif ($row['status'] === 'remote_only') { $dir = 'GitHub → Local'; $cls = 'remote'; } else { $dir = 'MANUAL'; $cls = 'bad'; } ?>
                <tr><td class="<?php echo esc_attr($cls); ?>"><?php echo esc_html($dir); ?></td><td><?php echo esc_html($row['status']); ?></td><td><code><?php echo esc_html($row['path']); ?></code></td></tr>
            <?php endforeach; if ($shown === 0) : ?><tr><td colspan="3">Managed source is synchronized.</td></tr><?php endif; ?>
            </tbody></table></div>

            <?php if ($tyz120_review['action'] === 'push' && empty($tyz120_review['blocked']) && (int) $tyz120_review['actionable'] > 0) : ?>
                <form method="post" class="tyz120-execute" onsubmit="return confirm('Push exactly these reviewed local-only files to GitHub?');">
                    <?php wp_nonce_field('tamiyouz_devhub_103'); ?>
                    <input type="hidden" name="devhub_action" value="execute_baseline_push">
                    <input type="hidden" name="fingerprint" value="<?php echo esc_attr($tyz120_review['fingerprint']); ?>">
                    <button class="button button-primary">Execute Reviewed Baseline Push</button>
                    <span>Approval expires in 10 minutes. Local/GitHub state is rechecked before commit.</span>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<style>
.tyz-review120{max-width:1180px}.tyz120-head,.tyz120-actions,.tyz120-summary,.tyz120-execute{display:flex;align-items:center;gap:12px;flex-wrap:wrap}.tyz120-head{justify-content:space-between;margin:16px 0}.tyz120-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:20px}.tyz120-actions form{margin:0}.tyz120-badge{padding:5px 9px;border-radius:999px;font-size:12px;font-weight:700}.tyz120-badge.ok{background:#ecfdf5;color:#047857}.tyz120-badge.hold{background:#fff7ed;color:#c2410c}.tyz120-summary{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px;margin:14px 0}.tyz120-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}.tyz120-stats>div{border:1px solid #e5e7eb;border-radius:10px;padding:10px}.tyz120-stats small{display:block;color:#64748b}.tyz120-stats b{font-size:18px}.tyz120-alert{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:10px;padding:10px;margin:12px 0}.tyz120-table{max-height:430px;overflow:auto;margin-top:12px}.tyz120-table .local{color:#0369a1;font-weight:700}.tyz120-table .remote{color:#7c3aed;font-weight:700}.tyz120-table .bad{color:#b91c1c;font-weight:800}.tyz120-execute{margin-top:16px;padding-top:16px;border-top:1px solid #e2e8f0}.tyz120-execute span{color:#64748b}@media(max-width:800px){.tyz120-stats{grid-template-columns:repeat(2,1fr)}}
</style>
