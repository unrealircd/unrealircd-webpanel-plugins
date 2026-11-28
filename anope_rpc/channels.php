<?php
require_once "../../inc/common.php";
require_once __DIR__ . "/lib/client.php";
require_once "../../inc/header.php";

$base = get_config('base_url') . 'plugins/anope_rpc/';
$rpc = AnopeRPC::instance();

$prefixNames = ['~' => t('Founders'), '&' => t('Protected'), '@' => t('Operators'), '%' => t('Half-ops'), '+' => t('Voiced'), '' => t('Members')];
$ulink = fn($n) => '<a href="' . ar_h($base . 'users.php?u=' . rawurlencode($n)) . '">' . ar_h($n) . '</a>';

/* ---------- Single channel ---------- */
if (!empty($_GET['c'])) {
	$name = $_GET['c'];
	if ($name[0] !== '#' && $name[0] !== '&') $name = '#' . $name;
	ar_page_start(t("Channel %s", $name), "hashtag");
	try { $c = $rpc->call('anope.channel', [$name]); }
	catch (Throwable $e) { ar_error($e); ar_page_end(); require_once "../../inc/footer.php"; exit; }

	$members = c_users($c);
	$groups = [];
	foreach ($members as [$p, $n]) {
		$k = $p === '' ? '' : $p[0];
		if (!isset($prefixNames[$k])) $k = '';
		$groups[$k][] = $n;
	}
	$nicks = array_column($members, 1);
	?>
	<a href="<?= ar_h($base) ?>channels.php" class="btn btn-sm btn-outline-secondary mb-3">← <?= th('All channels') ?></a>
	<div class="card mb-3"><div class="card-body">
		<div class="anope-topic"><?= c_topic($c) !== '' ? ar_irc_html(c_topic($c)) : '<span class="text-muted">' . th('No topic') . '</span>' ?></div>
		<div class="small text-muted mt-1">
			<?php if (c_topic_setter($c)): ?><?= c_topic_time($c) ? th('Set by %s, %s ago', c_topic_setter($c), ar_ago(c_topic_time($c))) : th('Set by %s', c_topic_setter($c)) ?> · <?php endif; ?>
			<?= th('Created %s (%s ago)', ar_date(c_created($c)), ar_ago(c_created($c))) ?> · <?= th('Modes') ?> <code><?= ar_h(c_modes($c)) ?></code> · <?= th('%d members', count($members)) ?>
		</div>
	</div></div>

	<div class="row">
	<?php foreach ($prefixNames as $k => $label): if (empty($groups[$k])) continue; natcasesort($groups[$k]); ?>
		<div class="col-md-6 col-xl-4 mb-3"><div class="card h-100"><div class="card-header"><?= ar_h($label) ?> <span class="badge badge-secondary"><?= count($groups[$k]) ?></span></div>
		<div class="card-body small"><?= implode(', ', array_map($ulink, $groups[$k])) ?></div></div></div>
	<?php endforeach; ?>
	</div>

	<div class="card mb-3"><div class="card-header"><?= th('Members in common with another channel') ?></div><div class="card-body">
		<form class="form-inline mb-2" method="get">
			<input type="hidden" name="c" value="<?= ar_h($name) ?>">
			<input class="form-control form-control-sm mr-2" name="vs" placeholder="#<?= th('other-channel') ?>" value="<?= ar_h($_GET['vs'] ?? '') ?>">
			<button class="btn btn-sm btn-primary"><?= th('Compare') ?></button>
		</form>
		<?php if (!empty($_GET['vs'])) {
			try {
				$o = $rpc->call('anope.channel', [$_GET['vs']]);
				$common = array_values(array_intersect($nicks, array_column(c_users($o), 1)));
				natcasesort($common);
				echo '<p class="mb-1">' . th('%d member(s) in common with %s:', count($common), c_name($o)) . '</p><div class="small">' . (implode(', ', array_map($ulink, $common)) ?: '—') . '</div>';
			} catch (Throwable $e) { echo '<div class="text-danger small">' . ar_h($e->getMessage()) . '</div>'; }
		} ?>
	</div></div>

	<details class="mb-3"><summary><?= th('Raw JSON response') ?></summary><pre class="anope-json"><?= ar_h(json_encode($c, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre></details>
	<?php
	ar_page_end(); require_once "../../inc/footer.php"; exit;
}

/* ---------- List ---------- */
ar_page_start(t("Channels"), "hashtag");
try { $chans = $rpc->channels(); }
catch (Throwable $e) { ar_error($e); ar_page_end(); require_once "../../inc/footer.php"; exit; }

$noTopic = count(array_filter($chans, fn($c) => c_topic($c) === ''));
$total = array_sum(array_map(fn($c) => count(c_users($c)), $chans));
?>
<div class="mb-2 small text-muted"><?= th('%d channels · %s memberships · %d without a topic', count($chans), ar_n($total), $noTopic) ?> ·
	<a href="#" class="anope-filter-link" data-filter="notopic"><?= th('Show only channels without a topic') ?></a></div>
<table id="anope-channels" class="table table-sm table-hover anope-dt w-100">
	<thead><tr><th><?= th('Channel') ?></th><th><?= th('Members') ?></th><th><?= th('Ops') ?></th><th><?= th('Topic') ?></th><th><?= th('Modes') ?></th><th><?= th('Created') ?></th></tr></thead>
	<tbody>
	<?php foreach ($chans as $c):
		$m = c_users($c);
		$ops = count(array_filter($m, fn($x) => strpbrk($x[0], '~&@') !== false));
	?>
		<tr data-flags="<?= c_topic($c) === '' ? 'notopic' : '' ?>">
			<td><a href="?c=<?= rawurlencode(c_name($c)) ?>"><b><?= ar_h(c_name($c)) ?></b></a></td>
			<td data-order="<?= count($m) ?>"><?= count($m) ?></td>
			<td><?= $ops ?></td>
			<td class="small text-truncate" style="max-width:420px" data-search="<?= ar_h(ar_irc_strip(c_topic($c))) ?>" title="<?= ar_h(ar_irc_strip(c_topic($c))) ?>"><?= ar_irc_html(c_topic($c)) ?></td>
			<td class="small"><code><?= ar_h(c_modes($c)) ?></code></td>
			<td data-order="<?= c_created($c) ?>" class="small"><?= ar_ago(c_created($c)) ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
ar_page_end();
require_once "../../inc/footer.php";
