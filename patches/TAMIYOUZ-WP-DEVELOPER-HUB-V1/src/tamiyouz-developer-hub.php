<?php
/**
 * Plugin Name: Tamiyouz Developer Hub
 * Description: TCRM-style GitHub control plane for Tamiyouz WordPress custom MU-plugin source.
 * Version: 1.0.1
 * Author: Tamiyouz
 * Marker: TAMIYOUZ-WP-DEVELOPER-HUB-V1
 * Hotfix: V1.0.1 fatal parse/runtime corrections
 */

if (!defined('ABSPATH')) exit;

final class Tamiyouz_Developer_Hub_V1 {
    const VERSION = '1.0.1';
    const OPTION = 'tamiyouz_devhub_v1_state';
    const AUDIT = 'tamiyouz_devhub_v1_audit';
    const REVIEW_PREFIX = 'tamiyouz_devhub_v1_review_';
    const REVIEW_TTL = 600;
    const API = 'https://api.github.com';
    const MAX_FILE = 2_000_000;

    public static function boot() {
        add_action('admin_menu', [__CLASS__, 'menu']);
        add_action('rest_api_init', [__CLASS__, 'routes']);
    }

    private static function allowed() { return current_user_can('manage_options'); }

    public static function menu() {
        add_menu_page('Developer Hub', 'Developer Hub', 'manage_options', 'tamiyouz-developer-hub', [__CLASS__, 'page'], 'dashicons-editor-code', 3);
    }

    private static function defaults() {
        return [
            'token' => '', 'repo' => '', 'branch' => '', 'prefix' => 'site',
            'login' => '', 'permission' => '', 'verified_at' => '', 'last_sync_at' => '',
            'last_sync_head' => '', 'base_manifest' => [],
        ];
    }

    private static function state() {
        return array_merge(self::defaults(), is_array(get_option(self::OPTION)) ? get_option(self::OPTION) : []);
    }

    private static function save_state($patch) {
        $next = array_merge(self::state(), $patch);
        update_option(self::OPTION, $next, false);
        return $next;
    }

    private static function key() { return hash('sha256', wp_salt('auth') . '|tamiyouz-devhub-v1', true); }

