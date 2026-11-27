<?php
/*
 * Anope 2.1 JSON-RPC client + normalisation helpers.
 * rpc_data field names moved around between 2.1.x releases,
 * so everything goes through ar_get(), which tries several keys.
 */

require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/ctcp.php';

if (!function_exists('ar_h')) {
	function ar_h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

class AnopeRPCException extends Exception {}

class AnopeRPC
{
	private static $inst = null;
	private $s;
	private $id = 0;
	private $serverHeader = '';

	public static function instance(): AnopeRPC
	{
		return self::$inst ??= new AnopeRPC(self::settings());
	}

	/** Settings: panel (Settings > General), with the plugin's local config.php as fallback */
	public static function settings(): array
	{
		$d = ['url' => 'http://127.0.0.1:8080/jsonrpc', 'token' => '', 'cache' => 30, 'detail' => 'full', 'source' => '', 'lang' => 'auto', 'version' => '',
		      'probe_host' => '', 'probe_port' => 6697, 'probe_tls' => true, 'probe_target' => 'NickServ', 'probe_pass' => ''];
		$local = __DIR__ . '/../config.php';
		if (is_file($local)) {
			$l = include $local;
			if (is_array($l)) $d = array_merge($d, $l);
		}
		$c = function_exists('get_config') ? get_config('anope_rpc') : null;
		if (is_array($c)) {
			foreach ($c as $k => $v) if ($v !== '' && $v !== null) $d[$k] = $v;
			foreach (['token', 'probe_pass'] as $secret) {
				if (!empty($c[$secret]) && function_exists('secret_decrypt')) {
					$dec = @secret_decrypt($c[$secret]);
					$d[$secret] = ($dec !== false && $dec !== null && $dec !== '') ? $dec : $c[$secret];
				}
			}
			if (array_key_exists('probe_tls', $c)) $d['probe_tls'] = (bool)$c['probe_tls'];
		}
		return $d;
	}

	public function __construct(array $s) { $this->s = $s; }

	public function detail(): string { return $this->s['detail'] ?: 'full'; }
	public function source(): string { return $this->s['source'] ?? ''; }

	/**
	 * Detects the running Anope version. There is no documented RPC method for it,
	 * so several sources are tried, the most reliable first:
	 *   1. a dedicated method, if system.listMethods exposes one (anope.version, anope.info, …)
	 *   2. CTCP VERSION to NickServ over a short IRC connection (if a probe host is configured)
	 *   3. the services server as seen by UnrealIRCd (server.get → features.software),
	 *      through the webpanel's own UnrealIRCd JSON-RPC connection
	 *   4. a "version" field on the services server (anope.server on the U-lined server)
	 *   5. the "Server:" header sent by Anope's httpd module
	 *   6. the manual "version" setting
	 * Returns ['version' => '2.1.28' | null, 'raw' => full string, 'via' => source label, 'errors' => [...]]
	 */
	public function version($urpc = null): array
	{
		$f = self::cacheDir() . '/version-' . md5($this->s['url']) . '.json';
		if (is_file($f) && filemtime($f) > time() - 3600) {
			$d = json_decode(file_get_contents($f), true);
			if (is_array($d)) return $d;
		}
		$found = null; $via = ''; $errors = [];
		$pick = function ($v) {
			if (is_array($v)) $v = ar_get($v, ['version', 'versionstring', 'version_string', 'full', 'anope', 'features.software', 'software'], null);
			return (is_string($v) && preg_match('/\d+\.\d+(\.\d+)?/', $v)) ? $v : null;
		};
		$isServices = fn($srv) => ar_get($srv, ['ulined', 'ulined_server', 'server.ulined'], false) || preg_match('/services|anope/i', s_name($srv));

		// 1. dedicated method
		try {
			$methods = $this->cached('system.listMethods', [], 600);
			foreach (['anope.version', 'anope.info', 'system.version', 'rpc.info', 'anope.stats'] as $m) {
				if (is_array($methods) && in_array($m, $methods, true)) {
					try { if ($v = $pick($this->call($m))) { $found = $v; $via = $m; break; } } catch (Throwable $e) {}
				}
			}
		} catch (Throwable $e) {}

		// 2. CTCP VERSION, exactly like /ctcp NickServ VERSION
		if (!$found && trim((string)$this->s['probe_host']) !== '') {
			try {
				$target = trim((string)$this->s['probe_target']) ?: 'NickServ';
				$found = AnopeCTCPProbe::version(trim($this->s['probe_host']), (int)$this->s['probe_port'] ?: 6697,
					(bool)$this->s['probe_tls'], $target, (string)$this->s['probe_pass']);
				$via = "CTCP VERSION $target";
			} catch (Throwable $e) { $errors[] = $e->getMessage(); }
		}

		// 3. UnrealIRCd's view of the services server (SINFO → features.software)
		if (!$found && is_object($urpc) && method_exists($urpc, 'query')) {
			try {
				$names = [];
				foreach ($this->servers() as $srv) if ($isServices($srv)) $names[] = s_name($srv);
				foreach ($names as $n) {
					$r = json_decode(json_encode($urpc->query('server.get', ['server' => $n])), true);
					$sw = ar_get($r, ['server.server.features.software', 'server.features.software', 'features.software'], null);
					if (is_string($sw) && preg_match('/\d+\.\d+\.\d+/', $sw)) { $found = $sw; $via = "UnrealIRCd server.get $n"; break; }
				}
			} catch (Throwable $e) { $errors[] = 'UnrealIRCd: ' . $e->getMessage(); }
		}

		// 4. services server through Anope (prefer a version string that mentions Anope)
		if (!$found) {
			try {
				foreach ($this->servers() as $srv) {
					if (!$isServices($srv)) continue;
					$v = $pick($srv);
					if (!$v) { try { $v = $pick($this->call('anope.server', [s_name($srv)])); } catch (Throwable $e) {} }
					if (!$v) continue;
					if (!$found || stripos($v, 'anope') !== false) { $found = $v; $via = 'anope.server ' . s_name($srv); }
					if (stripos($v, 'anope') !== false) break;
				}
			} catch (Throwable $e) {}
		}

		// 5. HTTP Server header
		if (!$found) {
			try { $this->call('system.listMethods'); } catch (Throwable $e) {}
			if (preg_match('/anope[^\d]*(\d+\.\d+\.\d+\S*)/i', $this->serverHeader)) { $found = $this->serverHeader; $via = 'HTTP Server header'; }
		}

		// 6. manual setting
		if (!$found && !empty($this->s['version'])) { $found = (string)$this->s['version']; $via = 'settings'; }

		preg_match('/(\d+\.\d+\.\d+(?:[-.][0-9A-Za-z]+)?)/', (string)$found, $m);
		$res = ['version' => $m[1] ?? null, 'raw' => $found, 'via' => $via, 'errors' => $errors];
		// Failed detections are retried sooner than successful ones
		@file_put_contents($f, json_encode($res), LOCK_EX);
		if (!$res['version']) @touch($f, time() - 3600 + 300);
		return $res;
	}

	/**
	 * Latest release on the same branch (e.g. 2.1.x), read from anope.org's news feed.
	 * Cached 6 h; returns null when the panel host has no Internet access.
	 */
	public static function latestRelease(string $current): ?string
	{
		if (!preg_match('/^(\d+\.\d+)\./', $current, $b)) return null;
		$f = self::cacheDir() . '/latest-release.json';
		$all = null;
		if (is_file($f) && filemtime($f) > time() - 6 * 3600) $all = json_decode(file_get_contents($f), true);
		if (!is_array($all)) {
			$all = [];
			if (function_exists('curl_init')) {
				$ch = curl_init('https://www.anope.org/feed.xml');
				curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 3, CURLOPT_TIMEOUT => 5, CURLOPT_FOLLOWLOCATION => true, CURLOPT_USERAGENT => 'anope_rpc-webpanel-plugin']);
				$xml = curl_exec($ch);
				curl_close($ch);
				if (is_string($xml) && preg_match_all('/Anope (\d+\.\d+\.\d+) Release/', $xml, $mm)) $all = array_values(array_unique($mm[1]));
			}
			@file_put_contents($f, json_encode($all), LOCK_EX);
		}
		$same = array_filter($all, fn($v) => str_starts_with($v, $b[1] . '.'));
		if (!$same) return null;
		usort($same, 'version_compare');
		return end($same);
	}

	private static function cacheDir(): string
	{
		$d = sys_get_temp_dir() . '/anope_rpc_cache';
		if (!is_dir($d)) @mkdir($d, 0700, true);
		return $d;
	}

	public static function flushCache(): void
	{
		foreach (glob(self::cacheDir() . '/*.json') ?: [] as $f) @unlink($f);
	}

	/** Raw call, no cache */
	public function call(string $method, array $params = [])
	{
		if (!function_exists('curl_init'))
			throw new AnopeRPCException(t('The PHP curl extension is required'));
		$ch = curl_init($this->s['url']);
		$headers = ['Content-Type: application/json', 'Accept: application/json'];
		if ($this->s['token'] !== '')
			$headers[] = 'Authorization: Bearer ' . $this->s['token'];
		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 3,
			CURLOPT_TIMEOUT => 15,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => json_encode(['jsonrpc' => '2.0', 'method' => $method, 'params' => array_values($params), 'id' => ++$this->id]),
			CURLOPT_HEADERFUNCTION => function ($ch, $line) {
				if (stripos($line, 'Server:') === 0) $this->serverHeader = trim(substr($line, 7));
				return strlen($line);
			},
		]);
		$body = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);
		curl_close($ch);
		if ($body === false)
			throw new AnopeRPCException(t('Unable to connect to %s: %s', $this->s['url'], $err));
		if ($code === 401 || $code === 403)
			throw new AnopeRPCException(t('Access denied by Anope (HTTP %d): check the token', $code));
		$r = json_decode($body, true);
		if (!is_array($r))
			throw new AnopeRPCException(t('Non-JSON response (HTTP %d)', $code));
		if (isset($r['error']))
			throw new AnopeRPCException(($r['error']['message'] ?? t('RPC error')) . ' (code ' . ($r['error']['code'] ?? '?') . ')');
		return $r['result'] ?? null;
	}

	/** Call with a short file cache */
	public function cached(string $method, array $params = [], ?int $ttl = null)
	{
		$ttl ??= (int)$this->s['cache'];
		if ($ttl <= 0) return $this->call($method, $params);
		$f = self::cacheDir() . '/' . md5($this->s['url'] . $method . json_encode($params)) . '.json';
		if (is_file($f) && filemtime($f) > time() - $ttl) {
			$d = json_decode(file_get_contents($f), true);
			if (is_array($d) && array_key_exists('v', $d)) return $d['v'];
		}
		$v = $this->call($method, $params);
		@file_put_contents($f, json_encode(['v' => $v]), LOCK_EX);
		return $v;
	}

	/** Simple list (names) */
	public function listRaw(string $method): array
	{
		$r = $this->cached($method);
		return is_array($r) ? array_values(ar_is_list($r) ? $r : array_keys($r)) : [];
	}

	/**
	 * Detailed list: tries the configured detail level, otherwise
	 * falls back to one call per item (capped).
	 */
	public function listFull(string $listMethod, string $oneMethod, int $cap = 500): array
	{
		try {
			$r = $this->cached($listMethod, [$this->detail()]);
		} catch (AnopeRPCException $e) {
			$r = $this->cached($listMethod);
		}
		if (!is_array($r)) return [];
		$out = [];
		foreach ($r as $k => $v) {
			if (is_array($v)) {
				if (!is_int($k) && !isset($v['name']) && !isset($v['nick']) && !isset($v['display']))
					$v['name'] = $k;
				$out[] = $v;
			}
		}
		if ($out) return $out;
		// No detail: fetch items one by one
		$names = ar_is_list($r) ? $r : array_keys($r);
		foreach (array_slice($names, 0, $cap) as $n) {
			try {
				$d = $this->cached($oneMethod, [(string)$n]);
				if (is_array($d)) $out[] = $d + ['name' => $n];
			} catch (AnopeRPCException $e) {}
		}
		return $out;
	}

	public function users(): array    { return $this->listFull('anope.listUsers', 'anope.user'); }
	public function channels(): array { return $this->listFull('anope.listChannels', 'anope.channel'); }
	public function servers(): array  { return $this->listFull('anope.listServers', 'anope.server'); }
	public function opers(): array    { return $this->listFull('anope.listOpers', 'anope.oper'); }
	public function accounts(): array { return $this->listFull('anope.listAccounts', 'anope.account', 300); }
}

