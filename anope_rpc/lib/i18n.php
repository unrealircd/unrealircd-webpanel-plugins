<?php
/*
 * Minimal i18n: English source strings are the keys (gettext style).
 * lang/xx.php returns ['English string' => 'Translation', ...].
 * Missing translations fall back to English.
 */

const ANOPE_LANGS = [
	'en' => 'English',
	'fr' => 'Français',
	'es' => 'Español',
	'nl' => 'Nederlands',
	'de' => 'Deutsch',
	'pl' => 'Polski',
];

function ar_lang(): string
{
	static $lang = null;
	if ($lang !== null) return $lang;

	// 1. explicit switch (?anope_lang=xx) remembered in a cookie
	if (isset($_GET['anope_lang']) && isset(ANOPE_LANGS[$_GET['anope_lang']])) {
		$lang = $_GET['anope_lang'];
		if (!headers_sent())
			setcookie('anope_lang', $lang, ['expires' => time() + 31536000, 'path' => '/', 'samesite' => 'Lax']);
		return $lang;
	}
	if (isset($_COOKIE['anope_lang']) && isset(ANOPE_LANGS[$_COOKIE['anope_lang']]))
		return $lang = $_COOKIE['anope_lang'];

	// 2. language forced in the panel settings
	$cfg = function_exists('get_config') ? get_config('anope_rpc') : null;
	$forced = is_array($cfg) ? ($cfg['lang'] ?? 'auto') : 'auto';
	if (isset(ANOPE_LANGS[$forced]))
		return $lang = $forced;

	// 3. browser Accept-Language, 4. English
	foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '') as $part) {
		$code = strtolower(substr(trim($part), 0, 2));
		if (isset(ANOPE_LANGS[$code])) return $lang = $code;
	}
	return $lang = 'en';
}

/** Translate; extra arguments are passed to sprintf */
function t(string $s, ...$args): string
{
	static $dict = [];
	$l = ar_lang();
	if (!isset($dict[$l])) {
		$f = __DIR__ . "/../lang/$l.php";
		$dict[$l] = ($l !== 'en' && is_file($f)) ? (include $f) : [];
		if (!is_array($dict[$l])) $dict[$l] = [];
	}
	$out = $dict[$l][$s] ?? $s;
	return $args ? vsprintf($out, $args) : $out;
}

/** Escaped translation */
function th(string $s, ...$args): string { return htmlspecialchars(t($s, ...$args), ENT_QUOTES, 'UTF-8'); }

/** Locale-aware number formatting */
function ar_n($n): string
{
	[$dec, $th] = match (ar_lang()) {
		'en' => ['.', ','],
		'de', 'nl', 'es' => [',', '.'],
		default => [',', ' '], // fr, pl
	};
	return number_format((float)$n, 0, $dec, $th);
}

function ar_date(int $ts): string
{
	if ($ts <= 0) return '—';
	return date(ar_lang() === 'en' ? 'Y-m-d H:i' : (ar_lang() === 'de' || ar_lang() === 'pl' ? 'd.m.Y H:i' : 'd/m/Y H:i'), $ts);
}

/** Small language switcher keeping the current query string */
function ar_lang_switcher(): string
{
	$q = $_GET; $html = '<div class="dropdown d-inline-block ml-2"><button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" type="button">'
		. '<i class="fa-solid fa-language"></i> ' . strtoupper(ar_lang()) . '</button><div class="dropdown-menu dropdown-menu-right">';
	foreach (ANOPE_LANGS as $code => $name) {
		$q['anope_lang'] = $code;
		$html .= '<a class="dropdown-item' . ($code === ar_lang() ? ' active' : '') . '" href="?' . htmlspecialchars(http_build_query($q), ENT_QUOTES) . '">' . htmlspecialchars($name) . '</a>';
	}
	return $html . '</div></div>';
}
