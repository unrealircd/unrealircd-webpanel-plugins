<?php
require_once "../../inc/common.php";
require_once __DIR__ . "/lib/client.php";
require_once "../../inc/header.php";

$base = get_config('base_url') . 'plugins/anope_rpc/';
$rpc = AnopeRPC::instance();
$admin = ar_can_admin();

/* ---------- Single account ---------- */
if (!empty($_GET['a'])) {
	ar_page_start(t("Account %s", $_GET['a']), "id-card");
	try { $a = $rpc->call('anope.account', [$_GET['a']]); }
	catch (Throwable $e) { ar_error($e); ar_page_end(); require_once "../../inc/footer.php"; exit; }

	// Sessions currently online on this account
	$online = [];
	try {
		foreach ($rpc->users() as $u)
			if (strcasecmp(u_account($u), a_name($a)) === 0) $online[] = $u;
	} catch (Throwable $e) {}

	$skip = ['pass', 'password', 'passwd', 'hash'];
	?>
	<a href="<?= ar_h($base) ?>accounts.php" class="btn btn-sm btn-outline-secondary mb-3">← <?= th('All accounts') ?></a>
	<div class="row">
		<div class="col-lg-6 mb-3"><div class="card"><div class="card-body p-0"><table class="table table-sm mb-0">
			<tr><th style="width:35%"><?= th('Display name') ?></th><td><b><?= ar_h(a_name($a)) ?></b></td></tr>
			<tr><th><?= th('Unique identifier') ?></th><td><code><?= ar_h((string)ar_get($a, ['uniqueid', 'id', 'identifier'], '—')) ?></code></td></tr>
			<tr><th><?= th('Registered') ?></th><td><?= ar_date(a_reg($a)) ?> <span class="text-muted">(<?= ar_ago(a_reg($a)) ?>)</span></td></tr>
			<tr><th><?= th('Last seen') ?></th><td><?= ar_date(a_seen($a)) ?></td></tr>
			<?php if ($admin): ?><tr><th><?= th('E-mail') ?></th><td><?= ar_h(a_email($a) ?: '—') ?></td></tr><?php endif; ?>
			<tr><th><?= th('Oper type') ?></th><td><?= ar_h((string)ar_get($a, ['opertype.name', 'opertype'], '—')) ?></td></tr>
			<tr><th><?= th('Grouped nicks') ?></th><td><?= ar_h(implode(', ', a_nicks($a)) ?: '—') ?></td></tr>
		</table></div></div></div>
		<div class="col-lg-6 mb-3"><div class="card"><div class="card-header"><?= th('Online now (%d)', count($online)) ?></div><div class="card-body p-0">
			<?php if (!$online): ?><div class="p-3 text-muted"><?= th('No active session.') ?></div><?php else: ?>
			<table class="table table-sm mb-0"><?php foreach ($online as $u): ?>
				<tr><td><a href="<?= ar_h($base . 'users.php?u=' . rawurlencode(u_nick($u))) ?>"><?= ar_h(u_nick($u)) ?></a></td>
				<td class="small"><?= ar_h(u_server($u)) ?></td><td class="small"><?= ar_ago(u_signon($u)) ?></td><td><?= u_tls($u) ? '🔒' : '' ?></td></tr>
			<?php endforeach; ?></table><?php endif; ?>
		</div></div></div>
	</div>
	<details class="mb-3"><summary><?= th('Raw JSON response') ?></summary><pre class="anope-json"><?php
		$clean = $a;
		array_walk_recursive($clean, function (&$v, $k) use ($skip, $admin) {
			if (in_array(strtolower((string)$k), $skip, true) || (!$admin && in_array(strtolower((string)$k), ['email', 'mail'], true))) $v = '•••';
		});
		echo ar_h(json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	?></pre></details>
	<?php
	ar_page_end(); require_once "../../inc/footer.php"; exit;
}

/* ---------- List ---------- */
ar_page_start(t("Registered accounts"), "id-card");
try { $accs = $rpc->accounts(); }
catch (Throwable $e) { ar_error($e); ar_page_end(); require_once "../../inc/footer.php"; exit; }

// Registrations per month (last 12 months)
$months = []; $labels = [];
for ($i = 11; $i >= 0; $i--) { $k = date('Y-m', strtotime("first day of -$i month")); $months[$k] = 0; $labels[] = date('m/y', strtotime("first day of -$i month")); }
foreach ($accs as $a) { $k = date('Y-m', a_reg($a) ?: 0); if (isset($months[$k])) $months[$k]++; }

$dormant = count(array_filter($accs, fn($a) => a_seen($a) > 0 && time() - a_seen($a) > 180 * 86400));
$week = count(array_filter($accs, fn($a) => a_reg($a) > time() - 7 * 86400));
?>
<div class="row">
	<div class="col-lg-8 mb-3"><div class="card h-100"><div class="card-header"><?= th('Registrations per month (last 12 months)') ?></div>
		<div class="card-body"><?php ar_histo(array_values($months), $labels); ?></div></div></div>
	<div class="col-lg-4 mb-3"><div class="card h-100"><div class="card-body anope-mini">
		<div><span class="v"><?= ar_n(count($accs)) ?></span> <?= th('accounts') ?></div>
		<div><span class="v text-success"><?= ar_n($week) ?></span> <?= th('new this week') ?></div>
		<div><span class="v text-muted"><?= ar_n($dormant) ?></span> <?= th('inactive for over 6 months') ?></div>
	</div></div></div>
</div>
<table id="anope-accounts" class="table table-sm table-hover anope-dt w-100">
	<thead><tr><th><?= th('Account') ?></th><th><?= th('Registered') ?></th><th><?= th('Last seen') ?></th><th><?= th('Nicks') ?></th><?= $admin ? '<th>' . th('E-mail') . '</th>' : '' ?></tr></thead>
	<tbody>
	<?php foreach ($accs as $a): ?>
		<tr>
			<td><a href="?a=<?= rawurlencode(a_name($a)) ?>"><b><?= ar_h(a_name($a)) ?></b></a></td>
			<td data-order="<?= a_reg($a) ?>" class="small"><?= ar_date(a_reg($a)) ?></td>
			<td data-order="<?= a_seen($a) ?>" class="small"><?= a_seen($a) ? th('%s ago', ar_ago(a_seen($a))) : '—' ?></td>
			<td class="small"><?= ar_h(implode(', ', array_slice(a_nicks($a), 0, 5))) ?></td>
			<?= $admin ? '<td class="small">' . ar_h(a_email($a)) . '</td>' : '' ?>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
ar_page_end();
require_once "../../inc/footer.php";