/* ---------- Normalisation helpers ---------- */

function ar_is_list(array $a): bool { return $a === [] || array_keys($a) === range(0, count($a) - 1); }

/** Returns the first existing key among $keys (a.b notation for nesting) */
function ar_get($arr, array $keys, $def = null)
{
	foreach ($keys as $k) {
		$cur = $arr;
		foreach (explode('.', $k) as $p) {
			if (is_array($cur) && array_key_exists($p, $cur)) $cur = $cur[$p];
			else { $cur = null; break; }
		}
		if ($cur !== null && $cur !== '') return $cur;
	}
	return $def;
}

function u_nick($u)    { return (string)ar_get($u, ['nick', 'name'], '?'); }
function u_ident($u)   { return (string)ar_get($u, ['ident', 'user', 'username'], ''); }
function u_host($u)    { return (string)ar_get($u, ['host', 'realhost', 'hostname'], ''); }
function u_vhost($u)   { return (string)ar_get($u, ['vhost', 'chost', 'displayedhost', 'cloakedhost'], ''); }
function u_ip($u)      { return (string)ar_get($u, ['ip', 'address'], ''); }
function u_real($u)    { return (string)ar_get($u, ['real', 'realname', 'gecos'], ''); }
function u_server($u)  { $s = ar_get($u, ['server.name', 'server'], ''); return is_array($s) ? '' : (string)$s; }
function u_signon($u)  { return (int)ar_get($u, ['signon', 'connected', 'timestamp', 'ts'], 0); }
function u_tls($u)     { return (bool)ar_get($u, ['tls', 'secure', 'ssl'], false); }
function u_away($u)
{
	$a = ar_get($u, ['away'], null);
	if (is_array($a)) return (string)ar_get($a, ['message', 'reason', 'value'], t('away'));
	return $a ? (is_string($a) ? $a : t('away')) : '';
}
function u_account($u)
{
	$a = ar_get($u, ['account'], null);
	if (is_array($a)) return (string)ar_get($a, ['display', 'name', 'nick'], '');
	return is_string($a) ? $a : '';
}
function u_oper($u)
{
	$o = ar_get($u, ['account.opertype', 'opertype', 'oper'], null);
	if (is_array($o)) return (string)ar_get($o, ['name', 'type'], 'oper');
	return $o ? (is_string($o) ? $o : 'oper') : '';
}
function u_modes($u)   { $m = ar_get($u, ['modes', 'umodes'], ''); return is_array($m) ? implode('', ar_is_list($m) ? $m : array_keys($m)) : (string)$m; }
function u_channels($u)
{
	$c = ar_get($u, ['channels', 'chans'], []);
	if (!is_array($c)) return [];
	$out = [];
	foreach ($c as $k => $v) $out[] = is_string($v) ? $v : (is_string($k) ? $k : (string)ar_get($v, ['name'], ''));
	return array_filter($out);
}

