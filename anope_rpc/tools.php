<?php
require_once "../../inc/common.php";
require_once __DIR__ . "/lib/client.php";
require_once "../../inc/header.php";

ar_page_start(t("Anope tools"), "toolbox");
$rpc = AnopeRPC::instance();
$admin = ar_can_admin();
$out = null; $flash = [];

/** Read-only methods allowed for every panel user */
function ar_readonly_method(string $m): bool
{
	return (bool)preg_match('/^(system\.|anope\.(list[A-Z]\w*|user|channel|server|account|oper)$)/', $m);
}

/** Tries a call, then retries with the source nick first if Anope requires it */
function ar_call_with_source(AnopeRPC $rpc, string $m, array $p)
{
	try { return $rpc->call($m, $p); }
	catch (AnopeRPCException $e) {
		if ($rpc->source() === '') throw $e;
		return $rpc->call($m, array_merge([$rpc->source()], $p));
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$act = $_POST['act'] ?? '';
	try {
		switch ($act) {
			case 'flush':
				AnopeRPC::flushCache();
				$flash[] = ['success', th('Cache cleared.')];
				break;
			case 'global':
				if (!$admin) throw new Exception(t('Permission denied'));
				$msg = trim($_POST['msg'] ?? '');
				if ($msg === '') throw new Exception(t('Empty message'));
				$target = trim($_POST['server'] ?? '');
				$target === ''
					? ar_call_with_source($rpc, 'anope.messageNetwork', [$msg])
					: ar_call_with_source($rpc, 'anope.messageServer', [$target, $msg]);
				$flash[] = ['success', $target ? th('Message sent on %s.', $target) : th('Message sent to the whole network.')];
				break;
			case 'privmsg':
				if (!$admin) throw new Exception(t('Permission denied'));
				$from = trim($_POST['from'] ?? '');
				if ($from === '') $from = $rpc->source() ?: 'NickServ';
				$rpc->call('anope.messageUser', [$from, trim($_POST['to'] ?? ''), trim($_POST['msg'] ?? '')]);
				$flash[] = ['success', th('Message sent to %s as %s.', $_POST['to'] ?? '', $from)];
				break;
			case 'creds':
				if (!$admin) throw new Exception(t('Permission denied'));
				$r = $rpc->call('anope.checkCredentials', [trim($_POST['acc'] ?? ''), (string)($_POST['pass'] ?? '')]);
				$flash[] = ['info', th('Valid credentials') . ' ✅ <pre class="anope-json mb-0 mt-2">' . ar_h(json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>'];
				break;
			case 'raw':
				$m = trim($_POST['method'] ?? '');
				if (!$admin && !ar_readonly_method($m)) throw new Exception(t('This method is restricted to panel administrators'));
				$p = trim($_POST['params'] ?? '');
				$params = $p === '' ? [] : json_decode($p, true);
				if (!is_array($params)) throw new Exception(t('Parameters must be a JSON array, e.g. ["#welcome"]'));
				$t0 = microtime(true);
				$out = $rpc->call($m, $params);
				$flash[] = ['secondary', th('%s answered in %s ms', $m, number_format((microtime(true) - $t0) * 1000, 1))];
				break;
		}
	} catch (Throwable $e) {
		$flash[] = ['danger', ar_h($e->getMessage())];
	}
}

// Diagnostics
$t0 = microtime(true);
try { $methods = $rpc->call('system.listMethods'); $lat = (microtime(true) - $t0) * 1000; $diag = null; }
catch (Throwable $e) { $methods = []; $lat = null; $diag = $e->getMessage(); }
if (!is_array($methods)) $methods = [];
sort($methods);
try { $srvList = $rpc->servers(); } catch (Throwable $e) { $srvList = []; }
$serverNames = array_map('s_name', $srvList);

// Service bots usable as message source: clients on U-lined (services) servers,
// falling back to the usual Anope pseudo-clients
$services = [];
$uServers = array_map('s_name', array_filter($srvList, fn($s) => ar_get($s, ['ulined', 'ulined_server'], false)));
if ($uServers) {
	try {
		foreach ($rpc->users() as $u) if (in_array(u_server($u), $uServers, true)) $services[] = u_nick($u);
	} catch (Throwable $e) {}
}
if (!$services) $services = ['NickServ', 'ChanServ', 'MemoServ', 'OperServ', 'HostServ', 'BotServ', 'Global'];
if ($rpc->source() !== '' && !in_array($rpc->source(), $services, true)) array_unshift($services, $rpc->source());
natcasesort($services);
$defaultFrom = in_array('NickServ', $services, true) ? 'NickServ' : ($rpc->source() ?: reset($services));

foreach ($flash as [$cls, $msg]) echo '<div class="alert alert-' . $cls . '">' . $msg . '</div>';
?>
<div class="row">
	<div class="col-lg-4 mb-3"><div class="card h-100"><div class="card-header"><?= th('Diagnostics') ?></div><div class="card-body">
		<?php if ($diag): ?>
			<div class="text-danger"><i class="fa-solid fa-circle-xmark"></i> <?= ar_h($diag) ?></div>
		<?php else: ?>
			<div class="text-success mb-2"><i class="fa-solid fa-circle-check"></i> <?= th('Connected · %s ms', number_format($lat, 1)) ?></div>
			<div class="small text-muted mb-1"><?= th('%d exposed methods:', count($methods)) ?></div>
			<div class="anope-methods small">
				<?php foreach ($methods as $m) echo '<a href="#" class="badge badge-light anope-pick" data-m="' . ar_h($m) . '">' . ar_h($m) . '</a> '; ?>
			</div>
		<?php endif; ?>
		<form method="post" class="mt-3"><input type="hidden" name="act" value="flush">
			<button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-broom"></i> <?= th('Clear cache') ?></button></form>
	</div></div></div>

	<div class="col-lg-8 mb-3"><div class="card h-100"><div class="card-header"><?= th('RPC explorer') ?></div><div class="card-body">
		<form method="post">
			<input type="hidden" name="act" value="raw">
			<div class="form-row">
				<div class="col-md-5 mb-2"><input class="form-control form-control-sm" id="anope-method" name="method" list="anope-mlist" placeholder="anope.channel" value="<?= ar_h($_POST['method'] ?? '') ?>" required>
					<datalist id="anope-mlist"><?php foreach ($methods as $m) echo '<option value="' . ar_h($m) . '">'; ?></datalist></div>
				<div class="col-md-5 mb-2"><input class="form-control form-control-sm" name="params" placeholder='["#welcome"]' value="<?= ar_h($_POST['params'] ?? '') ?>"></div>
				<div class="col-md-2 mb-2"><button class="btn btn-sm btn-primary btn-block"><?= th('Run') ?></button></div>
			</div>
			<?php if (!$admin): ?><small class="text-muted"><?= th('Read-only mode: only anope.list*, anope.user/channel/server/account/oper and system.* methods are allowed.') ?></small><?php endif; ?>
		</form>
		<?php if ($out !== null): ?>
			<div class="d-flex justify-content-end mb-1"><button class="btn btn-link btn-sm p-0" id="anope-copy"><?= th('Copy') ?></button></div>
			<pre class="anope-json" id="anope-out"><?= ar_h(json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
		<?php endif; ?>
	</div></div></div>
</div>

<?php if ($admin): ?>
<div class="row">
	<div class="col-lg-6 mb-3"><div class="card h-100"><div class="card-header"><i class="fa-solid fa-bullhorn"></i> <?= th('Global announcement') ?></div><div class="card-body">
		<form method="post" onsubmit="return confirm(<?= ar_h(json_encode(t('Send this message to all affected users?'))) ?>);">
			<input type="hidden" name="act" value="global">
			<textarea class="form-control mb-2" name="msg" rows="2" maxlength="400" placeholder="<?= th('Maintenance in 10 minutes, thank you for your patience!') ?>" required></textarea>
			<div class="form-row">
				<div class="col"><select class="custom-select custom-select-sm" name="server"><option value=""><?= th('Whole network') ?></option>
					<?php foreach ($serverNames as $s) echo '<option>' . ar_h($s) . '</option>'; ?></select></div>
				<div class="col-auto"><button class="btn btn-sm btn-warning"><?= th('Send') ?></button></div>
			</div>
		</form>
	</div></div></div>

	<div class="col-lg-3 mb-3"><div class="card h-100"><div class="card-header"><i class="fa-solid fa-message"></i> <?= th('Private message') ?></div><div class="card-body">
		<form method="post"><input type="hidden" name="act" value="privmsg">
			<label class="small mb-0" for="anope-from"><?= th('Send as') ?></label>
			<input class="form-control form-control-sm mb-2" id="anope-from" name="from" list="anope-services" value="<?= ar_h($_POST['from'] ?? $defaultFrom) ?>" required>
			<datalist id="anope-services"><?php foreach ($services as $s) echo '<option value="' . ar_h($s) . '">'; ?></datalist>
			<input class="form-control form-control-sm mb-2" name="to" placeholder="<?= th('Recipient nick') ?>" required>
			<input class="form-control form-control-sm mb-2" name="msg" placeholder="<?= th('Message') ?>" required>
			<button class="btn btn-sm btn-primary"><?= th('Send') ?></button></form>
	</div></div></div>

	<div class="col-lg-3 mb-3"><div class="card h-100"><div class="card-header"><i class="fa-solid fa-key"></i> <?= th('Test credentials') ?></div><div class="card-body">
		<form method="post" autocomplete="off"><input type="hidden" name="act" value="creds">
			<input class="form-control form-control-sm mb-2" name="acc" placeholder="<?= th('Account') ?>" required>
			<input type="password" class="form-control form-control-sm mb-2" name="pass" placeholder="<?= th('Password') ?>" required autocomplete="new-password">
			<button class="btn btn-sm btn-secondary"><?= th('Check') ?></button></form>
		<small class="text-muted"><?= th('The password is neither stored nor logged by the plugin.') ?></small>
	</div></div></div>
</div>
<?php endif; ?>

<div class="card mb-3"><div class="card-header"><i class="fa-solid fa-magnifying-glass"></i> <?= th('Search by mask') ?></div><div class="card-body">
	<form method="get" class="form-inline mb-2">
		<input class="form-control form-control-sm mr-2" name="mask" placeholder="*!*@*.fr  /  *bot*" value="<?= ar_h($_GET['mask'] ?? '') ?>">
		<label class="small mr-2"><input type="checkbox" name="real" value="1" <?= !empty($_GET['real']) ? 'checked' : '' ?>>&nbsp;<?= th('also search the real name') ?></label>
		<button class="btn btn-sm btn-primary"><?= th('Search') ?></button>
	</form>
	<?php if (!empty($_GET['mask'])) {
		try {
			$mask = $_GET['mask']; $hits = [];
			foreach ($rpc->users() as $u) {
				$full = u_nick($u) . '!' . u_ident($u) . '@' . u_host($u);
				$fullv = u_nick($u) . '!' . u_ident($u) . '@' . u_vhost($u);
				$ok = fnmatch($mask, $full, FNM_CASEFOLD) || fnmatch($mask, $fullv, FNM_CASEFOLD) || fnmatch($mask, u_nick($u), FNM_CASEFOLD)
					|| (u_ip($u) !== '' && fnmatch($mask, u_ip($u)))
					|| (!empty($_GET['real']) && fnmatch($mask, u_real($u), FNM_CASEFOLD));
				if ($ok) $hits[] = $u;
			}
			echo '<p class="small mb-1">' . th('%d result(s)', count($hits)) . '</p>';
			if ($hits) {
				echo '<table class="table table-sm mb-0"><tbody>';
				foreach (array_slice($hits, 0, 200) as $u)
					echo '<tr><td><a href="users.php?u=' . rawurlencode(u_nick($u)) . '">' . ar_h(u_nick($u)) . '</a></td><td class="small"><code>'
						. ar_h(u_ident($u) . '@' . u_host($u)) . '</code></td><td class="small">' . ar_h(u_real($u)) . '</td><td class="small">' . ar_h(u_server($u)) . '</td></tr>';
				echo '</tbody></table>';
			}
		} catch (Throwable $e) { echo '<div class="text-danger small">' . ar_h($e->getMessage()) . '</div>'; }
	} ?>
</div></div>
<?php
ar_page_end();
require_once "../../inc/footer.php";
