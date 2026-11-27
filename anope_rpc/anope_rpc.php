<?php
/**
@title Anope RPC
@author Claude & Madrix
@description Full Anope 2.1 interface over JSON-RPC: dashboard, users, channels, accounts, servers, opers and tools. Available in English, French, Spanish, Dutch, German and Polish.
@contact armyndev@outlook.com
@version 1.3
@tested 1.0
@minver 0.9
@maxver *
@license GPLv3
@icon https://github.com/Madriix/unrealircd-webpanel-plugins/blob/main/anope_rpc/screenshots/anope.png?raw=true
@screenshot https://github.com/Madriix/unrealircd-webpanel-plugins/blob/main/anope_rpc/screenshots/screenshot.png?raw=true
*/

require_once __DIR__ . "/lib/client.php";

class anope_rpc
{
	public $name = "anope_rpc";
	public $author = "Claude & Madrix";
	public $version = "1.3";
	public $description = "Anope 2.1 interface over JSON-RPC (rpc_data, rpc_message, rpc_user) — EN/FR/ES/NL/DE/PL";
	public $email = "armyndev@outlook.com";

	function __construct()
	{
		Hook::func(HOOKTYPE_NAVBAR, 'anope_rpc::add_navbar');
		Hook::func(HOOKTYPE_OVERVIEW_CARD, 'anope_rpc::overview_card');
		Hook::func(HOOKTYPE_GENERAL_SETTINGS, 'anope_rpc::settings_form');
		Hook::func(HOOKTYPE_GENERAL_SETTINGS_POST, 'anope_rpc::settings_save');
	}

	public static function add_navbar(&$pages)
	{
		$b = "plugins/anope_rpc/";
		$n = ["no_irc_server_required" => true];
		$pages["Anope"] = [
			t("Dashboard")         => ["script" => $b . "index.php"] + $n,
			t("Users")             => ["script" => $b . "users.php"] + $n,
			t("Channels")          => ["script" => $b . "channels.php"] + $n,
			t("Accounts")          => ["script" => $b . "accounts.php"] + $n,
			t("Servers & opers")   => ["script" => $b . "servers.php"] + $n,
			t("Tools")             => ["script" => $b . "tools.php"] + $n,
		];
	}

	/** Small card on the panel home page */
	public static function overview_card(&$stats)
	{
		try {
			$rpc = AnopeRPC::instance();
			$u = count($rpc->listRaw('anope.listUsers'));
			$c = count($rpc->listRaw('anope.listChannels'));
			$a = count($rpc->listRaw('anope.listAccounts'));
			$ok = true;
		} catch (Throwable $e) {
			$ok = false;
		}
		echo '<div class="container-fluid mt-3"><h5><i class="fa-solid fa-shield-halved"></i> Anope</h5><div class="row">';
		if (!$ok) {
			echo '<div class="col"><div class="alert alert-warning">' . th('Anope unreachable: %s', $e->getMessage()) . '</div></div></div></div>';
			return;
		}
		foreach ([[t("Users"), $u, "users", "primary"], [t("Channels"), $c, "channels", "success"], [t("Accounts"), $a, "accounts", "info"]] as [$title, $v, $p, $col]) {
			echo '<div class="col-sm-4 mb-3"><a class="text-decoration-none" href="' . ar_h(get_config("base_url") . "plugins/anope_rpc/$p.php") . '">'
				. '<div class="card text-white bg-' . $col . '"><div class="card-body"><h6 class="card-title mb-1">' . ar_h($title) . '</h6>'
				. '<h3 class="mb-0">' . ar_n($v) . '</h3></div></div></a></div>';
		}
		echo '</div></div>';
	}