function c_name($c)    { return (string)ar_get($c, ['name', 'channel'], '?'); }
function c_created($c) { return (int)ar_get($c, ['created', 'creation', 'ts', 'timestamp'], 0); }
function c_topic($c)
{
	$t = ar_get($c, ['topic'], '');
	return is_array($t) ? (string)ar_get($t, ['value', 'text', 'topic'], '') : (string)$t;
}
function c_topic_setter($c) { return (string)ar_get($c, ['topic.setter', 'topicsetter', 'topic_setter'], ''); }
function c_topic_time($c)   { return (int)ar_get($c, ['topic.time', 'topic.ts', 'topictime', 'topic_time'], 0); }
function c_modes($c)   { $m = ar_get($c, ['modes'], ''); return is_array($m) ? implode(' ', array_map(fn($x) => is_array($x) ? json_encode($x) : $x, ar_is_list($m) ? $m : array_keys($m))) : (string)$m; }
/** List of [prefix, nick] */
function c_users($c)
{
	$u = ar_get($c, ['users', 'members'], []);
	if (!is_array($u)) return [];
	$out = [];
	foreach ($u as $k => $v) {
		if (is_string($v)) {
			preg_match('/^([~&@%+!.]*)(.*)$/u', $v, $m);
			$out[] = [$m[1], $m[2]];
		} elseif (is_array($v)) {
			$p = ar_get($v, ['prefix', 'status'], '');
			$out[] = [is_array($p) ? implode('', $p) : (string)$p, (string)ar_get($v, ['nick', 'name'], is_string($k) ? $k : '?')];
		} elseif (is_string($k)) {
			$out[] = [is_string($v) ? $v : '', $k];
		}
	}
	return $out;
}

