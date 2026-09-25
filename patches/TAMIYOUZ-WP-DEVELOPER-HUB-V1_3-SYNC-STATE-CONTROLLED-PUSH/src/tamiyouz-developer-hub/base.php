<?php
// Marker: TAMIYOUZ-WP-DEVELOPER-HUB-V1_3-SYNC-STATE-CONTROLLED-PUSH
if (!defined('ABSPATH') || !current_user_can('manage_options')) {
    exit;
}

$option_key = 'tamiyouz_devhub_103_state';
$notice = '';
$notice_type = 'success';

function tamiyouz_devhub_103_state() {
    $state = get_option('tamiyouz_devhub_103_state', array());
    return is_array($state) ? $state : array();
}

function tamiyouz_devhub_103_secret_key() {
    $seed = defined('AUTH_KEY') ? AUTH_KEY : ABSPATH;
    return hash('sha256', $seed . '|tamiyouz-devhub-103', true);
}

function tamiyouz_devhub_103_encrypt($plain) {
    if ($plain === '') {
        return '';
    }
    if (!function_exists('openssl_encrypt') || !function_exists('openssl_random_pseudo_bytes')) {
        throw new Exception('OpenSSL is unavailable on this PHP runtime.');
    }
    $iv = openssl_random_pseudo_bytes(16);
    if ($iv === false || strlen($iv) !== 16) {
        throw new Exception('Could not generate an encryption IV.');
    }
    $cipher = openssl_encrypt($plain, 'AES-256-CBC', tamiyouz_devhub_103_secret_key(), OPENSSL_RAW_DATA, $iv);
    if ($cipher === false) {
        throw new Exception('Token encryption failed.');
    }
    $mac = hash_hmac('sha256', $iv . $cipher, tamiyouz_devhub_103_secret_key(), true);
    return base64_encode($iv . $mac . $cipher);
}

function tamiyouz_devhub_103_decrypt($payload) {
    if (!$payload) {
        return '';
    }
    $raw = base64_decode($payload, true);
    if ($raw === false || strlen($raw) < 49) {
        throw new Exception('Stored GitHub token is invalid.');
    }
    $iv = substr($raw, 0, 16);
    $mac = substr($raw, 16, 32);
    $cipher = substr($raw, 48);
    $expected = hash_hmac('sha256', $iv . $cipher, tamiyouz_devhub_103_secret_key(), true);
    if (!hash_equals($expected, $mac)) {
        throw new Exception('Stored GitHub token integrity check failed.');
    }
    $plain = openssl_decrypt($cipher, 'AES-256-CBC', tamiyouz_devhub_103_secret_key(), OPENSSL_RAW_DATA, $iv);
    if ($plain === false) {
        throw new Exception('Stored GitHub token could not be decrypted.');
    }
    return $plain;
}

function tamiyouz_devhub_103_github($token, $path) {
    $response = wp_remote_get('https://api.github.com' . $path, array(
        'timeout' => 20,
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'User-Agent' => 'Tamiyouz-WordPress-Developer-Hub/1.0.3'
        )
    ));
    if (is_wp_error($response)) {
        throw new Exception($response->get_error_message());
    }
    $code = wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);
    if ($code < 200 || $code >= 300) {
        $message = is_array($body) && isset($body['message']) ? $body['message'] : ('GitHub HTTP ' . $code);
        throw new Exception($message);
    }
    return is_array($body) ? $body : array();
}

function tamiyouz_devhub_111_github_request($token, $method, $path, $body, $allow_404) {
    $args = array(
        'method' => $method,
        'timeout' => 20,
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'User-Agent' => 'Tamiyouz-WordPress-Developer-Hub/1.1.2'
        )
    );
    if ($body !== null) {
        $args['body'] = wp_json_encode($body);
        $args['headers']['Content-Type'] = 'application/json';
    }
    $response = wp_remote_request('https://api.github.com' . $path, $args);
    if (is_wp_error($response)) {
        throw new Exception($response->get_error_message());
    }
    $code = (int) wp_remote_retrieve_response_code($response);
    $raw = wp_remote_retrieve_body($response);
    $decoded = $raw !== '' ? json_decode($raw, true) : array();
    if ($code === 404 && $allow_404) {
        return array('__status' => 404);
    }
    if ($code < 200 || $code >= 300) {
        $message = is_array($decoded) && isset($decoded['message']) ? $decoded['message'] : ('GitHub HTTP ' . $code);
        throw new Exception($message);
    }
    if (!is_array($decoded)) {
        $decoded = array();
    }
    $decoded['__status'] = $code;
    return $decoded;
}

