<?php
// Marker: TAMIYOUZ-WP-DEVELOPER-HUB-V1_4-BASELINE-REAL-PUSH-PROGRESS
if (!defined('ABSPATH')) { exit; }

if (!function_exists('tamiyouz_devhub_140_state')) {
    function tamiyouz_devhub_140_state() {
        $state = get_option('tamiyouz_devhub_103_state', array());
        return is_array($state) ? $state : array();
    }
}
if (!function_exists('tamiyouz_devhub_140_secret_key')) {
    function tamiyouz_devhub_140_secret_key() {
        $seed = defined('AUTH_KEY') ? AUTH_KEY : ABSPATH;
        return hash('sha256', $seed . '|tamiyouz-devhub-103', true);
    }
}
if (!function_exists('tamiyouz_devhub_140_decrypt')) {
    function tamiyouz_devhub_140_decrypt($payload) {
        if (!$payload) return '';
        $raw = base64_decode($payload, true);
        if ($raw === false || strlen($raw) < 49) throw new Exception('Stored GitHub token is invalid.');
        $iv = substr($raw, 0, 16);
        $mac = substr($raw, 16, 32);
        $cipher = substr($raw, 48);
        $expected = hash_hmac('sha256', $iv . $cipher, tamiyouz_devhub_140_secret_key(), true);
        if (!hash_equals($expected, $mac)) throw new Exception('Stored GitHub token integrity check failed.');
        $plain = openssl_decrypt($cipher, 'AES-256-CBC', tamiyouz_devhub_140_secret_key(), OPENSSL_RAW_DATA, $iv);
        if ($plain === false) throw new Exception('Stored GitHub token could not be decrypted.');
        return $plain;
    }
}
if (!function_exists('tamiyouz_devhub_140_github')) {
    function tamiyouz_devhub_140_github($token, $method, $path, $body = null) {
        $args = array(
            'method' => $method,
            'timeout' => 25,
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent' => 'Tamiyouz-WordPress-Developer-Hub/1.4.0'
            )
        );
        if ($body !== null) {
            $args['body'] = wp_json_encode($body);
            $args['headers']['Content-Type'] = 'application/json';
        }
        $response = wp_remote_request('https://api.github.com' . $path, $args);
        if (is_wp_error($response)) throw new Exception($response->get_error_message());
        $code = (int) wp_remote_retrieve_response_code($response);
        $raw = wp_remote_retrieve_body($response);
        $decoded = $raw !== '' ? json_decode($raw, true) : array();
        if ($code < 200 || $code >= 300) {
            $message = is_array($decoded) && isset($decoded['message']) ? $decoded['message'] : ('GitHub HTTP ' . $code);
            throw new Exception($message);
        }
        return is_array($decoded) ? $decoded : array();
    }
}
if (!function_exists('tamiyouz_devhub_140_safe_path')) {
    function tamiyouz_devhub_140_safe_path($path) {
        $path = str_replace('\\', '/', ltrim((string) $path, '/'));
        if ($path === '' || strpos($path, '..') !== false) return false;
        if (preg_match('#(^|/)(\.env|uploads|cache|backups|storage)(/|$)#i', $path)) return false;
        if (preg_match('#(^|/)tamiyouz-developer-hub(?:\.php|/|$)#i', $path)) return false;
        if (!preg_match('#^wp-content/mu-plugins/tamiyouz-[A-Za-z0-9._/-]+$#', $path)) return false;
        return (bool) preg_match('#\.(php|css|js|json|md|txt|svg)$#i', $path);
    }
}
if (!function_exists('tamiyouz_devhub_140_blob_sha')) {
    function tamiyouz_devhub_140_blob_sha($data) {
        return sha1('blob ' . strlen($data) . chr(0) . $data);
    }
}
if (!function_exists('tamiyouz_devhub_140_scan')) {
    function tamiyouz_devhub_140_scan($absolute, $relative, &$files) {
        if (!file_exists($absolute) || is_link($absolute)) return;
        if (is_file($absolute)) {
            $logical = 'wp-content/mu-plugins/' . ltrim($relative, '/');
            if (!tamiyouz_devhub_140_safe_path($logical)) return;
            $size = filesize($absolute);
            if ($size === false || $size > 2000000) return;
            $data = file_get_contents($absolute);
            if ($data === false || strpos($data, chr(0)) !== false) return;
            $files[$logical] = array('sha' => tamiyouz_devhub_140_blob_sha($data), 'size' => strlen($data));
            return;
        }
        if (!is_dir($absolute)) return;
        $items = scandir($absolute);
        if (!is_array($items)) return;
        foreach ($items as $name) {
            if ($name === '.' || $name === '..') continue;
            tamiyouz_devhub_140_scan($absolute . '/' . $name, trim($relative . '/' . $name, '/'), $files);
        }
    }
}
if (!function_exists('tamiyouz_devhub_140_local')) {
    function tamiyouz_devhub_140_local() {
        $root = WP_CONTENT_DIR . '/mu-plugins';
        $files = array();
        $items = is_dir($root) ? scandir($root) : false;
        if (!is_array($items)) return $files;
        foreach ($items as $name) {
            if ($name === '.' || $name === '..') continue;
            if (strpos(strtolower($name), 'tamiyouz-') !== 0) continue;
            if (strpos(strtolower($name), 'tamiyouz-developer-hub') === 0) continue;
            tamiyouz_devhub_140_scan($root . '/' . $name, $name, $files);
        }
        ksort($files);
        return $files;
    }
}
if (!function_exists('tamiyouz_devhub_140_remote')) {
    function tamiyouz_devhub_140_remote($token, $repo, $branch) {
        $branch_meta = tamiyouz_devhub_140_github($token, 'GET', '/repos/' . $repo . '/branches/' . rawurlencode($branch));
        $head = isset($branch_meta['commit']['sha']) ? (string) $branch_meta['commit']['sha'] : '';
        if ($head === '') throw new Exception('GitHub branch HEAD missing.');
        $commit = tamiyouz_devhub_140_github($token, 'GET', '/repos/' . $repo . '/git/commits/' . $head);
        $tree_sha = isset($commit['tree']['sha']) ? (string) $commit['tree']['sha'] : '';
        if ($tree_sha === '') throw new Exception('GitHub tree SHA missing.');
        $tree = tamiyouz_devhub_140_github($token, 'GET', '/repos/' . $repo . '/git/trees/' . $tree_sha . '?recursive=1');
        if (!empty($tree['truncated'])) throw new Exception('GitHub tree is truncated; safe push stopped.');
        $files = array();
        $items = isset($tree['tree']) && is_array($tree['tree']) ? $tree['tree'] : array();
        foreach ($items as $item) {
            if (!isset($item['type']) || $item['type'] !== 'blob' || empty($item['path']) || empty($item['sha'])) continue;
            $repo_path = (string) $item['path'];
            if (strpos($repo_path, 'site/') !== 0) continue;
            $logical = substr($repo_path, 5);
            if (!tamiyouz_devhub_140_safe_path($logical)) continue;
            $files[$logical] = array('sha' => (string) $item['sha'], 'repo_path' => $repo_path);
        }
        ksort($files);
        return array('head' => $head, 'tree' => $tree_sha, 'files' => $files);
    }
}
if (!function_exists('tamiyouz_devhub_140_manifest')) {
    function tamiyouz_devhub_140_manifest($files) {
        $out = array();
        foreach ($files as $path => $meta) $out[$path] = isset($meta['sha']) ? $meta['sha'] : $meta;
        ksort($out);
        return $out;
    }
}
if (!function_exists('tamiyouz_devhub_140_review')) {
    function tamiyouz_devhub_140_review($state) {
        if (empty($state['token']) || empty($state['repo']) || empty($state['branch'])) throw new Exception('Connect GitHub and save repository first.');
        $token = tamiyouz_devhub_140_decrypt($state['token']);
        $local = tamiyouz_devhub_140_local();
        $remote = tamiyouz_devhub_140_remote($token, $state['repo'], $state['branch']);
        $base = isset($state['base_manifest']) && is_array($state['base_manifest']) ? $state['base_manifest'] : array();
        if (empty($base)) throw new Exception('Sync baseline is not established. Run Review Push again.');

        $paths = array_unique(array_merge(array_keys($local), array_keys($remote['files']), array_keys($base)));
        sort($paths);
        $blocked = array();
        $rows = array();
        $actionable = 0;

        foreach ($paths as $path) {
            $l = isset($local[$path]['sha']) ? $local[$path]['sha'] : null;
            $r = isset($remote['files'][$path]['sha']) ? $remote['files'][$path]['sha'] : null;
            $b = isset($base[$path]) ? $base[$path] : null;

            if ($l !== null && $r !== null && hash_equals((string) $l, (string) $r)) $status = 'synced';
            elseif ($b === null) {
                if ($l !== null && $r === null) $status = 'local_only';
                elseif ($l === null && $r !== null) $status = 'remote_only';
                else $status = 'conflict';
            } else {
                if ($l === null && $r !== null && hash_equals((string) $r, (string) $b)) $status = 'local_deleted';
                elseif ($r === null && $l !== null && hash_equals((string) $l, (string) $b)) $status = 'remote_deleted';
                elseif ($l !== null && $r !== null && hash_equals((string) $r, (string) $b) && !hash_equals((string) $l, (string) $b)) $status = 'local_change';
                elseif ($r !== null && $l !== null && hash_equals((string) $l, (string) $b) && !hash_equals((string) $r, (string) $b)) $status = 'remote_change';
                else $status = 'conflict';
            }

            if ($status === 'local_only' || $status === 'local_change') $actionable++;
            elseif ($status !== 'synced') $blocked[] = $path . ' [' . $status . ']';
            $rows[] = array('path' => $path, 'status' => $status);
        }

        $fingerprint = hash('sha256', wp_json_encode(array(
            'push',
            $state['repo'],
            $state['branch'],
            $remote['head'],
            $base,
            tamiyouz_devhub_140_manifest($local),
            tamiyouz_devhub_140_manifest($remote['files'])
        )));

        return array(
            'fingerprint' => $fingerprint,
            'blocked' => $blocked,
            'actionable' => $actionable,
            'rows' => $rows,
            'remote' => $remote
        );
    }
}
if (!function_exists('tamiyouz_devhub_140_read_local')) {
    function tamiyouz_devhub_140_read_local($logical) {
        if (!tamiyouz_devhub_140_safe_path($logical)) throw new Exception('Unsafe local path.');
        $absolute = ABSPATH . $logical;
        if (!is_file($absolute) || is_link($absolute)) throw new Exception('Local source file is missing: ' . $logical);
        $size = filesize($absolute);
        if ($size === false || $size > 2000000) throw new Exception('Local source file is too large: ' . $logical);
        $data = file_get_contents($absolute);
        if ($data === false || strpos($data, chr(0)) !== false) throw new Exception('Local source file is not safe text: ' . $logical);
        return $data;
    }
}
if (!function_exists('tamiyouz_devhub_140_audit')) {
    function tamiyouz_devhub_140_audit($result, $meta) {
        $key = 'tamiyouz_devhub_130_audit';
        $items = get_option($key, array());
        if (!is_array($items)) $items = array();
        array_unshift($items, array(
            'at' => gmdate('c'),
            'user' => wp_get_current_user()->user_login,
            'action' => 'controlled_push_progress',
            'result' => $result,
            'meta' => is_array($meta) ? $meta : array()
        ));
        update_option($key, array_slice($items, 0, 50), false);
    }
}
if (!function_exists('tamiyouz_devhub_140_payload')) {
    function tamiyouz_devhub_140_payload($op, $status, $detail, $progress, $done = false) {
        return array(
            'stage' => isset($op['stage']) ? $op['stage'] : '',
            'status' => $status,
            'detail' => $detail,
            'progress' => (int) $progress,
            'done' => (bool) $done,
            'files_done' => isset($op['index']) ? (int) $op['index'] : 0,
            'files_total' => isset($op['paths']) && is_array($op['paths']) ? count($op['paths']) : 0,
            'commit' => isset($op['commit']) ? (string) $op['commit'] : ''
        );
    }
}