    private static function encrypt($plain) {
        if (!$plain) return '';
        $iv = random_bytes(12); $tag = '';
        $cipher = openssl_encrypt($plain, 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($cipher === false) throw new Exception('Token encryption failed.');
        return base64_encode($iv . $tag . $cipher);
    }

    private static function decrypt($payload) {
        if (!$payload) return '';
        $raw = base64_decode($payload, true);
        if ($raw === false || strlen($raw) < 29) throw new Exception('Stored GitHub token is invalid.');
        $iv = substr($raw, 0, 12); $tag = substr($raw, 12, 16); $cipher = substr($raw, 28);
        $plain = openssl_decrypt($cipher, 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($plain === false) throw new Exception('Stored GitHub token could not be decrypted.');
        return $plain;
    }

    private static function token() { return self::decrypt(self::state()['token']); }

    private static function repo_parts($repo) {
        $repo = trim((string)$repo);
        $repo = preg_replace('#^https://github\.com/#i', '', $repo);
        $repo = preg_replace('#\.git$#', '', $repo);
        if (!preg_match('#^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$#', $repo)) throw new Exception('Invalid GitHub repository.');
        return $repo;
    }

    private static function request($method, $path, $body = null, $token = null) {
        $token = $token !== null ? $token : self::token();
        if (!$token) throw new Exception('GitHub token is not configured.');
        $args = [
            'method' => $method, 'timeout' => 25,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent' => 'Tamiyouz-WordPress-Developer-Hub/1.0',
            ],
        ];
        if ($body !== null) { $args['body'] = wp_json_encode($body); $args['headers']['Content-Type'] = 'application/json'; }
        $res = wp_remote_request(self::API . $path, $args);
        if (is_wp_error($res)) throw new Exception($res->get_error_message());
        $code = wp_remote_retrieve_response_code($res);
        $raw = wp_remote_retrieve_body($res);
        $json = $raw !== '' ? json_decode($raw, true) : [];
        if ($code < 200 || $code >= 300) {
            $msg = is_array($json) && !empty($json['message']) ? $json['message'] : ('GitHub HTTP ' . $code);
            throw new Exception($msg);
        }
        return $json;
    }

    private static function git_blob_sha($data) { return sha1('blob ' . strlen($data) . "\0" . $data); }

    private static function safe_rel($path) {
        $path = str_replace('\\', '/', ltrim((string)$path, '/'));
        if ($path === '' || strpos($path, '..') !== false || preg_match('#(^|/)(\.env|uploads|cache|backups|storage)(/|$)#i', $path)) return false;
        if (preg_match('#(^|/)tamiyouz-developer-hub(?:\.php|/|$)#i', $path)) return false;
        if (!preg_match('#^wp-content/mu-plugins/tamiyouz-[A-Za-z0-9._/-]+$#', $path)) return false;
        if (!preg_match('#\.(php|css|js|json|md|txt|svg)$#i', $path)) return false;
        return true;
    }

    private static function managed_files() {
        $root = WP_CONTENT_DIR . '/mu-plugins';
        $out = [];
        if (!is_dir($root)) return $out;
        $scan = function($abs, $rel) use (&$scan, &$out) {
            if (!file_exists($abs) || is_link($abs)) return;
            if (is_file($abs)) {
                $logical = 'wp-content/mu-plugins/' . ltrim($rel, '/');
                if (!self::safe_rel($logical)) return;
                $size = filesize($abs); if ($size === false || $size > self::MAX_FILE) return;
                $data = file_get_contents($abs); if ($data === false) return;
                $out[$logical] = ['sha' => self::git_blob_sha($data), 'data' => $data, 'size' => strlen($data)];
                return;
            }
            foreach (scandir($abs) ?: [] as $name) {
                if ($name === '.' || $name === '..') continue;
                $scan($abs . '/' . $name, trim($rel . '/' . $name, '/'));
            }
        };
        foreach (scandir($root) ?: [] as $name) {
            if ($name === '.' || $name === '..') continue;
            if (!preg_match('/^tamiyouz-/i', $name) || preg_match('/^tamiyouz-developer-hub(?:\.php)?$/i', $name)) continue;
            $scan($root . '/' . $name, $name);
        }
        ksort($out); return $out;
    }

    private static function remote_snapshot($repo, $branch, $prefix) {
        $repo = self::repo_parts($repo); $branch = trim($branch); $prefix = trim($prefix, '/');
        $ref = null; $head = ''; $tree_sha = '';
        try {
            $ref = self::request('GET', '/repos/' . $repo . '/git/ref/heads/' . rawurlencode($branch));
            $head = $ref['object']['sha'] ?? '';
        } catch (Throwable $e) {
            return ['head' => '', 'tree' => '', 'files' => [], 'empty' => true];
        }
        $commit = self::request('GET', '/repos/' . $repo . '/git/commits/' . $head);
        $tree_sha = $commit['tree']['sha'] ?? '';
        $tree = self::request('GET', '/repos/' . $repo . '/git/trees/' . $tree_sha . '?recursive=1');
        $files = [];
        foreach (($tree['tree'] ?? []) as $item) {
            if (($item['type'] ?? '') !== 'blob') continue;
            $path = (string)($item['path'] ?? '');
            $logical = $prefix && strpos($path, $prefix . '/') === 0 ? substr($path, strlen($prefix) + 1) : ($prefix ? '' : $path);
            if (!$logical || !self::safe_rel($logical)) continue;
            $files[$logical] = ['sha' => (string)$item['sha'], 'repo_path' => $path, 'size' => (int)($item['size'] ?? 0)];
        }
        ksort($files);
        return ['head' => $head, 'tree' => $tree_sha, 'files' => $files, 'empty' => false];
    }

    private static function classify($local, $remote, $base) {
        $paths = array_unique(array_merge(array_keys($local), array_keys($remote), array_keys($base));
        sort($paths); $items = []; $counts = ['synced'=>0,'local_change'=>0,'remote_change'=>0,'local_only'=>0,'remote_only'=>0,'conflict'=>0];
        foreach ($paths as $p) {
            $l = $local[$p]['sha'] ?? null; $r = $remote[$p]['sha'] ?? null; $b = $base[$p] ?? null;
            if ($l && $r && hash_equals($l, $r)) $status = 'synced';
            elseif (!$b) $status = $l && !$r ? 'local_only' : (!$l && $r ? 'remote_only' : 'conflict');
            else {
                $lc = $l !== $b; $rc = $r !== $b;
                if ($lc && $rc && $l !== $r) $status = 'conflict';
                elseif ($lc) $status = 'local_change';
                elseif ($rc) $status = 'remote_change';
                else $status = 'synced';
            }
            $counts[$status]++; $items[] = ['path'=>$p,'status'=>$status,'local_sha'=>$l,'remote_sha'=>$r,'base_sha'=>$b];
        }
        return [$items, $counts];
    }

    private static function review($action) {
        $state = self::state();
        if (!$state['repo'] || !$state['branch'] || !$state['verified_at']) throw new Exception('Select and verify GitHub repository and branch first.');
        if (!in_array($action, ['push','pull','sync'], true)) throw new Exception('Invalid review action.');
        $local = self::managed_files(); $remote = self::remote_snapshot($state['repo'], $state['branch'], $state['prefix']);
        [$items, $counts] = self::classify($local, $remote['files'], is_array($state['base_manifest']) ? $state['base_manifest'] : []);
        $blocked = [];
        foreach ($items as $i) if ($i['status'] === 'conflict' && $action === 'sync') $blocked[] = ['path'=>$i['path'],'reason'=>'Both local and GitHub changed this file.'];
        $fingerprint = hash('sha256', wp_json_encode([$action,$state['repo'],$state['branch'],$state['prefix'],$remote['head'],array_map(fn($v)=>$v['sha'],$local),array_map(fn($v)=>$v['sha'],$remote['files']]));
        set_transient(self::REVIEW_PREFIX . get_current_user_id(), ['fingerprint'=>$fingerprint,'action'=>$action,'head'=>$remote['head'],'created'=>time()], self::REVIEW_TTL);
        self::audit('review_' . $action, $blocked ? 'blocked' : 'success', ['counts'=>$counts,'blocked'=>$blocked]);
        return ['action'=>$action,'repo'=>$state['repo'],'branch'=>$state['branch'],'remoteHead'=>$remote['head'],'fingerprint'=>$fingerprint,'expiresIn'=>self::REVIEW_TTL,'files'=>$items,'counts'=>$counts,'blocked'=>$blocked,'expectedAction'=>$blocked?'blocked':(($counts['local_change']+$counts['local_only']+$counts['remote_change']+$counts['remote_only'])?'execute':'noop')];
    }

    private static function ensure_review($action, $fingerprint) {
        $review = get_transient(self::REVIEW_PREFIX . get_current_user_id());
        if (!$review || !hash_equals((string)$review['fingerprint'], (string)$fingerprint) || $review['action'] !== $action) throw new Exception('Review expired or does not match. Review again.');
        $fresh = self::review($action);
        if (!hash_equals($fresh['fingerprint'], $fingerprint)) throw new Exception('Project or GitHub changed after review. Review again.');
        if (!empty($fresh['blocked'])) throw new Exception('Operation is blocked by conflicts.');
        return $fresh;
    }

    private static function backup($logical) {
        $abs = ABSPATH . $logical; if (!is_file($abs)) return;
        $target = WP_CONTENT_DIR . '/tamiyouz-devhub-backups/' . gmdate('Ymd-His') . '/' . $logical;
        wp_mkdir_p(dirname($target)); copy($abs, $target);
    }

    private static function fetch_blob_text($repo, $sha) {
        $blob = self::request('GET', '/repos/' . self::repo_parts($repo) . '/git/blobs/' . $sha);
        $data = base64_decode(str_replace("\n", '', (string)($blob['content'] ?? '')), true);
        if ($data === false || strlen($data) > self::MAX_FILE || strpos($data, "\0") !== false) throw new Exception('Remote file is not a safe text source file.');
        return $data;
    }

    private static function pull_files($state, $remote, $items, $action) {
        $pulled = 0;
        foreach ($items as $i) {
            $take = in_array($i['status'], ['remote_only','remote_change'], true) || ($action === 'pull' && $i['status'] === 'conflict');
            if (!$take || !$i['remote_sha'] || !self::safe_rel($i['path'])) continue;
            $data = self::fetch_blob_text($state['repo'], $i['remote_sha']);
            $abs = ABSPATH . $i['path']; self::backup($i['path']); wp_mkdir_p(dirname($abs));
            if (file_put_contents($abs, $data, LOCK_EX) === false) throw new Exception('Failed writing ' . $i['path']);
            $pulled++;
        }
        return $pulled;
    }

    private static function push_files($state, $remote, $items, $action, $message) {
        $repo = self::repo_parts($state['repo']); $prefix = trim($state['prefix'], '/'); $local = self::managed_files();
        $tree = [];
        foreach ($items as $i) {
            $take = in_array($i['status'], ['local_only','local_change'], true) || ($action === 'push' && $i['status'] === 'conflict');
            if (!$take || empty($local[$i['path']])) continue;
            $blob = self::request('POST', '/repos/' . $repo . '/git/blobs', ['content'=>base64_encode($local[$i['path']]['data']),'encoding'=>'base64']);
            $tree[] = ['path'=>($prefix ? $prefix . '/' : '') . $i['path'], 'mode'=>'100644','type'=>'blob','sha'=>$blob['sha']];
        }
        if (!$tree) return ['pushed'=>0,'commit'=>''];
        $tree_result = self::request('POST', '/repos/' . $repo . '/git/trees', array_filter(['base_tree'=>$remote['tree'] ?: null,'tree'=>$tree]));
        $commit_body = ['message'=>$message ?: ('Tamiyouz Developer Hub ' . strtoupper($action) . ' — ' . gmdate('c')),'tree'=>$tree_result['sha']];
        if ($remote['head']) $commit_body['parents'] = [$remote['head']];
        $commit = self::request('POST', '/repos/' . $repo . '/git/commits', $commit_body);
        if ($remote['head']) self::request('PATCH', '/repos/' . $repo . '/git/refs/heads/' . rawurlencode($state['branch']), ['sha'=>$commit['sha'],'force'=>false]);
        else self::request('POST', '/repos/' . $repo . '/git/refs', ['ref'=>'refs/heads/' . $state['branch'],'sha'=>$commit['sha']]);
        return ['pushed'=>count($tree),'commit'=>$commit['sha']];
    }

    private static function execute($action, $fingerprint, $message) {
        self::ensure_review($action, $fingerprint); $state = self::state();
        $local = self::managed_files(); $remote = self::remote_snapshot($state['repo'],$state['branch'],$state['prefix']);
        [$items] = self::classify($local,$remote['files'],is_array($state['base_manifest'])?$state['base_manifest']:[]);
        $pulled = 0; $pushed = 0; $commit = '';
        if (in_array($action, ['pull','sync'], true)) $pulled = self::pull_files($state,$remote,$items,$action);
        if (in_array($action,['push','sync'],true)) { $r = self::push_files($state,$remote,$items,$action,$message); $pushed=$r['pushed']; $commit=$r['commit']; }
        $fresh = self::remote_snapshot($state['repo'],$state['branch'],$state['prefix']);
        $base = []; foreach ($fresh['files'] as $p=>$v) $base[$p]=$v['sha'];
        self::save_state(['base_manifest'=>$base,'last_sync_at'=>gmdate('c'),'last_sync_head'=>$fresh['head']]);
        delete_transient(self::REVIEW_PREFIX . get_current_user_id());
        self::audit('execute_' . $action, 'success', ['pulled'=>$pulled,'pushed'=>$pushed,'commit'=>$commit]);
        return ['ok'=>true,'pulled'=>$pulled,'pushed'=>$pushed,'commitSha'=>$commit,'head'=>$fresh['head']];
    }

    private static function audit($action, $result, $meta = []) {
        $items = get_option(self::AUDIT, []); if (!is_array($items)) $items=[];
        array_unshift($items, ['at'=>gmdate('c'),'user'=>wp_get_current_user()->user_login,'action'=>$action,'result'=>$result,'meta'=>$meta]);
        update_option(self::AUDIT, array_slice($items,0,100), false);
    }

    public static function routes() {
        $ns = 'tamiyouz-devhub/v1'; $perm = fn()=>self::allowed();
        register_rest_route($ns,'/status',['methods'=>'GET','permission_callback'=>$perm,'callback'=>function(){ $s=self::state(); unset($s['token'],$s['base_manifest']); return rest_ensure_response(['ok'=>true,'connected'=>!empty($s['verified_at']),'tokenSet'=>!empty(self::state()['token']),'status'=>$s,'localFiles'=>count(self::managed_files())]); }]);
        register_rest_route($ns,'/connect',['methods'=>'POST','permission_callback'=>$perm,'callback'=>function($r){ try { $token=trim((string)$r['token']); if(strlen($token)<20) throw new Exception('Invalid token.'); $u=self::request('GET','/user',null,$token); $s=self::save_state(['token'=>self::encrypt($token),'login'=>$u['login']??'','verified_at'=>gmdate('c')]); self::audit('github_connect','success',['login'=>$s['login']]); return ['ok'=>true,'login'=>$s['login']]; } catch(Throwable $e){ return new WP_Error('devhub',$e->getMessage(),['status'=>400]); }}]);
        register_rest_route($ns,'/disconnect',['methods'=>'POST','permission_callback'=>$perm,'callback'=>function(){ self::save_state(['token'=>'','repo'=>'','branch'=>'','login'=>'','permission'=>'','verified_at'=>'','base_manifest'=>[]]); self::audit('github_disconnect','success'); return ['ok'=>true]; }]);
        register_rest_route($ns,'/repos',['methods'=>'GET','permission_callback'=>$perm,'callback'=>function(){ try { $repos=self::request('GET','/user/repos?per_page=100&sort=updated&affiliation=owner,collaborator,organization_member'); $out=[]; foreach($repos as $x)$out[]=['fullName'=>$x['full_name'],'private'=>(bool)$x['private'],'defaultBranch'=>$x['default_branch']??'main','push'=>in_array(($x['permissions']['push']??false),[true,1],true)]; return ['repositories'=>$out]; }catch(Throwable $e){return new WP_Error('devhub',$e->getMessage(),['status'=>400]);}}]);
        register_rest_route($ns,'/branches',['methods'=>'GET','permission_callback'=>$perm,'callback'=>function($r){ try{$repo=self::repo_parts($r['repo']);$b=self::request('GET','/repos/'.$repo.'/branches?per_page=100');return ['branches'=>array_values(array_filter(array_map(fn($x)=>$x['name']??'',$b)))];}catch(Throwable $e){return new WP_Error('devhub',$e->get_message(),['status'=>400]);}}]);
        register_rest_route($ns,'/selection',['methods'=>'POST','permission_callback'=>$perm,'callback'=>function($r){ try{$repo=self::repo_parts($r['repo']);$branch=preg_replace('/[^A-Za-z0-9._\\/-]/','',(string)$r['branch']);$prefix=trim(preg_replace('#[^A-Za-z0-9._/-]#','',(string)($r['prefix']?:'site')),'/');$meta=self::request('GET','/repos/'.$repo);$perm=!empty($meta['permissions']['push'])?'push':'pull';self::save_state(['repo'=>$repo,'branch'=>$branch,'prefix'=>$prefix,'permission'=>$perm,'verified_at'=>gmdate('c'),'base_manifest'=>[]]);self::audit('selection_saved','success',['repo'=>$repo,'branch'=>$branch]);return ['ok'=>true,'repo'=>$repo,'branch'=>$branch,'prefix'=>$prefix,'permission'=>$perm];}catch(Throwable $e){return new WP_Error('devhub',$e->get_message(),['status'=>400]);}}]);
        register_rest_route($ns,'/review',['methods'=>'POST','permission_callback'=>$perm,'callback'=>function($r){try{return ['ok'=>true,'preview'=>self::review((string)$r['action'])];}catch(Throwable $e){return new WP_Error('devhub',$e->get_message(),['status'=>400]);}}]);
        register_rest_route($ns,'/execute',['methods'=>'POST','permission_callback'=>$perm,'callback'=>function($r){try{return self::execute((string)$r['action'],(string)$r['fingerprint'],sanitize_text_field((string)$r['commitMessage']));}catch(Throwable $e){self::audit('execute_'.sanitize_key((string)$r['action']),'failed',['error'=>$e->getMessage()]);return new WP_Error('devhub',$e->get_message(),['status'=>400]);}}]);
        register_rest_route($ns,'/audit',['methods'=>'GET','permission_callback'=>$perm,'callback'=>fn()=>['items'=>array_slice((array)get_option(self::AUDIT,[]),0,50)]]);
    }

    public static function page() {
        if (!self::allowed()) return;
        $nonce = wp_create_nonce('wp_rest');
        ?>
        <div class="wrap tyz-devhub" dir="rtl">
          <div class="tyz-head"><div><h1>Developer Hub</h1><p>GitHub control plane للموقع Tamiyouz — Review first, execute second.</p></div><span id="tyz-state" class="tyz-pill">Loading…</span></div>
          <div class="tyz-grid two">
            <section class="tyz-card"><h2>GitHub Connection</h2><label>Personal Access Token</label><div class="row"><input id="tyz-token" type="password" placeholder="github_pat_…"><button class="button button-primary" id="tyz-connect">Connect</button></div><div class="row top"><button class="button" id="tyz-disconnect">Disconnect</button><button class="button" id="tyz-refresh">Refresh</button></div><div id="tyz-login" class="muted top"></div></section>
            <section class="tyz-card"><h2>Repository & Branch</h2><label>Repository</label><select id="tyz-repo"><option value="">—</option></select><label>Branch</label><select id="tyz-branch"><option value="">—</option></select><label>Repository prefix</label><input id="tyz-prefix" value="site"><button class="button button-primary top" id="tyz-save">Save selection</button></section>
          </div>
          <section class="tyz-card"><div class="tyz-section-title"><div><h2>Synchronization</h2><p>راجع الفروقات قبل أي كتابة على GitHub أو السيرفر.</p></div><div class="actions"><button class="button" data-review="push">Review Push</button><button class="button" data-review="pull">Review Pull</button><button class="button" data-review="sync">Review Sync</button></div></div><div id="tyz-summary" class="tyz-stats"></div></section>
          <section class="tyz-card" id="tyz-review" hidden><div class="tyz-section-title"><h2>Review & Execute</h2><span id="tyz-review-badge" class="tyz-pill"></span></div><div id="tyz-blocked"></div><div class="table-wrap"><table class="widefat striped"><thead><tr><th>Direction</th><th>Status</th><th>File</th></tr></thead><tbody id="tyz-files"></tbody></table></div><label>Commit message — optional</label><input id="tyz-message" placeholder="Brief description"><div class="row top"><button class="button button-primary" id="tyz-execute">Execute approved action</button><button class="button" id="tyz-clear">Clear</button></div></section>
          <section class="tyz-card"><div class="tyz-section-title"><h2>Audit</h2><button class="button" id="tyz-audit-refresh">Refresh</button></div><div id="tyz-audit" class="audit"></div></section>
        </div>
        <style>
        .tyz-devhub{max-width:1250px}.tyz-head,.tyz-section-title,.row{display:flex;align-items:center;justify-content:space-between;gap:12px}.tyz-head{margin:22px 0}.tyz-head h1{margin:0;font-size:30px}.tyz-head p,.muted,.tyz-section-title p{color:#6b7280}.tyz-grid.two{display:grid;grid-template-columns:1fr 1fr;gap:18px}.tyz-card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:20px;margin:0 0 18px;box-shadow:0 10px 30px rgba(15,23,42,.04)}.tyz-card h2{margin-top:0}.tyz-card label{display:block;font-weight:600;margin:12px 0 6px}.tyz-card input,.tyz-card select{width:100%;min-height:40px}.row input{flex:1}.row .button{flex:none}.top{margin-top:14px}.tyz-pill{display:inline-flex;align-items:center;padding:5px 10px;border-radius:999px;background:#f3f4f6;font-weight:700;font-size:12px}.tyz-pill.ok{background:#ecfdf5;color:#047857}.tyz-pill.bad{background:#fef2f2;color:#b91c1c}.tyz-stats{display:grid;grid-template-columns:repeat(6,1fr);gap:10px;margin-top:15px}.tyz-stat{border:1px solid #e5e7eb;border-radius:12px;padding:12px}.tyz-stat small{display:block;color:#6b7280}.tyz-stat b{font-size:20px}.actions{display:flex;gap:8px;flex-wrap:wrap}.table-wrap{max-height:430px;overflow:auto;margin:15px 0}.tyz-dir-local{color:#0369a1}.tyz-dir-remote{color:#7c3aed}.tyz-dir-conflict{color:#b91c1c;font-weight:800}.tyz-block{padding:12px;border-radius:12px;background:#fef2f2;color:#991b1b;margin:12px 0}.audit{display:grid;gap:8px}.audit-item{display:grid;grid-template-columns:170px 1fr 110px;gap:10px;padding:10px;border-bottom:1px solid #eee;font-family:monospace;font-size:12px}@media(max-width:900px){.tyz-grid.two{grid-template-columns:1fr}.tyz-stats{grid-template-columns:repeat(2,1fr);}.tyz-head,.tyz-section-title{align-items:flex-start;flex-direction:column}}
        </style>
        <script>
        (()=>{const base=<?php echo wp_json_encode(rest_url('tamiyouz-devhub/v1')); ?>,nonce=<?php echo wp_json_encode($nonce); ?>,$=s=>document.querySelector(s),$$=s=>[...document.querySelectorAll(s)];let status=null,preview=null;
        async function api(path,opt={}){const r=await fetch(base+path,{credentials:'same-origin',headers:{'X-WP-Nonce':nonce,'Content-Type':'application/json',...(opt.headers||{})},...opt});const j=await r.json().catch(()=>({}));if(!r.ok)throw new Error(j.message||j.code||'Request failed');return j}
        function toast(m){window.alert(m)}
        async function load(){const r=await api('/status');status=r.status;$('#tyz-state').textContent=r.connected?'GitHub Connected':'Disconnected';$('#tyz-state').className='tyz-pill '+(r.connected?'ok':'bad');$('#tyz-login').textContent=status.login?('@'+status.login+' · '+(status.permission||'')):'';$('#tyz-prefix').value=status.prefix||'site';await repos();await audit()}
        async function repos(){if(!status?.verified_at)return;const r=await api('/repos');$('#tyz-repo').innerHTML='<option value="">اختر المستودع</option>'+r.repositories.map(x=>`<option value="${x.fullName}">${x.fullName}${x.private?' · Private':''}</option>`).join('');if(status.repo){$('#tyz-repo').value=status.repo;await branches(status.repo);$('#tyz-branch').value=status.branch||''}}
        async function branches(repo){if(!repo)return;const r=await api('/branches?repo='+encodeURIComponent(repo));$('#tyz-branch').innerHTML='<option value="">اختر الفرع</option>'+r.branches.map(x=>`<option>${x}</option>`).join('')}
        $('#tyz-connect').onclick=async()=>{try{await api('/connect',{method:'POST',body:JSON.stringify({token:$('#tyz-token').value})});$('#tyz-token').value='';await load()}catch(e){toast(e.message)}};$('#tyz-disconnect').onclick=async()=>{if(confirm('Disconnect GitHub?')){await api('/disconnect',{method:'POST',body:'{}'});location.reload()}};$('#tyz-refresh').onclick=load;$('#tyz-repo').onchange=e=>branches(e.target.value);$('#tyz-save').onclick=async()=>{try{await api('/selection',{method:'POST',body:JSON.stringify({repo:$('#tyz-repo').value,branch:$('#tyz-branch').value,prefix:$('#tyz-prefix').value})});await load();toast('Saved')}catch(e){toast(e.message)}};
        $$('.button[data-review]').forEach(b=>b.onclick=async()=>{try{const r=await api('/review',{method:'POST',body:JSON.stringify({action:b.dataset.review})});preview=r.preview;render()}catch(e){toast(e.message)}});
        function render(){const p=preview;if(!p)return;$('#tyz-review').hidden=false;$('#tyz-review-badge').textContent=p.blocked.length?'Blocked':'Ready';$('#tyz-review-badge').className='tyz-pill '+(p.blocked.length?'bad':'ok');const c=p.counts;$('#tyz-summary').innerHTML=Object.entries(c).map(([k,v])=>`<div class="tyz-stat"><small>${k}</small><b>${v}</b></div>`).join('');$('#tyz-blocked').innerHTML=p.blocked.length?`<div class="tyz-block">${p.blocked.map(x=>x.path+' — '+x.reason).join('<br>')}</div>`:'';$('#tyz-files').innerHTML=p.files.filter(x=>x.status!=='synced').map(x=>{let d=x.status==='conflict'?['Conflict','tyz-dir-conflict']:(['local_only','local_change'].includes(x.status)?['Local → GitHub','tyz-dir-local']:['GitHub → Local','tyz-dir-remote']);return `<tr><td class="${d[1]}">${d[0]}</td><td>${x.status}</td><td><code>${x.path}</code></td></tr>`}).join('')||'<tr><td colspan="3">No changed files.</td></tr>';$('#tyz-execute').disabled=!!p.blocked.length||p.expectedAction==='noop';$('#tyz-review').scrollIntoView({behavior:'smooth'})}
        $('#tyz-execute').onclick=async()=>{if(!preview||!confirm('Execute reviewed '+preview.action.toUpperCase()+'?'))return;try{const r=await api('/execute',{method:'POST',body:JSON.stringify({action:preview.action,fingerprint:preview.fingerprint,commitMessage:$('#tyz-message').value})});toast(`Done · Push ${r.pushed||0} · Pull ${r.pulled||0}`);preview=null;$('#tyz-review').hidden=true;await load()}catch(e){toast(e.message)}};$('#tyz-clear').onclick=()=>{preview=null;$('#tyz-review').hidden=true};
        async function audit(){const r=await api('/audit');$('#tyz-audit').innerHTML=(r.items||[]).map(x=>`<div class="audit-item"><span>${x.at}</span><code>${x.action}</code><b>${x.result}</b></div>`).join('')||'<p>No audit entries.</p>'}$('#tyz-audit-refresh').onclick=audit;load().catch(e=>toast(e.message));})();
        </script>
        <?php
    }
}
Tamiyouz_Developer_Hub_V1::boot();