function tamiyouz_devhub_111_create_branch($token, $repo, $branch) {
    $repo_meta = tamiyouz_devhub_111_github_request($token, 'GET', '/repos/' . $repo, null, false);
    if (empty($repo_meta['permissions']['push'])) {
        throw new Exception('GitHub token needs Contents write permission for this repository.');
    }

    $existing = tamiyouz_devhub_111_github_request(
        $token,
        'GET',
        '/repos/' . $repo . '/branches/' . rawurlencode($branch),
        null,
        true
    );
    if (!isset($existing['__status']) || $existing['__status'] !== 404) {
        $sha = isset($existing['commit']['sha']) ? sanitize_text_field($existing['commit']['sha']) : '';
        return array('created' => false, 'sha' => $sha, 'empty_repo' => false);
    }

    $default_branch = !empty($repo_meta['default_branch']) ? (string) $repo_meta['default_branch'] : 'main';
    $source = tamiyouz_devhub_111_github_request(
        $token,
        'GET',
        '/repos/' . $repo . '/branches/' . rawurlencode($default_branch),
        null,
        true
    );

    $source_sha = '';
    $empty_repo = false;

    if (isset($source['__status']) && $source['__status'] === 404) {
        $empty_repo = true;

        tamiyouz_devhub_111_github_request(
            $token,
            'PUT',
            '/repos/' . $repo . '/contents/.gitkeep',
            array(
                'message' => 'Initialize Tamiyouz website repository',
                'content' => base64_encode("\n")
            ),
            false
        );

        $source = tamiyouz_devhub_111_github_request(
            $token,
            'GET',
            '/repos/' . $repo . '/branches/' . rawurlencode($default_branch),
            null,
            false
        );

        $source_sha = isset($source['commit']['sha']) ? (string) $source['commit']['sha'] : '';
        if ($source_sha === '') {
            throw new Exception('Repository initialized but the default branch HEAD could not be resolved.');
        }

        if ($branch === $default_branch) {
            return array('created' => true, 'sha' => $source_sha, 'empty_repo' => true);
        }
    } else {
        $source_sha = isset($source['commit']['sha']) ? (string) $source['commit']['sha'] : '';
        if ($source_sha === '') {
            throw new Exception('Could not resolve the source branch HEAD.');
        }
    }

    tamiyouz_devhub_111_github_request(
        $token,
        'POST',
        '/repos/' . $repo . '/git/refs',
        array(
            'ref' => 'refs/heads/' . $branch,
            'sha' => $source_sha
        ),
        false
    );

    return array('created' => true, 'sha' => $source_sha, 'empty_repo' => $empty_repo);
}

function tamiyouz_devhub_103_managed_file_count() {
    $root = WP_CONTENT_DIR . '/mu-plugins';
    if (!is_dir($root)) {
        return 0;
    }
    $count = 0;
    $items = scandir($root);
    if (!is_array($items)) {
        return 0;
    }
    foreach ($items as $name) {
        if ($name === '.' || $name === '..') {
            continue;
        }
        if (strpos(strtolower($name), 'tamiyouz-') !== 0) {
            continue;
        }
        if (strpos(strtolower($name), 'tamiyouz-developer-hub') === 0) {
            continue;
        }
        $count++;
    }
    return $count;
}

