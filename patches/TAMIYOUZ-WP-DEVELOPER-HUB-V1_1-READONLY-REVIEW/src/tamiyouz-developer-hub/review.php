<?php
if (!defined('ABSPATH') || !current_user_can('manage_options')) {
    exit;
}

if (!function_exists('tamiyouz_devhub_110_safe_path')) {
    function tamiyouz_devhub_110_safe_path($path) {
        $path = str_replace('\\', '/', ltrim((string) $path, '/'));
        if ($path === '' || strpos($path, '..') !== false) return false;
        if (preg_match('#(^|/)(\.env|uploads|cache|backups|storage)(/|$)#i', $path)) return false;
        if (preg_match('#(^|/)tamiyouz-developer-hub(?:\.php|/|$)#i', $path)) return false;
        if (!preg_match('#^wp-content/mu-plugins/tamiyouz-[A-Za-z0-9._/-]+$#', $path)) return false;
        return (bool) preg_match('#\.(php|css|js|json|md|txt|svg)$#i', $path);
    }
}

if (!function_exists('tamiyouz_devhub_110_blob_sha')) {
    function tamiyouz_devhub_110_blob_sha($data) {
        return sha1('blob ' . strlen($data) . chr(0) . $data);
    }
}

if (!function_exists('tamiyouz_devhub_110_scan')) {
    function tamiyouz_devhub_110_scan($absolute, $relative, &$files) {
        if (!file_exists($absolute) || is_link($absolute)) return;
        if (is_file($absolute)) {
            $logical = 'wp-content/mu-plugins/' . ltrim($relative, '/');
            if (!tamiyouz_devhub_110_safe_path($logical)) return;
            $size = filesize($absolute);
            if ($size === false || $size > 2000000) return;
            $data = file_get_contents($absolute);
            if ($data === false) return;
            $files[$logical] = tamiyouz_devhub_110_blob_sha($data);
            return;
        }
        if (!is_dir($absolute)) return;
        $items = scandir($absolute);
        if (!is_array($items)) return;
        foreach ($items as $name) {
            if ($name === '.' || $name === '..') continue;
            tamiyouz_devhub_110_scan($absolute . '/' . $name, trim($relative . '/' . $name, '/'), $files);
        }
    }
}

if (!function_exists('tamiyouz_devhub_110_local')) {
    function tamiyouz_devhub_110_local() {
        $root = WP_CONTENT_DIR . '/mu-plugins';
        $files = array();
        $items = is_dir($root) ? scandir($root) : false;
        if (!is_array($items)) return $files;
        foreach ($items as $name) {
            if ($name === '.' || $name === '..') continue;
            if (strpos(strtolower($name), 'tamiyouz-') !== 0) continue;
            if (strpos(strtolower($name), 'tamiyouz-developer-hub') === 0) continue;
            tamiyouz_devhub_110_scan($root . '/' . $name, $name, $files);
        }
        ksort($files);
        return $files;
    }
}

if (!function_exists('tamiyouz_devhub_110_remote')) {
    function tamiyouz_devhub_110_remote($token, $repo, $branch) {
        if (!preg_match('#^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$#', $repo)) throw new Exception('Invalid repository.');
        $branch_meta = tamiyouz_devhub_103_github($token, '/repos/' . $repo . '/branches/' . rawurlencode($branch));
        $head = isset($branch_meta['commit']['sha']) ? (string) $branch_meta['commit']['sha'] : '';
        if ($head === '') throw new Exception('GitHub branch HEAD missing.');
        $commit = tamiyouz_devhub_103_github($token, '/repos/' . $repo . '/git/commits/' . $head);
        $tree_sha = isset($commit['tree']['sha']) ? (string) $commit['tree']['sha'] : '';
        if ($tree_sha === '') throw new Exception('GitHub tree SHA missing.');
        $tree = tamiyouz_devhub_103_github($token, '/repos/' . $repo . '/git/trees/' . $tree_sha . '?recursive=1');
        if (!empty($tree['truncated'])) throw new Exception('GitHub tree is truncated; safe review stopped.');
        $files = array();
        $items = isset($tree['tree']) && is_array($tree['tree']) ? $tree['tree'] : array();
        foreach ($items as $item) {
            if (!isset($item['type']) || $item['type'] !== 'blob' || empty($item['path']) || empty($item['sha'])) continue;
            $repo_path = (string) $item['path'];
            if (strpos($repo_path, 'site/') !== 0) continue;
            $logical = substr($repo_path, 5);
            if (!tamiyouz_devhub_110_safe_path($logical)) continue;
            $files[$logical] = (string) $item['sha'];
        }
        ksort($files);
        return array('head' => $head, 'files' => $files);
    }
}