function s_name($s)    { return (string)ar_get($s, ['name'], '?'); }
function s_uplink($s)  { $u = ar_get($s, ['uplink', 'parent'], null); return is_array($u) ? (string)ar_get($u, ['name'], '') : (string)$u; }
function s_desc($s)    { return (string)ar_get($s, ['description', 'desc', 'info'], ''); }

function a_name($a)    { return (string)ar_get($a, ['display', 'name', 'account'], '?'); }
function a_reg($a)     { return (int)ar_get($a, ['registered', 'regtime', 'time_registered', 'created'], 0); }
function a_seen($a)    { return (int)ar_get($a, ['lastseen', 'last_seen', 'seen', 'lastusermask_time'], 0); }
function a_email($a)   { return (string)ar_get($a, ['email', 'mail'], ''); }
function a_nicks($a)
{
	$n = ar_get($a, ['nicks', 'aliases', 'nicknames'], []);
	if (!is_array($n)) return [];
	$out = [];
	foreach ($n as $k => $v) $out[] = is_string($v) ? $v : (is_string($k) ? $k : (string)ar_get($v, ['nick', 'name'], ''));
	return array_filter($out);
}

/* ---------- Display helpers ---------- */

function ar_ago(int $ts): string
{
	if ($ts <= 0) return '—';
	$d = time() - $ts;
	if ($d < 60) return t('%d s', $d);
	if ($d < 3600) return t('%d min', intdiv($d, 60));
	if ($d < 86400) return t('%d h %d min', intdiv($d, 3600), intdiv($d % 3600, 60));
	if ($d < 86400 * 365) return t('%d d', intdiv($d, 86400));
	return t('%s yr', number_format($d / 86400 / 365, 1));
}

