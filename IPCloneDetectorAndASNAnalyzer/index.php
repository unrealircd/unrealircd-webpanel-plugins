<?php
require_once "../../inc/common.php";
require_once "../../inc/header.php";

/*
 * View of the Good ASN column
 */
$asnIsGood = false;

function getCountryNames()
{
    return [
        'AF' => 'Afghanistan',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BR' => 'Brazil',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'CV' => 'Cabo Verde',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Congo, Democratic Republic of the',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Côte d\'Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CW' => 'Curaçao',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'SZ' => 'Eswatini',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island and McDonald Islands',
        'VA' => 'Holy See',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KP' => 'Korea, Democratic People\'s Republic of',
        'KR' => 'Korea, Republic of',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'North Macedonia',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia (Federated States of)',
        'MD' => 'Moldova',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'MM' => 'Myanmar',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestine, State of',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Réunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthélemy',
        'SH' => 'Saint Helena',
        'KN' => 'Saint Kitts and Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent and the Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SX' => 'Sint Maarten',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'SS' => 'South Sudan',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard and Jan Mayen',
        'SZ' => 'Eswatini',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania, United Republic of',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela',
        'VN' => 'Viet Nam',
        'VG' => 'British Virgin Islands',
        'VI' => 'United States Virgin Islands',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    ];
}

// Function to get the country name from the code
function getCountryName($code)
{
    $countries = getCountryNames();
    return isset($countries[$code]) ? $countries[$code] : 'Unknown country code';
}

require_once(UPATH . "/inc/connection.php");
global $rpc;
$users = $rpc->user()->getAll();
$server_ban = $rpc->serverban()->getAll();

/* ------------------------------------------------------------------ *
 * Helpers
 * ------------------------------------------------------------------ */
function irc_e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function irc_flag($code)
{
    if (empty($code)) {
        return '-';
    }
    $c = irc_e($code);
    return '<span class="irc-cc">' . $c . '</span> <img class="irc-flag" loading="lazy" src="https://flagcdn.com/48x36/' . strtolower($c) . '.png" width="20" height="15" alt="' . $c . '">';
}

function readFileContent($filePath)
{
    $content = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($content === false) {
        echo "Impossible to open the file.";
        return [];
    }
    return $content;
}

if ($asnIsGood)
    $asnFileContent = readFileContent('badasn/list.txt');

function asnExists($asn, $fileContent)
{
    foreach ($fileContent as $line) {
        if (trim($line) == $asn) {
            return true;
        }
    }
    return false;
}

