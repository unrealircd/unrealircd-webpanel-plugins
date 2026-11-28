<?php
require_once "../../inc/common.php";
require_once __DIR__ . "/lib/client.php";
require_once "../../inc/header.php";

ar_page_start(t("Servers & opers"), "server");
$rpc = AnopeRPC::instance();
try {
	$servers = $rpc->servers();
	$opers = $rpc->opers();
	$users = $rpc->users();
} catch (Throwable $e) { ar_error($e); ar_page_end(); require_once "../../inc/footer.php"; exit; }

$count = array_count_values(array_filter(array_map('u_server', $users)));
$byName = [];
foreach ($servers as $s) $byName[s_name($s)] = $s;

function ar_node(array $s, array $count, string $icon): string
{
	$n = s_name($s);
	$flags = '';
	if (ar_get($s, ['ulined', 'ulined_server'], false)) $flags .= ' <span class="badge badge-info">U-line</span>';
	if (ar_get($s, ['juped', 'jupe'], false)) $flags .= ' <span class="badge badge-danger">' . th('juped') . '</span>';
	if (ar_get($s, ['synced'], true) === false) $flags .= ' <span class="badge badge-warning">' . th('syncing…') . '</span>';
	return '<span class="node"><i class="fa-solid fa-' . $icon . '"></i> <b>' . ar_h($n) . '</b>' . $flags
		. ' <span class="text-muted small">' . ar_h(s_desc($s)) . '</span>'
		. ' <span class="badge badge-light">' . th('%s users', ar_n($count[$n] ?? 0)) . '</span></span>';
}

function ar_tree(array $servers, string $up, array $count, array &$seen)
{
	$kids = array_filter($servers, fn($s) => s_uplink($s) === $up && !isset($seen[s_name($s)]));
	if (!$kids) return;
	echo '<ul class="anope-tree">';
	foreach ($kids as $s) {
		$seen[s_name($s)] = true;
		echo '<li>' . ar_node($s, $count, 'server');
		ar_tree($servers, s_name($s), $count, $seen);
		echo '</li>';
	}
	echo '</ul>';
}

// Roots: servers with no uplink or an unknown uplink
$roots = array_filter($servers, fn($s) => s_uplink($s) === '' || !isset($byName[s_uplink($s)]));
?>
<div class="row">
	<div class="col-lg-7 mb-3"><div class="card h-100"><div class="card-header"><?= th('Network topology (%d servers)', count($servers)) ?></div><div class="card-body">
		<?php $seen = [];
		echo '<ul class="anope-tree root">';
		foreach ($roots as $r) {
			$seen[s_name($r)] = true;
			echo '<li>' . ar_node($r, $count, 'sitemap');
			ar_tree($servers, s_name($r), $count, $seen);
			echo '</li>';
		}
		echo '</ul>'; ?>
	</div></div></div>
	<div class="col-lg-5 mb-3"><div class="card h-100"><div class="card-header"><?= th('Load per server') ?></div><div class="card-body"><?php ar_bars($count, 15); ?></div></div></div>
</div>

<h5 id="opers" class="mt-2"><i class="fa-solid fa-user-shield"></i> <?= th('Services operators') ?></h5>
<table class="table table-sm anope-dt w-100">
	<thead><tr><th><?= th('Name') ?></th><th><?= th('Type') ?></th><th><?= th('Privileges / commands') ?></th><th><?= th('Online') ?></th></tr></thead>
	<tbody>
	<?php
	$onlineAcc = [];
	foreach ($users as $u) if (($a = u_account($u)) !== '') $onlineAcc[strtolower($a)][] = u_nick($u);
	foreach ($opers as $o):
		$name = (string)ar_get($o, ['name', 'nick', 'account'], '?');
		$type = ar_get($o, ['type.name', 'opertype.name', 'type', 'opertype'], '');
		$privs = ar_get($o, ['privileges', 'privs', 'type.privileges', 'opertype.privileges'], []);
		$cmds = ar_get($o, ['commands', 'type.commands', 'opertype.commands'], []);
		$list = array_merge(is_array($privs) ? $privs : [$privs], is_array($cmds) ? $cmds : [$cmds]);
		$on = $onlineAcc[strtolower($name)] ?? [];
	?>
		<tr><td><b><?= ar_h($name) ?></b></td><td><span class="badge badge-dark"><?= ar_h(is_array($type) ? json_encode($type) : $type) ?></span></td>
		<td class="small"><?= ar_h(implode(', ', array_filter(array_map(fn($x) => is_scalar($x) ? (string)$x : '', $list)))) ?: '—' ?></td>
		<td><?= $on ? '🟢 ' . ar_h(implode(', ', $on)) : '<span class="text-muted">' . th('offline') . '</span>' ?></td></tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
ar_page_end();
require_once "../../inc/footer.php";