function ar_can_admin(): bool
{
	if (!function_exists('current_user_can')) return true;
	foreach (['PERMISSION_MANAGE_USERS', 'PERMISSION_REHASH'] as $p)
		if (defined($p) && current_user_can(constant($p))) return true;
	return false;
}

/** Common page start */
function ar_page_start(string $title, string $icon)
{
	$base = get_config('base_url');
	echo '<link rel="stylesheet" href="' . ar_h($base) . 'plugins/anope_rpc/assets/anope.css?v=2">';
	echo '<div class="anope-wrap" lang="' . ar_lang() . '"><div class="d-flex align-items-center justify-content-between flex-wrap mb-3">';
	echo '<h4 class="mb-0"><i class="fa-solid fa-' . ar_h($icon) . '"></i> ' . ar_h($title) . '</h4>';
	echo '<div class="anope-meta text-muted small">' . th('Anope JSON-RPC · updated %s', date('H:i:s')) . ar_lang_switcher() . '</div></div>';
}
function ar_page_end()
{
	$base = get_config('base_url');
	$js = [];
	foreach (['Search:', '_MENU_ per page', '_START_–_END_ of _TOTAL_', '0 results', '(filtered from _MAX_)', 'No results',
		'Show all channels', 'Show only channels without a topic', 'Copied ✓'] as $k) $js[$k] = t($k);
	echo '</div><script>window.ANOPE_I18N=' . json_encode($js, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) . ';</script>';
	echo '<script src="' . ar_h($base) . 'plugins/anope_rpc/assets/anope.js?v=2"></script>';
}
function ar_error(Throwable $e)
{
	echo '<div class="alert alert-danger"><b>' . th('Unable to reach Anope.') . '</b> ' . ar_h($e->getMessage())
		. '<br><small>' . t('Set the URL and token in <i>Settings → General</i>, or copy <code>config.sample.php</code> to <code>config.php</code> in the plugin folder.') . '</small></div>';
}