if (!function_exists('tamiyouz_devhub_140_ajax')) {
    function tamiyouz_devhub_140_ajax() {
        try {
            if (!current_user_can('manage_options')) throw new Exception('Unauthorized.');
            check_ajax_referer('tamiyouz_devhub_140_push', 'nonce');

            $fingerprint = isset($_POST['fingerprint']) ? sanitize_text_field(wp_unslash($_POST['fingerprint'])) : '';
            $mode = isset($_POST['mode']) ? sanitize_key(wp_unslash($_POST['mode'])) : 'start';
            if ($fingerprint === '') throw new Exception('Missing reviewed Push fingerprint.');

            $uid = get_current_user_id();
            $op_key = 'tamiyouz_devhub_140_op_' . $uid;
            $op = get_transient($op_key);

            if ($mode === 'start') {
                if (is_array($op) && isset($op['fingerprint']) && hash_equals((string) $op['fingerprint'], $fingerprint) && empty($op['done'])) {
                    wp_send_json_success(tamiyouz_devhub_140_payload($op, 'Resuming reviewed Push', 'Continuing from ' . $op['stage'] . '.', isset($op['progress']) ? $op['progress'] : 5, false));
                }

                $approval = get_transient('tamiyouz_devhub_130_push_' . $uid);
                if (!is_array($approval) || empty($approval['fingerprint']) || !hash_equals((string) $approval['fingerprint'], $fingerprint)) {
                    throw new Exception('Reviewed Push expired or does not match. Run Review Push again.');
                }

                $state = tamiyouz_devhub_140_state();
                if (($approval['repo'] ?? '') !== ($state['repo'] ?? '') || ($approval['branch'] ?? '') !== ($state['branch'] ?? '')) {
                    throw new Exception('Repository or branch changed after review.');
                }

                $fresh = tamiyouz_devhub_140_review($state);
                if (!hash_equals((string) $fresh['fingerprint'], $fingerprint)) throw new Exception('Local or GitHub state changed after review. Run Review Push again.');
                if (!empty($fresh['blocked'])) throw new Exception('Push blocked by remote drift, delete, or conflict.');
                if ((int) $fresh['actionable'] < 1) throw new Exception('There are no reviewed local changes to push.');

                $paths = array();
                foreach ($fresh['rows'] as $row) {
                    if ($row['status'] === 'local_only' || $row['status'] === 'local_change') $paths[] = $row['path'];
                }

                $op = array(
                    'fingerprint' => $fingerprint,
                    'repo' => $state['repo'],
                    'branch' => $state['branch'],
                    'remote_head' => $fresh['remote']['head'],
                    'remote_tree' => $fresh['remote']['tree'],
                    'paths' => $paths,
                    'index' => 0,
                    'tree_entries' => array(),
                    'stage' => 'upload',
                    'progress' => 5,
                    'started_at' => time(),
                    'done' => false
                );
                set_transient($op_key, $op, 900);
                wp_send_json_success(tamiyouz_devhub_140_payload($op, 'Safety recheck passed', 'Uploading reviewed files to GitHub…', 5, false));
            }

            if (!is_array($op) || empty($op['fingerprint']) || !hash_equals((string) $op['fingerprint'], $fingerprint)) {
                throw new Exception('Push operation state is missing. Run Review Push again.');
            }
            if (!empty($op['done'])) {
                wp_send_json_success(tamiyouz_devhub_140_payload($op, 'Push complete', 'GitHub commit ' . $op['commit'], 100, true));
            }

            $state = tamiyouz_devhub_140_state();
            $token = tamiyouz_devhub_140_decrypt($state['token']);

            if ($op['stage'] === 'upload') {
                $total = count($op['paths']);
                if ($op['index'] < $total) {
                    $path = $op['paths'][$op['index']];
                    $data = tamiyouz_devhub_140_read_local($path);
                    $blob = tamiyouz_devhub_140_github($token, 'POST', '/repos/' . $op['repo'] . '/git/blobs', array(
                        'content' => base64_encode($data),
                        'encoding' => 'base64'
                    ));
                    if (empty($blob['sha'])) throw new Exception('GitHub blob creation failed for ' . $path);
                    $op['tree_entries'][] = array('path' => 'site/' . $path, 'mode' => '100644', 'type' => 'blob', 'sha' => $blob['sha']);
                    $op['index']++;
                    $op['progress'] = 8 + (int) round(($op['index'] / max(1, $total)) * 52);
                    if ($op['index'] >= $total) $op['stage'] = 'tree';
                    set_transient($op_key, $op, 900);
                    wp_send_json_success(tamiyouz_devhub_140_payload($op, 'Uploading files', $op['index'] . '/' . $total . ' · ' . $path, $op['progress'], false));
                }
                $op['stage'] = 'tree';
            }

            if ($op['stage'] === 'tree') {
                $tree = tamiyouz_devhub_140_github($token, 'POST', '/repos/' . $op['repo'] . '/git/trees', array(
                    'base_tree' => $op['remote_tree'],
                    'tree' => $op['tree_entries']
                ));
                if (empty($tree['sha'])) throw new Exception('GitHub tree creation failed.');
                $op['new_tree'] = $tree['sha'];
                $op['stage'] = 'commit';
                $op['progress'] = 70;
                set_transient($op_key, $op, 900);
                wp_send_json_success(tamiyouz_devhub_140_payload($op, 'Tree created', 'Preparing GitHub commit…', 70, false));
            }

            if ($op['stage'] === 'commit') {
                $commit = tamiyouz_devhub_140_github($token, 'POST', '/repos/' . $op['repo'] . '/git/commits', array(
                    'message' => 'Sync Tamiyouz website source',
                    'tree' => $op['new_tree'],
                    'parents' => array($op['remote_head'])
                ));
                if (empty($commit['sha'])) throw new Exception('GitHub commit creation failed.');
                $op['commit'] = $commit['sha'];
                $op['stage'] = 'ref';
                $op['progress'] = 80;
                set_transient($op_key, $op, 900);
                wp_send_json_success(tamiyouz_devhub_140_payload($op, 'Commit created', substr($op['commit'], 0, 12) . ' · updating branch…', 80, false));
            }

            if ($op['stage'] === 'ref') {
                tamiyouz_devhub_140_github($token, 'PATCH', '/repos/' . $op['repo'] . '/git/refs/heads/' . rawurlencode($op['branch']), array(
                    'sha' => $op['commit'],
                    'force' => false
                ));
                $op['stage'] = 'verify';
                $op['progress'] = 90;
                set_transient($op_key, $op, 900);
                wp_send_json_success(tamiyouz_devhub_140_payload($op, 'Branch updated', 'Verifying remote HEAD…', 90, false));
            }

            if ($op['stage'] === 'verify') {
                $remote = tamiyouz_devhub_140_remote($token, $op['repo'], $op['branch']);
                if (!hash_equals((string) $remote['head'], (string) $op['commit'])) throw new Exception('Remote HEAD verification failed.');
                $op['new_manifest'] = tamiyouz_devhub_140_manifest($remote['files']);
                $op['stage'] = 'baseline';
                $op['progress'] = 96;
                set_transient($op_key, $op, 900);
                wp_send_json_success(tamiyouz_devhub_140_payload($op, 'Remote verified', 'Refreshing persistent sync baseline…', 96, false));
            }

            if ($op['stage'] === 'baseline') {
                $state['base_manifest'] = $op['new_manifest'];
                $state['baseline_at'] = gmdate('c');
                $state['remote_head'] = $op['commit'];
                $state['last_push_at'] = gmdate('c');
                update_option('tamiyouz_devhub_103_state', $state, false);
                delete_transient('tamiyouz_devhub_130_push_' . $uid);
                $op['stage'] = 'done';
                $op['progress'] = 100;
                $op['done'] = true;
                set_transient($op_key, $op, 600);
                tamiyouz_devhub_140_audit('success', array('files' => count($op['tree_entries']), 'commit' => $op['commit']));
                wp_send_json_success(tamiyouz_devhub_140_payload($op, 'Push complete', count($op['tree_entries']) . ' files · ' . $op['commit'], 100, true));
            }

            throw new Exception('Unknown Push operation stage.');
        } catch (Exception $e) {
            tamiyouz_devhub_140_audit('failed', array('error' => $e->getMessage()));
            wp_send_json_error(array('message' => $e->getMessage()), 400);
        }
    }
}
add_action('wp_ajax_tamiyouz_devhub_140_push_step', 'tamiyouz_devhub_140_ajax');
