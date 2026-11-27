<?php
require_once "../../inc/common.php";
require_once __DIR__ . "/lib/client.php";
require_once "../../inc/header.php";

$base = get_config('base_url') . 'plugins/anope_rpc/';
$rpc = AnopeRPC::instance();

/* ---------- Single user ---------- */
if (!empty($_GET['u'])) {
	ar_page_start(t("User %s", $_GET['u']), "user");
	try {
		$u = $rpc->call('anope.user', [$_GET['u']]);
	} catch (Throwable $e) { ar_error($e); ar_page_end(); require_once "../../inc/footer.php"; exit; }
	$acc = u_account($u);
	$rows = [
		t('Nick') => ar_h(u_nick($u)),
		t('Mask') => '<code>' . ar_h(u_nick($u) . '!' . u_ident($u) . '@' . u_host($u)) . '</code>',
		t('Displayed host') => '<code>' . ar_h(u_vhost($u) ?: u_host($u)) . '</code>',
		'IP' => '<code>' . ar_h(u_ip($u) ?: '—') . '</code>',
		t('Real name') => ar_h(u_real($u)),
		t('Server') => ar_h(u_server($u)),
		t('Connected') => ar_date(u_signon($u)) . ' <span class="text-muted">(' . ar_ago(u_signon($u)) . ')</span>',
		t('Account') => $acc !== '' ? '<a href="' . ar_h($base . 'accounts.php?a=' . rawurlencode($acc)) . '">' . ar_h($acc) . '</a>' : '<span class="text-muted">' . th('not identified') . '</span>',
		t('Oper') => ar_h(u_oper($u) ?: '—'),
		t('Modes') => '<code>+' . ar_h(u_modes($u)) . '</code>',
		'TLS' => u_tls($u) ? '<span class="badge badge-success">🔒 ' . th('yes') . '</span>' : '<span class="badge badge-secondary">' . th('no') . '</span>',
		t('Away') => ar_h(u_away($u) ?: '—'),
	];
	?>
	<a href="<?= ar_h($base) ?>users.php" class="btn btn-sm btn-outline-secondary mb-3">← <?= th('All users') ?></a>
	<div class="row">
		<div class="col-lg-7 mb-3"><div class="card"><div class="card-body p-0"><table class="table table-sm mb-0">
		<?php foreach ($rows as $k => $v) echo "<tr><th style='width:30%'>" . ar_h($k) . "</th><td>$v</td></tr>"; ?>
		</table></div></div></div>
		<div class="col-lg-5 mb-3"><div class="card"><div class="card-header"><?= th('Channels (%d)', count(u_channels($u))) ?></div><div class="card-body">
		<?php foreach (u_channels($u) as $c) {
			$name = ltrim($c, '~&@%+');
			echo '<a class="badge badge-pill badge-light anope-chip" href="' . ar_h($base . 'channels.php?c=' . rawurlencode($name)) . '">' . ar_h($c) . '</a> ';
		} ?>
		</div></div></div>
	</div>
	<details class="mb-3"><summary><?= th('Raw JSON response') ?></summary><pre class="anope-json"><?= ar_h(json_encode($u, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre></details>
	<?php
	ar_page_end(); require_once "../../inc/footer.php"; exit;
}

/* ---------- List ---------- */
ar_page_start(t("Connected users"), "users");
try { $users = $rpc->users(); }
catch (Throwable $e) { ar_error($e); ar_page_end(); require_once "../../inc/footer.php"; exit; }

$servers = array_unique(array_filter(array_map('u_server', $users))); sort($servers);
?>
<div class="anope-filters mb-2">
	<div class="btn-group btn-group-sm btn-group-toggle mr-2" data-toggle="buttons">
		<label class="btn btn-outline-primary active"><input type="radio" name="f" value="" checked> <?= th('All (%d)', count($users)) ?></label>
		<label class="btn btn-outline-primary"><input type="radio" name="f" value="ident"> <?= th('Identified') ?></label>
		<label class="btn btn-outline-primary"><input type="radio" name="f" value="anon"> <?= th('Not identified') ?></label>
		<label class="btn btn-outline-primary"><input type="radio" name="f" value="tls"> TLS</label>
		<label class="btn btn-outline-primary"><input type="radio" name="f" value="notls"> <?= th('No TLS') ?></label>
		<label class="btn btn-outline-primary"><input type="radio" name="f" value="away"> <?= th('Away') ?></label>
		<label class="btn btn-outline-primary"><input type="radio" name="f" value="oper"> <?= th('Opers') ?></label>
	</div>
	<select id="anope-server-filter" class="custom-select custom-select-sm w-auto">
		<option value=""><?= th('All servers') ?></option>
		<?php foreach ($servers as $s) echo '<option>' . ar_h($s) . '</option>'; ?>
	</select>
</div>
<table id="anope-users" class="table table-sm table-hover anope-dt w-100">
	<thead><tr><th><?= th('Nick') ?></th><th><?= th('Account') ?></th><th><?= th('Host') ?></th><th>IP</th><th><?= th('Server') ?></th><th><?= th('Connected') ?></th><th><?= th('Channels') ?></th><th><?= th('Status') ?></th></tr></thead>
	<tbody>
	<?php foreach ($users as $u):
		$acc = u_account($u); $tls = u_tls($u); $away = u_away($u); $op = u_oper($u) !== '' || str_contains(u_modes($u), 'o');
		$flags = ($acc !== '' ? 'ident ' : 'anon ') . ($tls ? 'tls ' : 'notls ') . ($away !== '' ? 'away ' : '') . ($op ? 'oper' : '');
	?>
		<tr data-flags="<?= $flags ?>">
			<td><a href="?u=<?= rawurlencode(u_nick($u)) ?>"><b><?= ar_h(u_nick($u)) ?></b></a></td>
			<td><?= $acc !== '' ? '<a href="' . ar_h($base . 'accounts.php?a=' . rawurlencode($acc)) . '">' . ar_h($acc) . '</a>' : '<span class="text-muted">—</span>' ?></td>
			<td class="small text-truncate" style="max-width:220px" title="<?= ar_h(u_host($u)) ?>"><?= ar_h(u_vhost($u) ?: u_host($u)) ?></td>
			<td class="small"><code><?= ar_h(u_ip($u)) ?></code></td>
			<td class="small"><?= ar_h(u_server($u)) ?></td>
			<td data-order="<?= u_signon($u) ?>" class="small"><?= ar_ago(u_signon($u)) ?></td>
			<td><?= count(u_channels($u)) ?></td>
			<td><?= $tls ? '<span title="TLS">🔒</span>' : '' ?><?= $away !== '' ? ' <span title="' . ar_h($away) . '">💤</span>' : '' ?><?= $op ? ' <span title="' . th('Oper') . '">🛡️</span>' : '' ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
ar_page_end();
require_once "../../inc/footer.php";