$state = tamiyouz_devhub_103_state();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_referer('tamiyouz_devhub_103');
    $action = isset($_POST['devhub_action']) ? sanitize_key(wp_unslash($_POST['devhub_action'])) : '';
    try {
        if ($action === 'connect') {
            $token = isset($_POST['github_token']) ? trim(wp_unslash($_POST['github_token'])) : '';
            if (strlen($token) < 20) {
                throw new Exception('Enter a valid GitHub token.');
            }
            $user = tamiyouz_devhub_103_github($token, '/user');
            $state['token'] = tamiyouz_devhub_103_encrypt($token);
            $state['login'] = isset($user['login']) ? sanitize_text_field($user['login']) : '';
            $state['verified_at'] = gmdate('c');
            update_option($option_key, $state, false);
            $notice = 'GitHub account verified and connected.';
        } elseif ($action === 'disconnect') {
            delete_option($option_key);
            $state = array();
            $notice = 'GitHub disconnected.';
        } elseif ($action === 'save_repo') {
            $repo = isset($_POST['repo']) ? trim(wp_unslash($_POST['repo'])) : '';
            $branch = isset($_POST['branch']) ? trim(wp_unslash($_POST['branch'])) : '';
            if (!preg_match('#^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$#', $repo)) {
                throw new Exception('Repository must be owner/name.');
            }
            if (!preg_match('#^[A-Za-z0-9._/-]+$#', $branch)) {
                throw new Exception('Invalid branch name.');
            }
            if (empty($state['token'])) {
                throw new Exception('Connect GitHub first.');
            }
            $token = tamiyouz_devhub_103_decrypt($state['token']);
            $repo_meta = tamiyouz_devhub_103_github($token, '/repos/' . $repo);
            tamiyouz_devhub_103_github($token, '/repos/' . $repo . '/branches/' . rawurlencode($branch));
            $state['repo'] = $repo;
            $state['branch'] = $branch;
            $state['permission'] = !empty($repo_meta['permissions']['push']) ? 'push' : 'pull';
            $state['repo_verified_at'] = gmdate('c');
            update_option($option_key, $state, false);
            $notice = 'Repository and branch verified.';
        } elseif ($action === 'create_branch') {
            $repo = isset($_POST['repo']) ? trim(wp_unslash($_POST['repo'])) : '';
            $branch = isset($_POST['branch']) ? trim(wp_unslash($_POST['branch'])) : '';
            if (!preg_match('#^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$#', $repo)) {
                throw new Exception('Repository must be owner/name.');
            }
            if (!preg_match('#^[A-Za-z0-9._/-]+$#', $branch)) {
                throw new Exception('Invalid branch name.');
            }
            if (empty($state['token'])) {
                throw new Exception('Connect GitHub first.');
            }
            $token = tamiyouz_devhub_103_decrypt($state['token']);
            $created = tamiyouz_devhub_111_create_branch($token, $repo, $branch);
            $state['repo'] = $repo;
            $state['branch'] = $branch;
            $state['permission'] = 'push';
            $state['repo_verified_at'] = gmdate('c');
            $state['remote_head'] = isset($created['sha']) ? sanitize_text_field($created['sha']) : '';
            update_option($option_key, $state, false);
            if (!empty($created['created'])) {
                $notice = !empty($created['empty_repo'])
                    ? 'Empty repository initialized through GitHub Contents API; branch created and saved.'
                    : 'Branch created from the repository default branch and saved.';
            } else {
                $notice = 'Branch already exists and was saved.';
            }
        } elseif ($action === 'refresh_status') {
            if (empty($state['token'])) {
                throw new Exception('Connect GitHub first.');
            }
            $token = tamiyouz_devhub_103_decrypt($state['token']);
            $user = tamiyouz_devhub_103_github($token, '/user');
            $state['login'] = isset($user['login']) ? sanitize_text_field($user['login']) : '';
            $state['verified_at'] = gmdate('c');
            if (!empty($state['repo']) && !empty($state['branch'])) {
                $branch_meta = tamiyouz_devhub_103_github($token, '/repos/' . $state['repo'] . '/branches/' . rawurlencode($state['branch']));
                $state['remote_head'] = isset($branch_meta['commit']['sha']) ? sanitize_text_field($branch_meta['commit']['sha']) : '';
            }
            update_option($option_key, $state, false);
            $notice = 'Status refreshed.';
        }
    } catch (Exception $e) {
        $notice = $e->getMessage();
        $notice_type = 'error';
    }
    $state = tamiyouz_devhub_103_state();
}