if (!function_exists('tamiyouz_devhub_110_review')) {
    function tamiyouz_devhub_110_review($state, $action) {
        if (!in_array($action, array('push', 'pull', 'sync'), true)) throw new Exception('Invalid review action.');
        if (empty($state['token']) || empty($state['repo']) || empty($state['branch'])) throw new Exception('Connect GitHub and save repository first.');
        $token = tamiyouz_devhub_103_decrypt($state['token']);
        $local = tamiyouz_devhub_110_local();
        $remote = tamiyouz_devhub_110_remote($token, $state['repo'], $state['branch']);
        $paths = array_unique(array_merge(array_keys($local), array_keys($remote['files'])));
        sort($paths);
        $counts = array('synced' => 0, 'local_only' => 0, 'remote_only' => 0, 'different' => 0);
        $rows = array();
        $blocked = array();
        $actionable = 0;
        foreach ($paths as $path) {
            $l = isset($local[$path]) ? $local[$path] : null;
            $r = isset($remote['files'][$path]) ? $remote['files'][$path] : null;
            if ($l !== null && $r !== null && hash_equals((string) $l, (string) $r)) $status = 'synced';
            elseif ($l !== null && $r === null) $status = 'local_only';
            elseif ($l === null && $r !== null) $status = 'remote_only';
            else $status = 'different';
            $counts[$status]++;
            if ($status === 'different') $blocked[] = $path;
            if ($action === 'push' && $status === 'local_only') $actionable++;
            if ($action === 'pull' && $status === 'remote_only') $actionable++;
            if ($action === 'sync' && ($status === 'local_only' || $status === 'remote_only')) $actionable++;
            $rows[] = array('path' => $path, 'status' => $status, 'local' => $l !== null, 'remote' => $r !== null);
        }
        return array('action' => $action, 'head' => $remote['head'], 'counts' => $counts, 'rows' => $rows, 'blocked' => $blocked, 'actionable' => $actionable);
    }
}

