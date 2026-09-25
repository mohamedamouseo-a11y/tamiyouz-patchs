<?php
if (!defined('ABSPATH') || !current_user_can('manage_options')) {
    exit;
}

// Marker: TAMIYOUZ-WP-DEVELOPER-HUB-V1_4-BASELINE-REAL-PUSH-PROGRESS

if (!function_exists('tamiyouz_devhub_130_safe_path')) {
    function tamiyouz_devhub_130_safe_path($path) {
        $path = str_replace('\\', '/', ltrim((string) $path, '/'));
        if ($path === '' || strpos($path, '..') !== false) return false;
        if (preg_match('#(^|/)(\.env|uploads|cache|backups|storage)(/|$)#i', $path)) return false;
        if (preg_match('#(^|/)tamiyouz-developer-hub(?:\.php|/|$)#i', $path)) return false;
        if (!preg_match('#^wp-content/mu-plugins/tamiyouz-[A-Za-z0-9._/-]+$#', $path)) return false;
        return (bool) preg_match('#\.(php|css|js|json|md|txt|svg)$#i', $path);
    }
}

if (!function_exists('tamiyouz_devhub_130_blob_sha')) {
    function tamiyouz_devhub_130_blob_sha($data) {
        return sha1('blob ' . strlen($data) . chr(0) . $data);
    }
}

if (!function_exists('tamiyouz_devhub_130_scan')) {
    function tamiyouz_devhub_130_scan($absolute, $relative, &$files) {
        if (!file_exists($absolute) || is_link($absolute)) return;
        if (is_file($absolute)) {
            $logical = 'wp-content/mu-plugins/' . ltrim($relative, '/');
            if (!tamiyouz_devhub_130_safe_path($logical)) return;
            $size = filesize($absolute);
            if ($size === false || $size > 2000000) return;
            $data = file_get_contents($absolute);
            if ($data === false || strpos($data, chr(0)) !== false) return;
            $files[$logical] = array('sha' => tamiyouz_devhub_130_blob_sha($data), 'size' => strlen($data));
            return;
        }
        if (!is_dir($absolute)) return;
        $items = scandir($absolute);
        if (!is_array($items)) return;
        foreach ($items as $name) {
            if ($name === '.' || $name === '..') continue;
            tamiyouz_devhub_130_scan($absolute . '/' . $name, trim($relative . '/' . $name, '/'), $files);
        }
    }
}

if (!function_exists('tamiyouz_devhub_130_local')) {
    function tamiyouz_devhub_130_local() {
        $root = WP_CONTENT_DIR . '/mu-plugins';
        $files = array();
        $items = is_dir($root) ? scandir($root) : false;
        if (!is_array($items)) return $files;
        foreach ($items as $name) {
            if ($name === '.' || $name === '..') continue;
            if (strpos(strtolower($name), 'tamiyouz-') !== 0) continue;
            if (strpos(strtolower($name), 'tamiyouz-developer-hub') === 0) continue;
            tamiyouz_devhub_130_scan($root . '/' . $name, $name, $files);
        }
        ksort($files);
        return $files;
    }
}

if (!function_exists('tamiyouz_devhub_130_remote')) {
    function tamiyouz_devhub_130_remote($token, $repo, $branch) {
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
            if (!tamiyouz_devhub_130_safe_path($logical)) continue;
            $files[$logical] = array('sha' => (string) $item['sha'], 'repo_path' => $repo_path);
        }
        ksort($files);
        return array('head' => $head, 'tree' => $tree_sha, 'files' => $files);
    }
}

if (!function_exists('tamiyouz_devhub_130_manifest')) {
    function tamiyouz_devhub_130_manifest($files) {
        $out = array();
        foreach ($files as $path => $meta) {
            $out[$path] = isset($meta['sha']) ? $meta['sha'] : $meta;
        }
        ksort($out);
        return $out;
    }
}