	public static function settings_form(&$empty)
	{
		$s = AnopeRPC::settings();
		?>
		<h5 class="mt-4"><i class="fa-solid fa-shield-halved"></i> Anope JSON-RPC</h5>
		<div class="form-group">
			<label for="anope_rpc_url"><?= th("Endpoint URL") ?></label>
			<input class="form-control" id="anope_rpc_url" name="anope_rpc_url" value="<?= ar_h($s['url']) ?>" placeholder="http://127.0.0.1:8080/jsonrpc">
		</div>
		<div class="form-group">
			<label for="anope_rpc_token"><?= th("Token (leave empty to keep the current one)") ?></label>
			<input type="password" class="form-control" id="anope_rpc_token" name="anope_rpc_token" autocomplete="new-password">
		</div>
		<div class="form-row">
			<div class="form-group col-md-3">
				<label for="anope_rpc_cache"><?= th("Cache (seconds)") ?></label>
				<input type="number" min="0" class="form-control" id="anope_rpc_cache" name="anope_rpc_cache" value="<?= (int)$s['cache'] ?>">
			</div>
			<div class="form-group col-md-3">
				<label for="anope_rpc_detail"><?= th("Detail level for list*") ?></label>
				<input class="form-control" id="anope_rpc_detail" name="anope_rpc_detail" value="<?= ar_h($s['detail']) ?>">
				<small class="form-text text-muted"><?= th("“full” on Anope 2.1.14+") ?></small>
			</div>
			<div class="form-group col-md-3">
				<label for="anope_rpc_source"><?= th("Source nick for messages") ?></label>
				<input class="form-control" id="anope_rpc_source" name="anope_rpc_source" value="<?= ar_h($s['source']) ?>" placeholder="Global">
			</div>
			<div class="form-group col-md-3">
				<label for="anope_rpc_lang"><?= th("Default language") ?></label>
				<select class="custom-select" id="anope_rpc_lang" name="anope_rpc_lang">
					<option value="auto"<?= ($s['lang'] ?? 'auto') === 'auto' ? ' selected' : '' ?>><?= th("Automatic (browser)") ?></option>
					<?php foreach (ANOPE_LANGS as $code => $name): ?>
						<option value="<?= $code ?>"<?= ($s['lang'] ?? '') === $code ? ' selected' : '' ?>><?= ar_h($name) ?></option>
					<?php endforeach; ?>
				</select>
				<small class="form-text text-muted"><?= th("Each user can still switch from the plugin pages.") ?></small>
			</div>
		</div>
		<h6 class="mt-3"><?= th("Version detection with CTCP VERSION (like /ctcp NickServ VERSION)") ?></h6>
		<p class="small text-muted mb-2"><?= th("The panel briefly connects to IRC with a temporary nick, asks the services bot for its version, then disconnects. Leave the host empty to disable it. The result is cached for one hour.") ?></p>
		<div class="form-row">
			<div class="form-group col-md-4">
				<label for="anope_rpc_probe_host"><?= th("IRC server host") ?></label>
				<input class="form-control" id="anope_rpc_probe_host" name="anope_rpc_probe_host" value="<?= ar_h($s['probe_host']) ?>" placeholder="127.0.0.1">
			</div>
			<div class="form-group col-md-2">
				<label for="anope_rpc_probe_port"><?= th("Port") ?></label>
				<input type="number" class="form-control" id="anope_rpc_probe_port" name="anope_rpc_probe_port" value="<?= (int)$s['probe_port'] ?>">
			</div>
			<div class="form-group col-md-2">
				<label class="d-block"><?= th("TLS") ?></label>
				<label class="mt-2"><input type="checkbox" name="anope_rpc_probe_tls" value="1" <?= $s['probe_tls'] ? 'checked' : '' ?>> <?= th("yes") ?></label>
			</div>
			<div class="form-group col-md-2">
				<label for="anope_rpc_probe_target"><?= th("Service to query") ?></label>
				<input class="form-control" id="anope_rpc_probe_target" name="anope_rpc_probe_target" value="<?= ar_h($s['probe_target']) ?>" placeholder="NickServ">
			</div>
			<div class="form-group col-md-2">
				<label for="anope_rpc_probe_pass"><?= th("Server password") ?></label>
				<input type="password" class="form-control" id="anope_rpc_probe_pass" name="anope_rpc_probe_pass" autocomplete="new-password" placeholder="<?= th("unchanged") ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="anope_rpc_version"><?= th("Anope version (optional)") ?></label>
			<input class="form-control" id="anope_rpc_version" name="anope_rpc_version" value="<?= ar_h($s['version'] ?? '') ?>" placeholder="2.1.28">
			<small class="form-text text-muted"><?= th("Only used when the version cannot be detected automatically.") ?></small>
		</div>
		<?php
	}

	public static function settings_save(&$post)
	{
		global $config;
		if (!isset($post['anope_rpc_url']))
			return;
		$lang = $post['anope_rpc_lang'] ?? 'auto';
		$config['anope_rpc'] = [
			'url'    => trim($post['anope_rpc_url']),
			'token'  => ($post['anope_rpc_token'] ?? '') !== '' ? secret_encrypt($post['anope_rpc_token']) : ($config['anope_rpc']['token'] ?? ''),
			'cache'  => max(0, (int)($post['anope_rpc_cache'] ?? 30)),
			'detail' => trim($post['anope_rpc_detail'] ?? 'full') ?: 'full',
			'source' => trim($post['anope_rpc_source'] ?? ''),
			'lang'   => isset(ANOPE_LANGS[$lang]) ? $lang : 'auto',
			'version'=> trim($post['anope_rpc_version'] ?? ''),
			'probe_host'   => trim($post['anope_rpc_probe_host'] ?? ''),
			'probe_port'   => (int)($post['anope_rpc_probe_port'] ?? 6697) ?: 6697,
			'probe_tls'    => !empty($post['anope_rpc_probe_tls']),
			'probe_target' => trim($post['anope_rpc_probe_target'] ?? '') ?: 'NickServ',
			'probe_pass'   => ($post['anope_rpc_probe_pass'] ?? '') !== '' ? secret_encrypt($post['anope_rpc_probe_pass']) : ($config['anope_rpc']['probe_pass'] ?? ''),
		];
		write_config('anope_rpc');
		AnopeRPC::flushCache();
	}
}