$tyz110_review = null;
$tyz110_error = '';
$tyz110_state = tamiyouz_devhub_103_state();
$tyz110_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
if ($tyz110_method === 'POST') {
    $tyz110_action = isset($_POST['devhub_action']) ? sanitize_key(wp_unslash($_POST['devhub_action'])) : '';
    if (in_array($tyz110_action, array('review_push', 'review_pull', 'review_sync'), true)) {
        try {
            check_admin_referer('tamiyouz_devhub_103');
            $tyz110_review = tamiyouz_devhub_110_review($tyz110_state, substr($tyz110_action, 7));
        } catch (Exception $e) {
            $tyz110_error = $e->getMessage();
        }
    }
}
?>
<div class="wrap tyz-review110" dir="rtl">
    <div class="tyz110-head"><div><h2>Synchronization Review</h2><p>V1.1 — مقارنة Read-only بنفس فلسفة TCRM. لا يوجد أي تنفيذ أو كتابة.</p></div><span class="tyz110-badge ok">READONLY REVIEW ACTIVE</span></div>
    <?php if ($tyz110_error !== '') : ?><div class="notice notice-error"><p><?php echo esc_html($tyz110_error); ?></p></div><?php endif; ?>
    <div class="tyz110-card">
        <div class="tyz110-actions">
            <form method="post"><?php wp_nonce_field('tamiyouz_devhub_103'); ?><input type="hidden" name="devhub_action" value="review_push"><button class="button">Review Push</button></form>
            <form method="post"><?php wp_nonce_field('tamiyouz_devhub_103'); ?><input type="hidden" name="devhub_action" value="review_pull"><button class="button">Review Pull</button></form>
            <form method="post"><?php wp_nonce_field('tamiyouz_devhub_103'); ?><input type="hidden" name="devhub_action" value="review_sync"><button class="button button-primary">Review Sync</button></form>
            <span class="tyz110-badge hold">EXECUTE DISABLED</span>
        </div>
        <p class="description">Remote scope: <code>site/wp-content/mu-plugins/tamiyouz-*</code>. Developer Hub files are excluded.</p>
        <?php if ($tyz110_review !== null) : ?>
            <div class="tyz110-summary">
                <strong><?php echo esc_html(strtoupper($tyz110_review['action'])); ?></strong>
                <span>Actionable: <?php echo esc_html((string) $tyz110_review['actionable']); ?></span>
                <span>Blocked: <?php echo esc_html((string) count($tyz110_review['blocked'])); ?></span>
                <code><?php echo esc_html($tyz110_review['head']); ?></code>
            </div>
            <div class="tyz110-stats"><?php foreach ($tyz110_review['counts'] as $name => $value) : ?><div><small><?php echo esc_html($name); ?></small><b><?php echo esc_html((string) $value); ?></b></div><?php endforeach; ?></div>
            <?php if (!empty($tyz110_review['blocked'])) : ?><div class="tyz110-alert"><strong>Different on both sides — manual decision required:</strong><br><?php echo esc_html(implode(', ', $tyz110_review['blocked'])); ?></div><?php endif; ?>
            <div class="tyz110-table"><table class="widefat striped"><thead><tr><th>Direction</th><th>Status</th><th>File</th></tr></thead><tbody>
            <?php $shown = 0; foreach ($tyz110_review['rows'] as $row) : if ($row['status'] === 'synced') continue; $shown++; ?>
                <?php if ($row['status'] === 'local_only') { $dir = 'Local → GitHub'; $cls = 'local'; } elseif ($row['status'] === 'remote_only') { $dir = 'GitHub → Local'; $cls = 'remote'; } else { $dir = 'MANUAL'; $cls = 'bad'; } ?>
                <tr><td class="<?php echo esc_attr($cls); ?>"><?php echo esc_html($dir); ?></td><td><?php echo esc_html($row['status']); ?></td><td><code><?php echo esc_html($row['path']); ?></code></td></tr>
            <?php endforeach; if ($shown === 0) : ?><tr><td colspan="3">Managed source is synchronized.</td></tr><?php endif; ?>
            </tbody></table></div>
        <?php endif; ?>
    </div>
</div>
<style>
.tyz-review110{max-width:1180px}.tyz110-head,.tyz110-actions,.tyz110-summary{display:flex;align-items:center;gap:12px;flex-wrap:wrap}.tyz110-head{justify-content:space-between;margin:16px 0}.tyz110-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:20px}.tyz110-actions form{margin:0}.tyz110-badge{padding:5px 9px;border-radius:999px;font-size:12px;font-weight:700}.tyz110-badge.ok{background:#ecfdf5;color:#047857}.tyz110-badge.hold{background:#fff7ed;color:#c2410c}.tyz110-summary{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px;margin:14px 0}.tyz110-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}.tyz110-stats>div{border:1px solid #e5e7eb;border-radius:10px;padding:10px}.tyz110-stats small{display:block;color:#64748b}.tyz110-stats b{font-size:18px}.tyz110-alert{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:10px;padding:10px;margin:12px 0}.tyz110-table{max-height:430px;overflow:auto;margin-top:12px}.tyz110-table .local{color:#0369a1;font-weight:700}.tyz110-table .remote{color:#7c3aed;font-weight:700}.tyz110-table .bad{color:#b91c1c;font-weight:800}@media(max-width:800px){.tyz110-stats{grid-template-columns:repeat(2,1fr)}}
</style>