if (!function_exists('tamiyouz_devhub_130_save_baseline')) {
    function tamiyouz_devhub_130_save_baseline($state, $remote) {
        $state['base_manifest'] = tamiyouz_devhub_130_manifest($remote['files']);
        $state['baseline_at'] = gmdate('c');
        $state['remote_head'] = $remote['head'];
        update_option('tamiyouz_devhub_103_state', $state, false);
        return $state;
    }
}

if (!function_exists('tamiyouz_devhub_130_review')) {
    function tamiyouz_devhub_130_review($state, $action, $store) {
        if (!in_array($action, array('push', 'pull', 'sync'), true)) throw new Exception('Invalid review action.');
        if (empty($state['token']) || empty($state['repo']) || empty($state['branch'])) throw new Exception('Connect GitHub and save repository first.');

        $token = tamiyouz_devhub_103_decrypt($state['token']);
        $local = tamiyouz_devhub_130_local();
        $remote = tamiyouz_devhub_130_remote($token, $state['repo'], $state['branch']);
        $base = isset($state['base_manifest']) && is_array($state['base_manifest']) ? $state['base_manifest'] : array();

        if (empty($base)) {
            /*
             * V1.4 safe baseline bootstrap:
             * Remote managed files may be an exact matching subset of local files.
             * This safely supports the common case where new local files were added
             * after the last GitHub baseline but before V1.3 established base_manifest.
             * Any mismatch or remote-only managed file prevents adoption.
             */
            $can_adopt = count($remote['files']) > 0;
            if ($can_adopt) {
                foreach ($remote['files'] as $path => $meta) {
                    if (!isset($local[$path]['sha']) || !hash_equals((string) $local[$path]['sha'], (string) $meta['sha'])) {
                        $can_adopt = false;
                        break;
                    }
                }
            }
            if ($can_adopt) {
                $state = tamiyouz_devhub_130_save_baseline($state, $remote);
                $base = $state['base_manifest'];
            }
        }

        $paths = array_unique(array_merge(array_keys($local), array_keys($remote['files']), array_keys($base)));
        sort($paths);

        $counts = array(
            'synced' => 0,
            'local_only' => 0,
            'remote_only' => 0,
            'local_change' => 0,
            'remote_change' => 0,
            'local_deleted' => 0,
            'remote_deleted' => 0,
            'conflict' => 0
        );
        $rows = array();
        $blocked = array();
        $actionable = 0;

        foreach ($paths as $path) {
            $l = isset($local[$path]['sha']) ? $local[$path]['sha'] : null;
            $r = isset($remote['files'][$path]['sha']) ? $remote['files'][$path]['sha'] : null;
            $b = isset($base[$path]) ? $base[$path] : null;

            if ($l !== null && $r !== null && hash_equals((string) $l, (string) $r)) {
                $status = 'synced';
            } elseif ($b === null) {
                if ($l !== null && $r === null) $status = 'local_only';
                elseif ($l === null && $r !== null) $status = 'remote_only';
                else $status = 'conflict';
            } else {
                if ($l === null && $r !== null && hash_equals((string) $r, (string) $b)) $status = 'local_deleted';
                elseif ($r === null && $l !== null && hash_equals((string) $l, (string) $b)) $status = 'remote_deleted';
                elseif ($l !== null && hash_equals((string) $r, (string) $b) && !hash_equals((string) $l, (string) $b)) $status = 'local_change';
                elseif ($r !== null && hash_equals((string) $l, (string) $b) && !hash_equals((string) $r, (string) $b)) $status = 'remote_change';
                else $status = 'conflict';
            }

            if (!isset($counts[$status])) $counts[$status] = 0;
            $counts[$status]++;

            if ($action === 'push') {
                if ($status === 'local_only' || $status === 'local_change') {
                    $actionable++;
                } elseif ($status !== 'synced') {
                    $blocked[] = $path . ' [' . $status . ']';
                }
            }

            $rows[] = array('path' => $path, 'status' => $status);
        }

        $local_manifest = tamiyouz_devhub_130_manifest($local);
        $remote_manifest = tamiyouz_devhub_130_manifest($remote['files']);
        $fingerprint = hash('sha256', wp_json_encode(array(
            $action, $state['repo'], $state['branch'], $remote['head'], $base, $local_manifest, $remote_manifest
        )));

        if ($store && $action === 'push' && empty($blocked) && $actionable > 0) {
            set_transient(
                'tamiyouz_devhub_130_push_' . get_current_user_id(),
                array(
                    'fingerprint' => $fingerprint,
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
            'fingerprint' => $fingerprint,
            'baseline_ready' => !empty($base)
        );
    }
}

if (!function_exists('tamiyouz_devhub_130_read_local')) {
    function tamiyouz_devhub_130_read_local($logical) {
        if (!tamiyouz_devhub_130_safe_path($logical)) throw new Exception('Unsafe local path.');
        $absolute = ABSPATH . $logical;
        if (!is_file($absolute) || is_link($absolute)) throw new Exception('Local source file is missing: ' . $logical);
        $size = filesize($absolute);
        if ($size === false || $size > 2000000) throw new Exception('Local source file is too large: ' . $logical);
        $data = file_get_contents($absolute);
        if ($data === false || strpos($data, chr(0)) !== false) throw new Exception('Local source file is not safe text: ' . $logical);
        return $data;
    }
}

if (!function_exists('tamiyouz_devhub_130_audit')) {
    function tamiyouz_devhub_130_audit($action, $result, $meta) {
        $key = 'tamiyouz_devhub_130_audit';
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

if (!function_exists('tamiyouz_devhub_130_execute_push')) {
    function tamiyouz_devhub_130_execute_push($state, $fingerprint) {
        $approval = get_transient('tamiyouz_devhub_130_push_' . get_current_user_id());
        if (!is_array($approval) || empty($approval['fingerprint']) || !hash_equals((string) $approval['fingerprint'], (string) $fingerprint)) {
            throw new Exception('Reviewed Push expired or does not match. Run Review Push again.');
        }
        if ($approval['repo'] !== $state['repo'] || $approval['branch'] !== $state['branch']) {
            throw new Exception('Repository or branch changed after review.');
        }

        $fresh = tamiyouz_devhub_130_review($state, 'push', false);
        if (!hash_equals((string) $fresh['fingerprint'], (string) $fingerprint)) {
            throw new Exception('Local or GitHub state changed after review. Run Review Push again.');
        }
        if (!$fresh['baseline_ready']) throw new Exception('Sync baseline is not established.');
        if (!empty($fresh['blocked'])) throw new Exception('Push blocked by remote drift, delete, or conflict.');
        if ((int) $fresh['actionable'] < 1) throw new Exception('There are no reviewed local changes to push.');

        $token = tamiyouz_devhub_103_decrypt($state['token']);
        $remote = tamiyouz_devhub_130_remote($token, $state['repo'], $state['branch']);
        $tree_entries = array();

        foreach ($fresh['rows'] as $row) {
            if ($row['status'] !== 'local_only' && $row['status'] !== 'local_change') continue;
            $data = tamiyouz_devhub_130_read_local($row['path']);
            $blob = tamiyouz_devhub_111_github_request(
                $token,
                'POST',
                '/repos/' . $state['repo'] . '/git/blobs',
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

        $tree = tamiyouz_devhub_111_github_request(
            $token,
            'POST',
            '/repos/' . $state['repo'] . '/git/trees',
            array('base_tree' => $remote['tree'], 'tree' => $tree_entries),
            false
        );
        if (empty($tree['sha'])) throw new Exception('GitHub tree creation failed.');

        $commit = tamiyouz_devhub_111_github_request(
            $token,
            'POST',
            '/repos/' . $state['repo'] . '/git/commits',
            array(
                'message' => 'Sync Tamiyouz website source',
                'tree' => $tree['sha'],
                'parents' => array($remote['head'])
            ),
            false
        );
        if (empty($commit['sha'])) throw new Exception('GitHub commit creation failed.');

        tamiyouz_devhub_111_github_request(
            $token,
            'PATCH',
            '/repos/' . $state['repo'] . '/git/refs/heads/' . rawurlencode($state['branch']),
            array('sha' => $commit['sha'], 'force' => false),
            false
        );

        $new_remote = tamiyouz_devhub_130_remote($token, $state['repo'], $state['branch']);
        $state = tamiyouz_devhub_130_save_baseline($state, $new_remote);
        $state['last_push_at'] = gmdate('c');
        update_option('tamiyouz_devhub_103_state', $state, false);
        delete_transient('tamiyouz_devhub_130_push_' . get_current_user_id());

        tamiyouz_devhub_130_audit('controlled_push', 'success', array(
            'files' => count($tree_entries),
            'commit' => $commit['sha']
        ));

        return array('files' => count($tree_entries), 'commit' => $commit['sha']);
    }
}

$tyz130_review = null;
$tyz130_error = '';
$tyz130_notice = '';
$tyz130_state = tamiyouz_devhub_103_state();
$tyz130_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

if ($tyz130_method === 'POST') {
    $tyz130_action = isset($_POST['devhub_action']) ? sanitize_key(wp_unslash($_POST['devhub_action'])) : '';
    if (in_array($tyz130_action, array('review_push', 'review_pull', 'review_sync'), true)) {
        try {
            check_admin_referer('tamiyouz_devhub_103');
            $tyz130_review = tamiyouz_devhub_130_review($tyz130_state, substr($tyz130_action, 7), true);
            $tyz130_state = tamiyouz_devhub_103_state();
        } catch (Exception $e) {
            $tyz130_error = $e->getMessage();
        }
    } elseif ($tyz130_action === 'execute_controlled_push') {
        try {
            check_admin_referer('tamiyouz_devhub_103');
            $fingerprint = isset($_POST['fingerprint']) ? sanitize_text_field(wp_unslash($_POST['fingerprint'])) : '';
            $result = tamiyouz_devhub_130_execute_push($tyz130_state, $fingerprint);
            $tyz130_notice = 'Controlled Push completed: ' . $result['files'] . ' files · ' . $result['commit'];
            $tyz130_state = tamiyouz_devhub_103_state();
        } catch (Exception $e) {
            $tyz130_error = $e->getMessage();
            tamiyouz_devhub_130_audit('controlled_push', 'failed', array('error' => $tyz130_error));
        }
    }
}
?>
<div class="wrap tyz-review130" dir="rtl">
    <div class="tyz130-head">
        <div>
            <h2>Synchronization Review</h2>
            <p>V1.4 — safe baseline bootstrap + controlled Push with real progress. Pull/Sync execution remains disabled.</p>
        </div>
        <span class="tyz130-badge ok">SYNC STATE ACTIVE</span>
    </div>

    <?php if ($tyz130_error !== '') : ?><div class="notice notice-error"><p><?php echo esc_html($tyz130_error); ?></p></div><?php endif; ?>
    <?php if ($tyz130_notice !== '') : ?><div class="notice notice-success"><p><?php echo esc_html($tyz130_notice); ?></p></div><?php endif; ?>

    <div class="tyz130-card">
        <div class="tyz130-actions">
            <form method="post"><?php wp_nonce_field('tamiyouz_devhub_103'); ?><input type="hidden" name="devhub_action" value="review_push"><button class="button">Review Push</button></form>
            <form method="post"><?php wp_nonce_field('tamiyouz_devhub_103'); ?><input type="hidden" name="devhub_action" value="review_pull"><button class="button">Review Pull</button></form>
            <form method="post"><?php wp_nonce_field('tamiyouz_devhub_103'); ?><input type="hidden" name="devhub_action" value="review_sync"><button class="button">Review Sync</button></form>
            <span class="tyz130-badge hold">PULL/SYNC EXECUTE DISABLED</span>
        </div>

        <p class="description">Remote scope: <code>site/wp-content/mu-plugins/tamiyouz-*</code>. Developer Hub itself is excluded.</p>

        <?php if ($tyz130_review !== null) : ?>
            <div class="tyz130-summary">
                <strong><?php echo esc_html(strtoupper($tyz130_review['action'])); ?></strong>
                <span>Baseline: <?php echo $tyz130_review['baseline_ready'] ? 'READY' : 'NOT READY'; ?></span>
                <span>Actionable: <?php echo esc_html((string) $tyz130_review['actionable']); ?></span>
                <span>Blocked: <?php echo esc_html((string) count($tyz130_review['blocked'])); ?></span>
                <code><?php echo esc_html($tyz130_review['head']); ?></code>
            </div>

            <div class="tyz130-stats">
                <?php foreach ($tyz130_review['counts'] as $name => $value) : ?>
                    <div><small><?php echo esc_html($name); ?></small><b><?php echo esc_html((string) $value); ?></b></div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($tyz130_review['blocked'])) : ?>
                <div class="tyz130-alert"><strong>Push blocked:</strong><br><?php echo esc_html(implode(', ', $tyz130_review['blocked'])); ?></div>
            <?php endif; ?>

            <div class="tyz130-table"><table class="widefat striped"><thead><tr><th>Direction</th><th>Status</th><th>File</th></tr></thead><tbody>
            <?php $shown = 0; foreach ($tyz130_review['rows'] as $row) : if ($row['status'] === 'synced') continue; $shown++; ?>
                <?php
                if ($row['status'] === 'local_only' || $row['status'] === 'local_change') { $dir = 'Local → GitHub'; $cls = 'local'; }
                elseif ($row['status'] === 'remote_only' || $row['status'] === 'remote_change') { $dir = 'GitHub → Local'; $cls = 'remote'; }
                else { $dir = 'MANUAL'; $cls = 'bad'; }
                ?>
                <tr><td class="<?php echo esc_attr($cls); ?>"><?php echo esc_html($dir); ?></td><td><?php echo esc_html($row['status']); ?></td><td><code><?php echo esc_html($row['path']); ?></code></td></tr>
            <?php endforeach; if ($shown === 0) : ?><tr><td colspan="3">Managed source is synchronized.</td></tr><?php endif; ?>
            </tbody></table></div>

            <?php if ($tyz130_review['action'] === 'push' && $tyz130_review['baseline_ready'] && empty($tyz130_review['blocked']) && (int) $tyz130_review['actionable'] > 0) : ?>
                <div class="tyz130-execute" data-tyz140-push>
                    <button
                        type="button"
                        class="button button-primary"
                        data-tyz140-push-button
                        data-fingerprint="<?php echo esc_attr($tyz130_review['fingerprint']); ?>"
                        data-nonce="<?php echo esc_attr(wp_create_nonce('tamiyouz_devhub_140_push')); ?>"
                    >Execute Reviewed Push</button>
                    <span>Approval expires in 10 minutes. Actual GitHub stages and file progress are shown below.</span>
                </div>
                <div class="tyz140-progress" data-tyz140-progress hidden>
                    <div class="tyz140-progress__head"><strong data-tyz140-status>Ready</strong><b data-tyz140-percent>0%</b></div>
                    <div class="tyz140-progress__track"><i data-tyz140-bar></i></div>
                    <code data-tyz140-detail>Waiting to start.</code>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<style>
.tyz-review130{max-width:1180px}.tyz130-head,.tyz130-actions,.tyz130-summary,.tyz130-execute{display:flex;align-items:center;gap:12px;flex-wrap:wrap}.tyz130-head{justify-content:space-between;margin:16px 0}.tyz130-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:20px}.tyz130-actions form{margin:0}.tyz130-badge{padding:5px 9px;border-radius:999px;font-size:12px;font-weight:700}.tyz130-badge.ok{background:#ecfdf5;color:#047857}.tyz130-badge.hold{background:#fff7ed;color:#c2410c}.tyz130-summary{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px;margin:14px 0}.tyz130-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}.tyz130-stats>div{border:1px solid #e5e7eb;border-radius:10px;padding:10px}.tyz130-stats small{display:block;color:#64748b}.tyz130-stats b{font-size:18px}.tyz130-alert{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:10px;padding:10px;margin:12px 0}.tyz130-table{max-height:430px;overflow:auto;margin-top:12px}.tyz130-table .local{color:#0369a1;font-weight:700}.tyz130-table .remote{color:#7c3aed;font-weight:700}.tyz130-table .bad{color:#b91c1c;font-weight:800}.tyz130-execute{margin-top:16px;padding-top:16px;border-top:1px solid #e2e8f0}.tyz130-execute span{color:#64748b}.tyz140-progress{margin-top:14px;padding:14px;border:1px solid #dbeafe;background:#f8fbff;border-radius:12px}.tyz140-progress__head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:9px}.tyz140-progress__head b{color:#0369a1}.tyz140-progress__track{height:10px;border-radius:999px;background:#e2e8f0;overflow:hidden}.tyz140-progress__track i{display:block;width:0;height:100%;background:linear-gradient(90deg,#2563eb,#0ea5e9);transition:width .25s ease}.tyz140-progress code{display:block;margin-top:9px;white-space:normal;word-break:break-word}.tyz140-progress.is-error{border-color:#fecaca;background:#fff7f7}.tyz140-progress.is-done{border-color:#bbf7d0;background:#f0fdf4}@media(max-width:800px){.tyz130-stats{grid-template-columns:repeat(2,1fr)}}
</style>

<script>
(function(){
    var box=document.querySelector('[data-tyz140-push]');
    if(!box)return;
    var btn=box.querySelector('[data-tyz140-push-button]');
    var panel=document.querySelector('[data-tyz140-progress]');
    if(!btn||!panel)return;
    var statusEl=panel.querySelector('[data-tyz140-status]');
    var percentEl=panel.querySelector('[data-tyz140-percent]');
    var bar=panel.querySelector('[data-tyz140-bar]');
    var detailEl=panel.querySelector('[data-tyz140-detail]');
    var running=false;

    function paint(data){
        var pct=Math.max(0,Math.min(100,parseInt(data.progress||0,10)));
        panel.hidden=false;
        panel.classList.toggle('is-done',data.done===true);
        panel.classList.remove('is-error');
        statusEl.textContent=data.status||'Working…';
        percentEl.textContent=pct+'%';
        bar.style.width=pct+'%';
        detailEl.textContent=data.detail||'';
        if(data.done){
            btn.disabled=true;
            btn.textContent='Push Complete';
        }
    }

    async function call(mode){
        var body=new URLSearchParams();
        body.set('action','tamiyouz_devhub_140_push_step');
        body.set('nonce',btn.dataset.nonce);
        body.set('fingerprint',btn.dataset.fingerprint);
        body.set('mode',mode);
        var res=await fetch(ajaxurl,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:body.toString()});
        var json=await res.json();
        if(!json||!json.success)throw new Error(json&&json.data&&json.data.message?json.data.message:'Push progress request failed.');
        paint(json.data);
        return json.data;
    }

    async function run(){
        if(running)return;
        if(!window.confirm('Push exactly these reviewed changes to GitHub?'))return;
        running=true;
        btn.disabled=true;
        btn.textContent='Pushing…';
        panel.hidden=false;
        try{
            var data=await call('start');
            while(!data.done){
                data=await call('next');
            }
        }catch(err){
            panel.classList.add('is-error');
            statusEl.textContent='Push paused / failed';
            detailEl.textContent=err&&err.message?err.message:String(err);
            btn.disabled=false;
            btn.textContent='Resume Push';
        }finally{
            running=false;
        }
    }
    btn.addEventListener('click',run);
})();
</script>
