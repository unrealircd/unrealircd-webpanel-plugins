<?php
require_once "../../inc/common.php";
require_once __DIR__ . "/lib/client.php";

// The webpanel's own UnrealIRCd JSON-RPC connection, used to read the services
// server's software version. Only loaded when an UnrealIRCd RPC server is configured.
$urpc = null;
try {
	if (function_exists('get_active_rpc_server') && get_active_rpc_server() && is_file("../../inc/connection.php")) {
		require_once "../../inc/connection.php";
		$urpc = $GLOBALS['rpc'] ?? null;
	}
} catch (Throwable $e) { $urpc = null; }

require_once "../../inc/header.php";

ar_page_start(t("Anope — Dashboard"), "gauge-high");
try {
	$rpc = AnopeRPC::instance();
	$users = $rpc->users();
	$chans = $rpc->channels();
	$servers = $rpc->servers();
	$opers = $rpc->opers();
	try { $nacc = count($rpc->listRaw('anope.listAccounts')); } catch (Throwable $e) { $nacc = null; }
	$ver = $rpc->version($urpc);
	$latest = $ver['version'] ? AnopeRPC::latestRelease($ver['version']) : null;
} catch (Throwable $e) {
	ar_error($e); ar_page_end(); require_once "../../inc/footer.php"; exit;
}

$nu = count($users);
$tls = count(array_filter($users, 'u_tls'));
$ident = count(array_filter($users, fn($u) => u_account($u) !== ''));
$away = count(array_filter($users, fn($u) => u_away($u) !== ''));
$ircops = count(array_filter($users, fn($u) => u_oper($u) !== '' || str_contains(u_modes($u), 'o')));

// Most populated channels
$chanSize = [];
foreach ($chans as $c) $chanSize[c_name($c)] = count(c_users($c));
$biggest = $chanSize ? max($chanSize) : 0;

// Users per server
$perServer = array_count_values(array_filter(array_map('u_server', $users)));

// TLDs
$tld = [];
foreach ($users as $u) {
	$h = u_host($u);
	if ($h === '' || filter_var($h, FILTER_VALIDATE_IP)) { $t = t('Raw IP'); }
	else { $t = strtolower(substr(strrchr($h, '.') ?: '.?', 1)); }
	$tld[$t] = ($tld[$t] ?? 0) + 1;
}

// Connections over the last 24 h, per hour
$buckets = array_fill(0, 24, 0); $labels = [];
$now = time();
for ($i = 0; $i < 24; $i++) $labels[$i] = date(ar_lang() === 'en' ? 'ga' : 'H\h', $now - (23 - $i) * 3600);
foreach ($users as $u) {
	$s = u_signon($u);
	if ($s > 0 && $now - $s < 86400) $buckets[23 - intdiv($now - $s, 3600)]++;
}

// Clones
$ips = [];
foreach ($users as $u) if (($ip = u_ip($u)) !== '' && $ip !== '255.255.255.255' && $ip !== '0') $ips[$ip][] = u_nick($u);
$clones = array_filter($ips, fn($n) => count($n) > 2);
uasort($clones, fn($a, $b) => count($b) <=> count($a));

// Longest connected
$old = array_filter($users, fn($u) => u_signon($u) > 0);
usort($old, fn($a, $b) => u_signon($a) <=> u_signon($b));
$old = array_slice($old, 0, 5);

$base = get_config('base_url') . 'plugins/anope_rpc/';
?>
<div class="anope-version card mb-3"><div class="card-body py-2 d-flex align-items-center flex-wrap">
	<i class="fa-solid fa-code-branch mr-2"></i>
	<?php if ($ver['version']): ?>
		<span class="mr-2"><?= th('Anope version') ?> <b class="anope-vnum"><?= ar_h($ver['version']) ?></b></span>
		<?php if ($latest && version_compare($latest, $ver['version'], '>')): ?>
			<a class="badge badge-warning mr-2" href="https://www.anope.org/news/" target="_blank" rel="noopener"><?= th('Update available: %s', $latest) ?></a>
		<?php elseif ($latest): ?>
			<span class="badge badge-success mr-2"><?= th('Up to date') ?></span>
		<?php endif; ?>
		<?php if (preg_match('/^\d+\.(\d+)\./', $ver['version'], $mm) && (int)$mm[1] % 2 === 1): ?>
			<span class="badge badge-info mr-2" title="<?= th('Odd minor versions (2.1, 2.3…) are Anope development releases.') ?>"><?= th('development branch') ?></span>
		<?php endif; ?>
		<span class="text-muted small ml-auto" title="<?= ar_h($ver['raw']) ?>"><?= th('detected via %s', $ver['via']) ?></span>
	<?php else: ?>
		<span class="text-muted" title="<?= ar_h(implode("\n", $ver['errors'] ?? [])) ?>"><?= th('Anope version not detected. Enable the CTCP VERSION probe or enter the version manually in Settings → General.') ?></span>
	<?php endif; ?>
</div></div>