/* IPv4 / IPv6 statistics per ASN (computed once, in a single pass) */
$asnIpStats = [];
foreach ($users as $obj) {
    if (isset($obj->geoip->asn) && isset($obj->ip)) {
        $key = (string)$obj->geoip->asn;
        if (!isset($asnIpStats[$key])) {
            $asnIpStats[$key] = ['v4' => 0, 'v6' => 0, 'total' => 0];
        }
        $asnIpStats[$key]['total']++;
        if (filter_var($obj->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $asnIpStats[$key]['v4']++;
        } elseif (filter_var($obj->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $asnIpStats[$key]['v6']++;
        }
    }
}

function ipFromAsn($v, $asn)
{
    global $asnIpStats;
    $s = $asnIpStats[(string)$asn] ?? ['v4' => 0, 'v6' => 0, 'total' => 0];

    $ipv4Percentage = ($s['total'] > 0) ? ($s['v4'] / $s['total']) * 100 : 0;
    $ipv6Percentage = ($s['total'] > 0) ? ($s['v6'] / $s['total']) * 100 : 0;

    $css = ' irc-pill-neutral';
    if ($ipv4Percentage > $ipv6Percentage) {
        $css = ($v == 4) ? ' irc-pill-ok' : ' irc-pill-bad';
    } elseif ($ipv6Percentage > $ipv4Percentage) {
        $css = ($v == 6) ? ' irc-pill-ok' : ' irc-pill-bad';
    }

    if ($v == 4) {
        $count = $s['v4'];
        $percentage = $ipv4Percentage;
    } else {
        $count = $s['v6'];
        $percentage = $ipv6Percentage;
    }

    return "<span class=\"irc-num\">$count</span><span class=\"irc-pill$css\">" . round($percentage, 2) . "%</span>";
}

/* Clones : same IP, or same first 4 segments of an IPv6 */
function irc_find_clones($users, $ipv6Prefix = false)
{
    $list = [];
    foreach ($users as $obj) {
        if (!isset($obj->ip)) {
            continue;
        }
        $ip = $obj->ip;
        if ($ipv6Prefix) {
            if (strpos($ip, ':') === false) {
                continue;
            }
            $ip = implode(':', array_slice(explode(':', $ip), 0, 4));
        }
        if (!isset($list[$ip])) {
            $list[$ip] = ['count' => 0, 'names' => []];
        }
        $list[$ip]['count']++;
        $list[$ip]['names'][] = $obj->name ?? '';
    }

    $duplicates = [];
    foreach ($list as $ip => $info) {
        if ($info['count'] > 1) {
            $duplicates[] = ['ip' => (string)$ip, 'count' => $info['count'], 'names' => $info['names']];
        }
    }
    usort($duplicates, function ($a, $b) {
        return $b['count'] - $a['count'];
    });
    return $duplicates;
}

function irc_render_clones($duplicates)
{
    echo "<div class='irc-scroll irc-scroll-short'><table class='irc-table'>
        <thead>
            <tr>
                <th>IP</th>
                <th>Number<br>of<br>duplicates</th>
                <th class='irc-left'>List of names</th>
            </tr>
        </thead>
        <tbody>";

    if (empty($duplicates)) {
        echo "<tr><td colspan='3' class='irc-empty'>No clones found</td></tr>";
    }

    foreach ($duplicates as $entry) {
        $names = '';
        foreach ($entry['names'] as $n) {
            $names .= '<a class="irc-user" href="' . irc_e(get_config("base_url")) . 'users/details.php?nick=' . urlencode($n) . '">' . irc_e($n) . '</a>';
        }
        echo "<tr>
            <td><code>" . irc_e($entry['ip']) . "</code></td>
            <td><span class='irc-pill irc-pill-bad'>" . (int)$entry['count'] . "</span></td>
            <td class='irc-left'><div class='irc-users'>$names</div></td>
        </tr>";
    }

    echo "</tbody></table></div>";
}

/* Server bans : $asnMode = true  -> '~asn:' bans
 *               $asnMode = false -> other types of '~' bans */
function irc_render_bans($server_ban, $asnMode)
{
    echo "<div class='irc-scroll irc-scroll-short'><table class='irc-table'>
        <thead>
            <tr>
                <th>Type ban</th>
                <th>Found</th>
                <th>Affected users</th>
                <th>duration</th>
            </tr>
        </thead>
        <tbody>";

    $found = 0;
    foreach ($server_ban as $obj) {
        if (!isset($obj->name)) {
            continue;
        }
        $name = $obj->name;
        $hasAsn = strpos($name, "~asn:") !== false;
        $match = $asnMode ? $hasAsn : (!$hasAsn && strpos($name, "~") !== false);
        if (!$match) {
            continue;
        }
        $found++;
        $affected = (strpos($name, '%') === 0)
            ? '<a href="https://www.unrealircd.org/docs/Soft_ban" target="_blank" rel="noopener">unregistered</a>'
            : 'all';

        echo "<tr>
            <td><span class='irc-pill irc-pill-bad'>" . irc_e($obj->type ?? '') . "</span></td>
            <td><code>" . irc_e($name) . "</code></td>
            <td>$affected</td>
            <td>" . irc_e($obj->duration_string ?? '') . "</td>
        </tr>";
    }

    if ($found === 0) {
        echo "<tr><td colspan='4' class='irc-empty'>No ban found</td></tr>";
    }

    echo "</tbody></table></div>";
}

//print_r($users);

// Attempt to display an alert if no GeoIP item is found
foreach ($users as $entry) {
    if (!isset($entry->geoip)) {
        echo '
        <div class="alert alert-danger" role="alert">
        GeoIP does not seem to be configured in the UnrealIRCd configuration. Update modules.conf, and you can refer to an example in <a href="https://github.com/unrealircd/unrealircd/blob/unreal60_dev/doc/conf/modules.default.conf" target="_blank">modules.default.conf</a>.
        </div>';
    }
    break;
}

/* ------------------------------------------------------------------ *
 * Data computation
 * ------------------------------------------------------------------ */

// --- ASN duplicates
$asnCounts = [];
$asnCount = 0;
foreach ($users as $entry) {
    $geoip = $entry->geoip ?? "";
    $asn = $geoip->asn ?? "";
    $asname = $geoip->asname ?? "localhost ?";
    $country_code = $geoip->country_code ?? "";

    if (!isset($asnCounts[$asn])) {
        $asnCounts[$asn] = [
            'asn' => $asn,
            'asname' => $asname,
            'country_code' => $country_code,
            'count' => 0
        ];
        $asnCount++;
    }
    $asnCounts[$asn]['count']++;
}
$asnCounts = array_values($asnCounts);
usort($asnCounts, function ($a, $b) {
    return $b['count'] - $a['count'];
});

// --- Countries
$countryCounts = [];
foreach ($users as $user) {
    if (!isset($user->geoip->country_code))
        continue;

    $countryCode = $user->geoip->country_code;
    if (isset($countryCounts[$countryCode])) {
        $countryCounts[$countryCode]++;
    } else {
        $countryCounts[$countryCode] = 1;
    }
}
arsort($countryCounts);

$countries = [];
$counts = [];
foreach ($countryCounts as $countryCode => $count) {
    $countries[] = (!empty($countryCode) ? $countryCode : 'localhost');
    $counts[] = $count;
}
$totalUsers = array_sum($countryCounts);

// --- Clones
$cloneIpList = irc_find_clones($users, false);
$cloneIpv6List = irc_find_clones($users, true);

// --- Statistics users
$ipv4Count = 0;
$ipv6Count = 0;
$usersCount = 0;
$accountCount = 0;
$noAccountCount = 0;

foreach ($users as $obj) {
    $usersCount++;
    if (isset($obj->ip)) {
        if (filter_var($obj->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipv4Count++;
        } elseif (filter_var($obj->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ipv6Count++;
        }
    }

    if (isset($obj->user)) {
        if (isset($obj->user->account)) {
            $accountCount++;
        } else {
            $noAccountCount++;
        }
    }
}

// avoid division by zero
if ($usersCount > 0) {
    $ipv4Pct = round(($ipv4Count / $usersCount) * 100, 2);
    $ipv6Pct = round(($ipv6Count / $usersCount) * 100, 2);
    $accountPct = round(($accountCount / $usersCount) * 100, 2);
    $noAccountPct = round(($noAccountCount / $usersCount) * 100, 2);
    $ipv4CountPercentage = $ipv4Pct . '%';
    $ipv6CountPercentage = $ipv6Pct . '%';
    $accountCountPercentage = $accountPct . '%';
    $noAccountCountPercentage = $noAccountPct . '%';
} else {
    $ipv4Pct = $ipv6Pct = $accountPct = $noAccountPct = 0;
    $ipv4CountPercentage = $ipv6CountPercentage = $accountCountPercentage = $noAccountCountPercentage = '-';
}

?>
<style>
    .irc {
        --irc-accent: #3b6cf6;
        --irc-accent-soft: rgba(59, 108, 246, .12);
        --irc-border: rgba(127, 127, 127, .28);
        --irc-tint: rgba(127, 127, 127, .07);
        --irc-th: rgba(127, 127, 127, .16);
        --irc-ok: #198754;
        --irc-bad: #dc3545;
        --irc-neutral: #6c757d;
        --irc-radius: 14px;
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px 16px 48px;
    }

    .irc *,
    .irc *::before,
    .irc *::after {
        box-sizing: border-box;
    }

    /* ---------- Header ---------- */
    .irc-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .irc-header h2 {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 700;
        letter-spacing: -.01em;
    }

    .irc-header p {
        margin: 2px 0 0;
        opacity: .65;
        font-size: .92rem;
    }

    #SearchinUsers {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 9px 20px;
        font-weight: 600;
        box-shadow: 0 4px 14px rgba(59, 108, 246, .28);
    }

    /* ---------- KPI ---------- */
    .irc-kpis {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .irc-kpi {
        position: relative;
        padding: 16px 18px 14px 22px;
        border: 1px solid var(--irc-border);
        border-radius: var(--irc-radius);
        background: linear-gradient(var(--irc-tint), var(--irc-tint)), var(--bs-body-bg, transparent);
        overflow: hidden;
    }

    .irc-kpi::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: var(--k, var(--irc-accent));
    }

    .irc-kpi-label {
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        opacity: .65;
        font-weight: 600;
    }

    .irc-kpi-value {
        font-size: 1.9rem;
        font-weight: 700;
        line-height: 1.15;
        margin-top: 2px;
    }

    .irc-kpi-sub {
        font-size: .82rem;
        opacity: .65;
    }

    /* ---------- Layout ---------- */
    .irc-main {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
        gap: 22px;
        align-items: start;
    }

    .irc-col {
        display: flex;
        flex-direction: column;
        gap: 22px;
        min-width: 0;
    }

    .irc-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
        margin-top: 22px;
        align-items: start;
    }

    @media screen and (max-width: 1100px) {

        .irc-main,
        .irc-grid-2 {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    /* ---------- Cards ---------- */
    .irc-card {
        border: 1px solid var(--irc-border);
        border-radius: var(--irc-radius);
        background: linear-gradient(var(--irc-tint), var(--irc-tint)), var(--bs-body-bg, transparent);
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
        overflow: hidden;
        min-width: 0;
    }

    .irc-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        padding: 14px 18px;
        border-bottom: 1px solid var(--irc-border);
    }

    .irc-card-head h4 {
        margin: 0;
        font-size: 1.02rem;
        font-weight: 650;
        line-height: 1.3;
    }

    .irc-card-body {
        padding: 16px 18px;
    }

    .irc-card-foot {
        padding: 10px 18px;
        border-top: 1px solid var(--irc-border);
        font-size: .88rem;
        opacity: .8;
    }

    .irc-card-foot p {
        margin: 0;
    }

    .irc-toolbar {
        padding: 10px 18px;
        border-bottom: 1px solid var(--irc-border);
    }

    .irc-toolbar input {
        width: 100%;
        padding: 8px 14px;
        border-radius: 999px;
        border: 1px solid var(--irc-border);
        background: transparent;
        color: inherit;
        outline: none;
    }

    .irc-toolbar input:focus {
        border-color: var(--irc-accent);
        box-shadow: 0 0 0 3px var(--irc-accent-soft);
    }

    /* ---------- Tables ---------- */
    .irc-scroll {
        overflow: auto;
    }

    .irc-scroll-tall {
        max-height: 78vh;
    }

    .irc-scroll-short {
        max-height: 420px;
    }

    table.irc-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
        font-size: .92rem;
        color: inherit;
    }

    table.irc-table th,
    table.irc-table td {
        padding: 9px 12px;
        text-align: center;
        vertical-align: middle;
        border: 0;
        border-bottom: 1px solid var(--irc-border);
    }

    table.irc-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: linear-gradient(var(--irc-th), var(--irc-th)), var(--bs-body-bg, #fff);
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 700;
        white-space: nowrap;
    }

    table.irc-table tbody tr:last-child td {
        border-bottom: 0;
    }

    table.irc-table tbody tr:nth-child(even) td {
        background: rgba(127, 127, 127, .05);
    }

    table.irc-table tbody tr:hover td {
        background: var(--irc-accent-soft);
    }

    table.irc-table td.irc-left,
    table.irc-table th.irc-left {
        text-align: left;
    }

    table.irc-table tr.irc-total td {
        font-weight: 700;
        border-top: 2px solid var(--irc-border);
        background: var(--irc-th);
    }

    table.irc-table tr.irc-sep td {
        border-top: 3px solid var(--irc-border);
    }

    table.irc-table td.irc-empty {
        padding: 22px;
        opacity: .6;
        font-style: italic;
    }

    table.irc-table code {
        font-size: .85em;
        padding: 2px 6px;
        border-radius: 6px;
        background: var(--irc-tint);
        word-break: break-all;
    }

    /* ---------- Bits & pieces ---------- */
    .irc-btn,
    .irc-count {
        display: inline-block;
        min-width: 44px;
        padding: 3px 12px;
        border-radius: 999px;
        border: 1px solid var(--irc-accent);
        background: transparent;
        color: var(--irc-accent);
        font-weight: 600;
        font-size: .88rem;
        line-height: 1.5;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s, color .15s, transform .1s;
    }

    .irc-btn:hover,
    .irc-count:hover {
        background: var(--irc-accent);
        color: #fff;
        text-decoration: none;
    }

    .irc-count:active {
        transform: scale(.95);
    }

    .irc-link {
        color: inherit;
        text-decoration: none;
        border-bottom: 1px dotted rgba(127, 127, 127, .7);
    }

    .irc-link:hover {
        color: var(--irc-accent);
        border-bottom-color: var(--irc-accent);
    }

    .irc-pill {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: .76rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.5;
    }

    .irc-pill-ok {
        background: var(--irc-ok);
    }

    .irc-pill-bad {
        background: var(--irc-bad);
    }

    .irc-pill-neutral {
        background: var(--irc-neutral);
    }

    .irc-num {
        display: block;
        font-weight: 600;
    }

    .irc-pill+.irc-num,
    .irc-num+.irc-pill {
        margin-top: 2px;
    }

    .irc-cc {
        font-weight: 600;
    }

    .irc-flag {
        vertical-align: -2px;
        border-radius: 3px;
        box-shadow: 0 0 0 1px rgba(127, 127, 127, .35);
    }

    .irc-users {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .irc-user {
        padding: 1px 9px;
        border-radius: 999px;
        background: var(--irc-accent-soft);
        color: inherit;
        text-decoration: none;
        font-size: .85rem;
    }

    .irc-user:hover {
        background: var(--irc-accent);
        color: #fff;
        text-decoration: none;
    }

    .irc-bar {
        display: inline-block;
        vertical-align: middle;
        width: 70px;
        height: 7px;
        margin-left: 8px;
        border-radius: 999px;
        background: rgba(127, 127, 127, .25);
        overflow: hidden;
    }

    .irc-bar span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: var(--irc-accent);
    }

    .irc-chart-wrap {
        max-width: 460px;
        margin: 0 auto;
    }

    /* ---------- Gender colours (unchanged) ---------- */
    .text_boy {
        color: #0e77ff;
    }

    .text_girl {
        color: #ff0592;
    }

    /* ---------- Modals ---------- */
    #searchModal .modal-content,
    #customModal .modal-content {
        border-radius: var(--irc-radius, 14px);
        overflow: hidden;
    }

    #searchModal .modal-header,
    #customModal .modal-header {
        border-bottom: 1px solid rgba(127, 127, 127, .28);
    }

    #customModal .modal-body {
        max-height: 70vh;
        overflow: auto;
    }

    #results ul {
        list-style: none;
        padding: 0;
        margin: 10px 0 0;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    #results ul li a {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 999px;
        background: rgba(59, 108, 246, .12);
        text-decoration: none;
        font-weight: 600;
    }

    #results ul li a:hover {
        background: #3b6cf6;
    }

    #results .irc-hint ul {
        display: block;
        list-style: disc;
        padding-left: 22px;
    }

    #customModal table {
        width: 100%;
        font-size: .9rem;
    }

    #customModal th,
    #customModal td {
        padding: 7px 10px;
        text-align: center;
        vertical-align: middle;
    }

    #customModal td.irc-sub {
        font-size: .82rem;
        opacity: .75;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<div class="irc">

    <!-- ===== Header ===== -->
    <div class="irc-header">
        <div>
            <h2>IP Clone Detector &amp; ASN Analyzer</h2>
            <p>Overview of connected users: networks, countries, clones and server bans.</p>
        </div>
        <div class="bd-example m-0 border-0">
            <button id="SearchinUsers" type="button" class="btn btn-primary" data-toggle="modal" data-target="#searchModal">&#128269; Search in Users</button>
        </div>
    </div>

    <!-- ===== KPI ===== -->
    <div class="irc-kpis">
        <div class="irc-kpi" style="--k:#3b6cf6">
            <div class="irc-kpi-label">Total users</div>
            <div class="irc-kpi-value"><?= (int)$usersCount ?></div>
            <div class="irc-kpi-sub">connected now</div>
        </div>
        <div class="irc-kpi" style="--k:#198754">
            <div class="irc-kpi-label">IPv4</div>
            <div class="irc-kpi-value"><?= (int)$ipv4Count ?></div>
            <div class="irc-kpi-sub"><?= irc_e($ipv4CountPercentage) ?></div>
        </div>
        <div class="irc-kpi" style="--k:#7048e8">
            <div class="irc-kpi-label">IPv6</div>
            <div class="irc-kpi-value"><?= (int)$ipv6Count ?></div>
            <div class="irc-kpi-sub"><?= irc_e($ipv6CountPercentage) ?></div>
        </div>
        <div class="irc-kpi" style="--k:#f59f00">
            <div class="irc-kpi-label">With account</div>
            <div class="irc-kpi-value"><?= (int)$accountCount ?></div>
            <div class="irc-kpi-sub"><?= irc_e($accountCountPercentage) ?></div>
        </div>
        <div class="irc-kpi" style="--k:#dc3545">
            <div class="irc-kpi-label">Without account</div>
            <div class="irc-kpi-value"><?= (int)$noAccountCount ?></div>
            <div class="irc-kpi-sub"><?= irc_e($noAccountCountPercentage) ?></div>
        </div>
        <div class="irc-kpi" style="--k:#0ca5b0">
            <div class="irc-kpi-label">ASNs</div>
            <div class="irc-kpi-value"><?= (int)$asnCount ?></div>
            <div class="irc-kpi-sub"><?= count($countryCounts) ?> countries</div>
        </div>
    </div>

    <div class="irc-main">

        <!-- ===== Left column : ASN ===== -->
        <div class="irc-col">
            <section class="irc-card">
                <div class="irc-card-head">
                    <h4>Number of ASN duplicates sorted from largest to smallest</h4>
                </div>
                <div class="irc-toolbar">
                    <input type="search" id="asnFilter" placeholder="Filter by ASN, name or country…" autocomplete="off">
                </div>
                <div class="irc-scroll irc-scroll-tall">
                    <table class="irc-table" id="asnTable">
                        <thead>
                            <tr>
                                <th>ASN</th>
                                <th class="irc-left">ASName</th>
                                <th>Country Code</th>
                                <th>Count</th>
                                <th>IPv4</th>
                                <th>IPv6</th>
                                <?php if ($asnIsGood) echo "<th>Good ASN</th>"; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asnCounts as $info) : ?>
                                <tr>
                                    <td>
                                        <?php if (empty($info['asn'])) : ?>
                                            -
                                        <?php else : ?>
                                            <a class="irc-btn" href="<?= irc_e(get_config("base_url")) ?>tools/ip-whois.php?ip=AS<?= irc_e($info['asn']) ?>"><?= irc_e($info['asn']) ?></a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="irc-left">
                                        <?php if (empty($info['asname'])) : ?>
                                            Localhost ?
                                        <?php elseif (empty($info['asn'])) : ?>
                                            <?= irc_e($info['asname']) ?>
                                        <?php else : ?>
                                            <a href="https://www.peeringdb.com/asn/<?= irc_e($info['asn']) ?>" target="_blank" rel="noopener" class="irc-link"><?= irc_e($info['asname']) ?></a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= irc_flag($info['country_code']) ?></td>
                                    <td>
                                        <?php if (empty($info['asn'])) : ?>
                                            <?= (int)$info['count'] ?>
                                        <?php else : ?>
                                            <button type="button" class="irc-count" onclick="showCustomModal(this)" data-asn="<?= irc_e($info['asn']) ?>"><?= (int)$info['count'] ?></button>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= empty($info['asn']) ? '-' : ipFromAsn(4, $info['asn']) ?></td>
                                    <td><?= empty($info['asn']) ? '-' : ipFromAsn(6, $info['asn']) ?></td>
                                    <?php if ($asnIsGood) : ?>
                                        <td><?= empty($info['asn']) ? '-' : (asnExists($info['asn'], $asnFileContent) ? '⚠️' : '✅') ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="irc-card-foot">
                    <p><strong><?= (int)$asnCount ?></strong> different ASNs found</p>
                    <?php if ($asnIsGood) : ?>
                        <p class="pt-3">The "Good ASN" column is experimental. By default, all ASNs are considered good, but special attention is given to those listed in the file <em>plugins/IPCloneDetectorAndASNAnalyzer/badasn/list.txt</em>.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- ===== Right column : chart, countries, statistics ===== -->
        <div class="irc-col">

            <section class="irc-card">
                <div class="irc-card-body">
                    <div class="irc-chart-wrap">
                        <canvas id="Chart" width="400" height="400"></canvas>
                    </div>
                </div>
            </section>

            <script>
                // PHP-generated countries array
                const countries = <?php echo json_encode($countries); ?>;
                const internetUsers = <?php echo json_encode($counts); ?>;

                // Predefined set of colors (RGB)
                const predefinedRgb = [
                    [255, 99, 132], // Red
                    [54, 162, 235], // Blue
                    [255, 206, 86], // Yellow
                    [75, 192, 192], // Green
                    [153, 102, 255], // Purple
                    [255, 159, 64], // Orange
                    [199, 199, 199], // Grey
                    [83, 102, 255], // Another Blue
                    [255, 99, 255], // Pink
                    [99, 255, 132], // Light Green
                    [255, 219, 88], // Light Yellow
                    [255, 99, 64], // Light Red
                    [75, 255, 192], // Mint Green
                    [192, 75, 255], // Light Purple
                    [159, 255, 64], // Light Orange
                    [132, 99, 255], // Periwinkle
                    [64, 255, 199], // Turquoise
                    [255, 64, 159], // Salmon
                    [255, 192, 75], // Gold
                    [102, 255, 83] // Lime Green
                ];

                // Generate colors for each country based on index
                const backgroundColors = countries.map((_, i) => `rgba(${predefinedRgb[i % predefinedRgb.length].join(',')}, 0.75)`);
                const borderColors = countries.map((_, i) => `rgba(${predefinedRgb[i % predefinedRgb.length].join(',')}, 1)`);

                // Configuration of the chart
                const data = {
                    labels: countries,
                    datasets: [{
                        label: 'Users',
                        data: internetUsers,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1,
                        hoverOffset: 8
                    }]
                };

                // Chart options
                const config = {
                    type: 'pie',
                    data: data,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true
                                }
                            },
                            title: {
                                display: true,
                                text: 'Number of Connected Users by Country',
                                font: {
                                    size: 15
                                }
                            },
                            datalabels: {
                                color: '#1f2937',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: (value, context) => {
                                    const total = context.chart.data.datasets[0].data.reduce((acc, val) => acc + val, 0);
                                    const percentage = (value / total) * 100;
                                    if (percentage < 5) {
                                        return null;
                                    }
                                    return `${context.chart.data.labels[context.dataIndex]}\n${percentage.toFixed(1)}%`;
                                },
                                textAlign: 'center'
                            }
                        }
                    },
                };

                // Register the plugin
                Chart.register(ChartDataLabels);

                // Render the chart
                const myChart = new Chart(
                    document.getElementById('Chart'),
                    config
                );
            </script>

            <section class="irc-card">
                <div class="irc-card-head">
                    <h4>Users by country</h4>
                </div>
                <div class="irc-scroll irc-scroll-short">
                    <table class="irc-table country">
                        <thead>
                            <tr>
                                <th class="irc-left">Country</th>
                                <th>Online number</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($countryCounts as $countryCode => $count) :
                                $percentage = $totalUsers > 0 ? ($count / $totalUsers) * 100 : 0;
                                $country = getCountryName($countryCode);
                            ?>
                                <tr>
                                    <td class="irc-left">
                                        <?php if (!empty($countryCode)) : ?>
                                            <img class="irc-flag" loading="lazy" src="https://flagcdn.com/48x36/<?= strtolower(irc_e($countryCode)) ?>.png" width="20" height="15" alt="<?= irc_e($countryCode) ?>">
                                        <?php endif; ?>
                                        <?= !empty($countryCode) ? irc_e($country) : 'localhost' ?>
                                    </td>
                                    <td><button type="button" class="irc-count" onclick="showListUsersModal(this)" data-country="<?= irc_e($country) ?>" data-country_code="<?= irc_e($countryCode) ?>"><?= (int)$count ?></button></td>
                                    <td><?= number_format($percentage, 2) ?>%<span class="irc-bar"><span style="width:<?= round($percentage, 2) ?>%"></span></span></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="irc-total">
                                <td class="irc-left">Total</td>
                                <td><?= count($users) ?></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="irc-card">
                <div class="irc-card-head">
                    <h4>Statistics Users</h4>
                </div>
                <table class="irc-table">
                    <thead>
                        <tr>
                            <th class="irc-left">Type</th>
                            <th>Online number</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="irc-left">Number of total users</td>
                            <td><?= (int)$usersCount ?></td>
                            <td>-</td>
                        </tr>
                        <tr class="irc-sep">
                            <td class="irc-left">Number of IPv4</td>
                            <td><?= (int)$ipv4Count ?></td>
                            <td><?= irc_e($ipv4CountPercentage) ?><span class="irc-bar"><span style="width:<?= $ipv4Pct ?>%"></span></span></td>
                        </tr>
                        <tr>
                            <td class="irc-left">Number of IPv6</td>
                            <td><?= (int)$ipv6Count ?></td>
                            <td><?= irc_e($ipv6CountPercentage) ?><span class="irc-bar"><span style="width:<?= $ipv6Pct ?>%"></span></span></td>
                        </tr>
                        <tr class="irc-sep">
                            <td class="irc-left">Number of account</td>
                            <td><?= (int)$accountCount ?></td>
                            <td><?= irc_e($accountCountPercentage) ?><span class="irc-bar"><span style="width:<?= $accountPct ?>%"></span></span></td>
                        </tr>
                        <tr>
                            <td class="irc-left">Number of no account</td>
                            <td><?= (int)$noAccountCount ?></td>
                            <td><?= irc_e($noAccountCountPercentage) ?><span class="irc-bar"><span style="width:<?= $noAccountPct ?>%"></span></span></td>
                        </tr>
                    </tbody>
                </table>
            </section>

        </div>
    </div>

    <!-- ===== Clones & bans ===== -->
    <div class="irc-grid-2">
        <section class="irc-card">
            <div class="irc-card-head">
                <h4>Show clones on IP</h4>
            </div>
            <?php irc_render_clones($cloneIpList); ?>
        </section>

        <section class="irc-card">
            <div class="irc-card-head">
                <h4>Show clones based on the first 4 segments of IPv6 addresses</h4>
            </div>
            <?php irc_render_clones($cloneIpv6List); ?>
        </section>

        <section class="irc-card">
            <div class="irc-card-head">
                <h4>Bans server corresponding to '~asn:'</h4>
            </div>
            <?php irc_render_bans($server_ban, true); ?>
        </section>

        <section class="irc-card">
            <div class="irc-card-head">
                <h4>Bans server corresponding to other type of '~'</h4>
            </div>
            <?php irc_render_bans($server_ban, false); ?>
        </section>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Search in Users</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="mt-4">Search for value in the list of users</h4>
                <form id="searchForm">

                    <div class="input-group mb-2">
                        <input type="text" id="searchValue" name="searchValue" class="form-control" placeholder="Enter value to search" required>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <input type="checkbox" id="modeStrict" name="modeStrict" value="1" checked>
                                <label class="d-block ml-1 mb-0" for="modeStrict">Strict search</label>
                            </span>

                        </div>
                    </div>

                    <div class="input-group mb-2">
                        <span class="input-group-text">#</span>
                        <input type="text" id="searchFromChannel" name="searchFromChannel" class="form-control" placeholder="(Optional) Search in a specific channel - Ex: quiz">
                    </div>
                    <button class="btn btn-primary" type="submit">Search</button>

                </form>
                <div id="results" class="results mt-4">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 720px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customModalLabel">Modal title</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="custom_results">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const IRC_BASE_URL = <?= json_encode(get_config("base_url")) ?>;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(c) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [c];
        });
    }

    function extractionRealname(realname) {
        const real_name = String(realname || '').split(/[/ ]/);
        return real_name;
    }

    function genderClass(realname) {
        const part = extractionRealname(realname);
        if (part && part[1] && /^(H|M|h|m)$/.test(part[1])) return 'boy';
        if (part && part[1] && /^(F|f)$/.test(part[1])) return 'girl';
        return 'regular';
    }

    function flagHtml(geoip) {
        const code = geoip && geoip.country_code;
        if (!code) return 'N/A';
        return escapeHtml(code) + ' <img class="irc-flag" src="https://flagcdn.com/48x36/' + escapeHtml(code.toLowerCase()) + '.png" width="20" height="15" alt="' + escapeHtml(code) + '">';
    }

    function userLink(item) {
        const name = item.name || '';
        const sex = genderClass(item.user && item.user.realname);
        return '<a href="' + IRC_BASE_URL + 'users/details.php?nick=' + encodeURIComponent(name) + '"><strong class="text_' + sex + '">' + escapeHtml(name || 'N/A') + '</strong></a>';
    }

    /* ---------- ASN table filter ---------- */
    document.getElementById('asnFilter').addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('#asnTable tbody tr').forEach(function(tr) {
            tr.style.display = (q === '' || tr.textContent.toLowerCase().indexOf(q) !== -1) ? '' : 'none';
        });
    });

    /* ---------- Search in users ---------- */
    document.getElementById('searchForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const searchValue = document.getElementById('searchValue').value;
        const searchFromChannel = document.getElementById('searchFromChannel').value;
        const modeStrict = document.getElementById('modeStrict').checked;
        const results = document.getElementById('results');
        results.textContent = 'Searching…';

        fetch(window.location.href + 'search.php?modeStrict=' + (modeStrict ? '1' : '0') + '&searchValue=' + encodeURIComponent(searchValue) + '&searchFromChannel=' + encodeURIComponent(searchFromChannel))
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById('results');
                resultsDiv.innerHTML = '<strong>' + data.length + ' results</strong>';

                if (data.length > 0) {
                    const ul = document.createElement('ul');

                    data.forEach(item => {
                        const li = document.createElement('li');
                        const sex = genderClass(item.user && item.user.realname);

                        li.innerHTML = `<a class="link-opacity-50-hover text_${sex}" href="${IRC_BASE_URL}users/details.php?nick=${encodeURIComponent(item.name)}" target="_blank">${escapeHtml(item.name)}</a>`;
                        ul.appendChild(li);
                    });

                    resultsDiv.appendChild(ul);
                } else {
                    resultsDiv.textContent = "The value was not found.";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('results').textContent = "An error occurred.";
            });
    });


    document.getElementById('SearchinUsers').addEventListener('click', function() {
        document.getElementById('searchForm').reset();
        document.getElementById('results').innerHTML = `
            <div class="irc-hint">
                <p>
                    Will search for all users matching the search criteria.<br>
                    Examples of search criteria :
                </p>
                <ul>
                    <li>known-users</li>
                    <li>3215</li>
                    <li>FR</li>
                    <li>123.123.123.123</li>
                    <li>...</li>
                </ul>
                <p>In the optional channel field, you can enter a channel to search for the value in that channel.</p>
            </div>
        `;
    });


    function showCustomModal(button) {
        var dataAsn = button.getAttribute('data-asn');
        var modalTitle = document.getElementById('customModalLabel');
        var resultsDiv = document.getElementById('custom_results');

        resultsDiv.textContent = 'Loading…';

        modalTitle.textContent = 'List of users with AS' + dataAsn;

        fetch(window.location.href + 'users_by_asn.php?asn=' + encodeURIComponent(dataAsn))
            .then(response => response.json())
            .then(data => {

                let html = `<table class='irc-table table table-bordered'>`;
                html += `<thead><tr><th>Nickname</th><th>Country</th><th>ASN</th><th>Asname</th><th>Type IP</th></tr></thead>`;
                html += `<tbody>`;

                for (let i = 0; i < data.length; i++) {
                    const item = data[i];
                    const geoip = item.geoip || {};
                    const type_ip = /\./.test(item.ip || '') ? 'IPv4' : 'IPv6';

                    html += `<tr>`;
                    html += `<td>${userLink(item)}</td>`;
                    html += `<td>${flagHtml(geoip)}</td>`;
                    html += `<td>${escapeHtml(geoip.asn || 'N/A')}</td>`;
                    html += `<td>${escapeHtml(geoip.asname || 'N/A')}</td>`;
                    html += `<td>${type_ip}</td>`;
                    html += `</tr>`;
                }

                if (Object.keys(data).length === 0) {
                    html += `<tr><td colspan="5">The value was not found.</td></tr>`;
                }

                html += `</tbody></table>`;

                resultsDiv.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                resultsDiv.textContent = "An error occurred.";
            });

        var myModal = new bootstrap.Modal(document.getElementById('customModal'));
        myModal.show();
    }

    function showListUsersModal(button) {
        var dataCountry_code = button.getAttribute('data-country_code');
        var dataCountry = button.getAttribute('data-country');
        var modalTitle = document.getElementById('customModalLabel');
        var resultsDiv = document.getElementById('custom_results');

        resultsDiv.textContent = 'Loading…';

        modalTitle.innerHTML = '<p class="mb-0">List of users of country ' + escapeHtml(dataCountry) + ' (' + escapeHtml(dataCountry_code) + ')</p>';

        fetch(window.location.href + 'users_by_country.php?country=' + encodeURIComponent(dataCountry_code))
            .then(response => response.json())
            .then(data => {

                let numberAccount = 0;
                let numberNoAccount = 0;
                let html = `<table class='irc-table table table-bordered'>`;
                html += `<thead><tr><th>Nickname</th><th>Country</th><th>Account</th></tr></thead>`;
                html += `<tbody>`;

                for (let i = 0; i < data.length; i++) {
                    const item = data[i];
                    const account = item.user && item.user.account;
                    if (account)
                        numberAccount++;
                    else
                        numberNoAccount++;

                    html += `<tr>`;
                    html += `<td>${userLink(item)}</td>`;
                    html += `<td>${flagHtml(item.geoip)}</td>`;
                    html += `<td>${account ? escapeHtml(account) : '<span class="text-danger">N/A</span>'}</td>`;
                    html += `</tr>`;
                }

                if (Object.keys(data).length === 0) {
                    html += `<tr><td colspan="3">The value was not found.</td></tr>`;
                }

                html += `</tbody></table>`;

                modalTitle.innerHTML += `<p class="mb-0" style="font-size:13px">Number of accounts: ${numberAccount} | Number of no-accounts: ${numberNoAccount}</p>`;

                resultsDiv.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                resultsDiv.textContent = "An error occurred.";
            });

        var myModal = new bootstrap.Modal(document.getElementById('customModal'));
        myModal.show();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
require_once "../../inc/footer.php";
?>