$connected = !empty($state['token']) && !empty($state['verified_at']);
$local_count = tamiyouz_devhub_103_managed_file_count();
?>
<div class="wrap tyz-devhub103" dir="rtl">
    <h1>Developer Hub</h1>
    <p class="description">Developer Hub V1.3 — persistent sync baseline + controlled reviewed Push. Pull/Sync execution remains disabled.</p>

    <?php if ($notice !== '') : ?>
        <div class="notice notice-<?php echo esc_attr($notice_type); ?> is-dismissible"><p><?php echo esc_html($notice); ?></p></div>
    <?php endif; ?>

    <div class="tyz-grid">
        <div class="tyz-card">
            <h2>Runtime Safety</h2>
            <p><strong>Status:</strong> <span class="tyz-badge ok">SAFE BOOTSTRAP ACTIVE</span></p>
            <p><strong>Version:</strong> 1.3.0</p>
            <p><strong>Managed Tamiyouz entries:</strong> <?php echo esc_html((string) $local_count); ?></p>
            <p><strong>Write operations:</strong> Branch creation + reviewed controlled Push; Pull/Sync execution stays disabled</p>
        </div>

        <div class="tyz-card">
            <h2>GitHub Connection</h2>
            <?php if ($connected) : ?>
                <p><strong>Connected:</strong> @<?php echo esc_html(isset($state['login']) ? $state['login'] : 'unknown'); ?></p>
                <p><strong>Verified:</strong> <?php echo esc_html($state['verified_at']); ?></p>
                <form method="post" class="tyz-inline">
                    <?php wp_nonce_field('tamiyouz_devhub_103'); ?>
                    <input type="hidden" name="devhub_action" value="refresh_status">
                    <button class="button button-primary">Refresh Status</button>
                </form>
                <form method="post" class="tyz-inline" onsubmit="return confirm('Disconnect GitHub?');">
                    <?php wp_nonce_field('tamiyouz_devhub_103'); ?>
                    <input type="hidden" name="devhub_action" value="disconnect">
                    <button class="button">Disconnect</button>
                </form>
            <?php else : ?>
                <form method="post">
                    <?php wp_nonce_field('tamiyouz_devhub_103'); ?>
                    <input type="hidden" name="devhub_action" value="connect">
                    <label for="github_token"><strong>Personal Access Token</strong></label>
                    <input id="github_token" name="github_token" type="password" class="regular-text" autocomplete="new-password" required>
                    <p><button class="button button-primary">Connect & Verify</button></p>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="tyz-card">
        <h2>Repository & Branch</h2>
        <?php if (!$connected) : ?>
            <p>Connect GitHub first.</p>
        <?php else : ?>
            <form method="post">
                <?php wp_nonce_field('tamiyouz_devhub_103'); ?>
                <table class="form-table" role="presentation">
                    <tr><th><label for="repo">Repository</label></th><td><input id="repo" name="repo" class="regular-text" placeholder="owner/repository" value="<?php echo esc_attr(isset($state['repo']) ? $state['repo'] : ''); ?>" required></td></tr>
                    <tr><th><label for="branch">Branch</label></th><td><input id="branch" name="branch" class="regular-text" value="<?php echo esc_attr(isset($state['branch']) ? $state['branch'] : 'main'); ?>" required></td></tr>
                </table>
                <p class="tyz-branch-actions">
                    <button class="button button-primary" type="submit" name="devhub_action" value="save_repo">Verify & Save</button>
                    <button class="button" type="submit" name="devhub_action" value="create_branch" onclick="return confirm('Create this GitHub branch if it does not exist?');">Create Branch & Save</button>
                </p>
            </form>
        <?php endif; ?>
        <?php if (!empty($state['repo'])) : ?>
            <hr>
            <p><strong>Saved repository:</strong> <?php echo esc_html($state['repo']); ?></p>
            <p><strong>Branch:</strong> <?php echo esc_html(isset($state['branch']) ? $state['branch'] : ''); ?></p>
            <p><strong>Permission:</strong> <?php echo esc_html(isset($state['permission']) ? $state['permission'] : ''); ?></p>
            <p><strong>Remote HEAD:</strong> <code><?php echo esc_html(isset($state['remote_head']) ? $state['remote_head'] : '—'); ?></code></p>
        <?php endif; ?>
    </div>

    <div class="tyz-card">
        <h2>Controlled Sync</h2>
        <p><span class="tyz-badge ok">CONTROLLED PUSH READY</span></p>
        <p>V1.3 tracks a persistent sync baseline and allows only explicitly reviewed safe Push changes to GitHub. Pull and Sync remain read-only.</p>
    </div>
</div>
<style>
.tyz-devhub103{max-width:1180px}.tyz-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.tyz-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;margin:18px 0;box-shadow:0 8px 24px rgba(15,23,42,.05)}.tyz-card h2{margin-top:0}.tyz-inline{display:inline-block;margin-left:8px}.tyz-badge{display:inline-block;padding:5px 9px;border-radius:999px;font-size:12px;font-weight:700}.tyz-badge.ok{background:#ecfdf5;color:#047857}.tyz-badge.hold{background:#fff7ed;color:#c2410c}.tyz-branch-actions{display:flex;gap:8px;flex-wrap:wrap}@media(max-width:900px){.tyz-grid{grid-template-columns:1fr}}
</style>