/** Horizontal HTML/CSS bars */
function ar_bars(array $data, int $max = 10, string $link = '')
{
	arsort($data);
	$data = array_slice($data, 0, $max, true);
	$top = max($data ?: [1]);
	echo '<div class="anope-bars">';
	foreach ($data as $k => $v) {
		$w = $top ? round(100 * $v / $top, 1) : 0;
		$label = ar_h($k);
		if ($link) $label = '<a href="' . ar_h(sprintf($link, rawurlencode($k))) . '">' . $label . '</a>';
		echo '<div class="anope-bar"><span class="lbl" title="' . ar_h($k) . '">' . $label . '</span>'
			. '<span class="trk"><span class="fill" style="width:' . $w . '%"></span></span><span class="val">' . ar_n($v) . '</span></div>';
	}
	if (!$data) echo '<div class="text-muted small">' . th('No data') . '</div>';
	echo '</div>';
}

/** SVG ring (share in %) */
function ar_ring(float $part, float $total, string $label, string $cls = 'ring-a')
{
	$p = $total > 0 ? $part / $total : 0;
	$r = 40; $c = 2 * M_PI * $r;
	$dash = round($c * $p, 2);
	echo '<div class="anope-ring ' . $cls . '"><svg viewBox="0 0 100 100" role="img" aria-label="' . ar_h($label) . '">'
		. '<circle cx="50" cy="50" r="' . $r . '" class="bg"/>'
		. '<circle cx="50" cy="50" r="' . $r . '" class="fg" stroke-dasharray="' . $dash . ' ' . round($c, 2) . '" transform="rotate(-90 50 50)"/>'
		. '<text x="50" y="50" class="pct">' . round($p * 100) . '%</text>'
		. '<text x="50" y="66" class="sub">' . ar_n($part) . '/' . ar_n($total) . '</text></svg>'
		. '<div class="lbl">' . ar_h($label) . '</div></div>';
}

/** Vertical SVG histogram (e.g. connections per hour) */
function ar_histo(array $buckets, array $labels)
{
	$n = count($buckets); $max = max($buckets ?: [1]) ?: 1;
	$w = 600; $h = 140; $bw = $w / max($n, 1);
	echo '<svg class="anope-histo" viewBox="0 0 ' . $w . ' ' . ($h + 20) . '" preserveAspectRatio="none">';
	foreach (array_values($buckets) as $i => $v) {
		$bh = round($h * $v / $max, 1);
		echo '<rect x="' . round($i * $bw + 2, 1) . '" y="' . ($h - $bh) . '" width="' . round($bw - 4, 1) . '" height="' . $bh . '" rx="2"><title>'
			. ar_h($labels[$i] ?? '') . ' : ' . $v . '</title></rect>';
		if ($n <= 24 && $i % 3 === 0)
			echo '<text x="' . round($i * $bw + $bw / 2, 1) . '" y="' . ($h + 15) . '">' . ar_h($labels[$i] ?? '') . '</text>';
	}
	echo '</svg>';
}

// Resolve the language early so the ?anope_lang= cookie can still be sent
ar_lang();