<div class="row anope-kpis">
	<?php foreach ([
		[t('Users'), $nu, 'users', 'users.php'],
		[t('Channels'), count($chans), 'hashtag', 'channels.php'],
		[t('Accounts'), $nacc, 'id-card', 'accounts.php'],
		[t('Servers'), count($servers), 'server', 'servers.php'],
		[t('Services opers'), count($opers), 'user-shield', 'servers.php#opers'],
		[t('Biggest channel'), $biggest, 'arrow-trend-up', 'channels.php'],
	] as [$title, $v, $i, $l]): ?>
	<div class="col-6 col-md-4 col-xl-2 mb-3">
		<a href="<?= ar_h($base . $l) ?>" class="anope-kpi card h-100">
			<div class="card-body">
				<div class="ico"><i class="fa-solid fa-<?= $i ?>"></i></div>
				<div class="v"><?= $v === null ? '—' : ar_n($v) ?></div>
				<div class="t"><?= ar_h($title) ?></div>
			</div>
		</a>
	</div>
	<?php endforeach; ?>
</div>

<div class="row">
	<div class="col-lg-5 mb-3">
		<div class="card h-100"><div class="card-header"><?= th('Network health') ?></div>
			<div class="card-body d-flex justify-content-around flex-wrap">
				<?php ar_ring($tls, $nu, t('TLS connections'), 'ring-a'); ?>
				<?php ar_ring($ident, $nu, t('Identified'), 'ring-b'); ?>
				<?php ar_ring($away, $nu, t('Away'), 'ring-c'); ?>
			</div>
			<div class="card-footer small text-muted"><?= th('%s user(s) with operator status', ar_n($ircops)) ?></div>
		</div>
	</div>
	<div class="col-lg-7 mb-3">
		<div class="card h-100"><div class="card-header"><?= th('Still-active connections by arrival hour (24 h)') ?></div>
			<div class="card-body"><?php ar_histo($buckets, $labels); ?></div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-4 mb-3">
		<div class="card h-100"><div class="card-header"><?= th('Top 10 channels') ?></div>
			<div class="card-body"><?php ar_bars($chanSize, 10, $base . 'channels.php?c=%s'); ?></div></div>
	</div>
	<div class="col-lg-4 mb-3">
		<div class="card h-100"><div class="card-header"><?= th('Users per server') ?></div>
			<div class="card-body"><?php ar_bars($perServer, 10); ?></div></div>
	</div>
	<div class="col-lg-4 mb-3">
		<div class="card h-100"><div class="card-header"><?= th('Domains (TLD)') ?></div>
			<div class="card-body"><?php ar_bars($tld, 10); ?></div></div>
	</div>
</div>

<div class="row">
	<div class="col-lg-6 mb-3">
		<div class="card h-100"><div class="card-header"><i class="fa-solid fa-triangle-exclamation text-warning"></i> <?= th('Clones (more than 2 connections per IP)') ?></div>
			<div class="card-body p-0">
			<?php if (!$clones): ?><div class="p-3 text-muted"><?= th('No clones detected') ?> 🎉</div><?php else: ?>
			<table class="table table-sm mb-0"><thead><tr><th>IP</th><th>#</th><th><?= th('Nicks') ?></th></tr></thead><tbody>
			<?php foreach (array_slice($clones, 0, 15, true) as $ip => $n): ?>
				<tr><td><code><?= ar_h($ip) ?></code></td><td><span class="badge badge-warning"><?= count($n) ?></span></td>
				<td class="small"><?= implode(', ', array_map(fn($x) => '<a href="' . ar_h($base . 'users.php?u=' . rawurlencode($x)) . '">' . ar_h($x) . '</a>', $n)) ?></td></tr>
			<?php endforeach; ?></tbody></table>
			<?php endif; ?></div></div>
	</div>
	<div class="col-lg-6 mb-3">
		<div class="card h-100"><div class="card-header"><i class="fa-solid fa-trophy text-warning"></i> <?= th('Longest connected') ?></div>
			<div class="card-body p-0"><table class="table table-sm mb-0"><thead><tr><th><?= th('Nick') ?></th><th><?= th('Account') ?></th><th><?= th('Connected for') ?></th></tr></thead><tbody>
			<?php foreach ($old as $i => $u): ?>
				<tr><td><?= ['🥇','🥈','🥉','4.','5.'][$i] ?> <a href="<?= ar_h($base . 'users.php?u=' . rawurlencode(u_nick($u))) ?>"><?= ar_h(u_nick($u)) ?></a></td>
				<td><?= ar_h(u_account($u) ?: '—') ?></td><td><?= ar_ago(u_signon($u)) ?></td></tr>
			<?php endforeach; ?></tbody></table></div></div>
	</div>
</div>

<div class="text-right small text-muted mb-3">
	<label class="mb-0"><input type="checkbox" id="anope-autorefresh"> <?= th('Auto-refresh every 60 s') ?></label>
</div>
<?php
ar_page_end();
require_once "../../inc/footer.php";
