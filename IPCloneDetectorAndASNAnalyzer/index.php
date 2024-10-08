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

function ipFromAsn($v, $asn)
{
    global $users;
    $count = 0;
    $ipv4Count = 0;
    $ipv6Count = 0;
    $totalIpCount = 0;

    foreach ($users as $obj) {
        if (isset($obj->geoip->asn) && $obj->geoip->asn == $asn && isset($obj->ip)) {
            $totalIpCount++;
            if (filter_var($obj->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ipv4Count++;
            } elseif (filter_var($obj->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $ipv6Count++;
            }
        }
    }

    $ipv4Percentage = ($totalIpCount > 0) ? ($ipv4Count / $totalIpCount) * 100 : 0;
    $ipv6Percentage = ($totalIpCount > 0) ? ($ipv6Count / $totalIpCount) * 100 : 0;

    $css = '';
    if ($ipv4Percentage > $ipv6Percentage) {
        $css = ($v == 4) ? ' bg-success' : ' bg-danger';
    } elseif ($ipv6Percentage > $ipv4Percentage) {
        $css = ($v == 6) ? ' bg-success' : ' bg-danger';
    }

    if ($v == 4) {
        $count = $ipv4Count;
        $percentage = $ipv4Percentage;
    } else if ($v == 6) {
        $count = $ipv6Count;
        $percentage = $ipv6Percentage;
    }

    return "$count<span class=\"d-block badge badge-secondary$css\">" . round($percentage, 2) . "%</span>";
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

?>
<style>
    table td,
    table th {
        padding: 2px;
        text-align: center
    }

    table a.btn {
        user-select: unset;
    }

    table.country td {
        padding: 5px;
    }

    @media screen and (max-width: 1100px) {
        #main_contain .col-md-6 {
            max-width: inherit;
            margin: 0 auto;
        }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<div class="bd-example m-0 border-0">
    <button id="SearchinUsers" type="button" class="btn btn-primary" data-toggle="modal" data-target="#searchModal">Search in Users</button>
</div>

<div class="container d-flex justify-content-center align-items-center container-center">
    <div class="row">
        <div class="col-md-6 div-item">
            <h4>Number of ASN duplicates sorted from largest to smallest</h4>
            <?php
            $asnCounts = [];
            $asnCount = 0;
            foreach ($users as $entry) {
                $geoip = $entry->geoip;
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

            // Convertir en tableau indexé
            $asnCounts = array_values($asnCounts);

            // Trier par nombre d'occurrences en ordre décroissant
            usort($asnCounts, function ($a, $b) {
                return $b['count'] - $a['count'];
            });


            echo "<table class='table-striped' border='1'>";
            echo "<tr><th>ASN</th><th>ASName</th><th>Country Code</th><th>Count</th><th>IPv4</th><th>IPv6</th>" . ($asnIsGood ? "<th>Good ASN</th>" : "") . "</tr>";

            foreach ($asnCounts as $info) {
                echo "<tr>";
                echo "<td>" . (empty($info['asn']) ? '-' : '<a class="btn btn-outline-primary" href="' . get_config("base_url") . 'tools/ip-whois.php?ip=AS' . $info['asn'] . '">' . $info['asn'] . '</a>') . "</td>";
                echo "<td>" . (empty($info['asname']) ? 'Localhost ?' : '<a href="https://www.peeringdb.com/asn/' . $info['asn'] . '" target="_blank">' . $info['asname']) . '</a>' . "</td>";
                echo "<td>" . (empty($info['country_code']) ? '-' : "{$info['country_code']} <img src=\"https://flagcdn.com/48x36/" . strtolower($info['country_code']) . ".png\" width=\"20\" height=\"15\">") . "</td>";
                echo "<td>" . (empty($info['asn']) ? $info['count'] : '<button type="button" class="btn btn-outline-primary" onclick="showCustomModal(this)" data-asn="' . $info['asn'] . '">' . $info['count'] . '</button>') . "</td>";
                echo "<td>" . (empty($info['asn']) ? '-' : ipFromAsn(4, $info['asn'])) . "</td>";
                echo "<td>" . (empty($info['asn']) ? '-' : ipFromAsn(6, $info['asn'])) . "</td>";
                if ($asnIsGood)
                    echo "<td>" . (empty($info['asn']) ? '-' : (asnExists($info['asn'], $asnFileContent) ? '⚠️' : '✅')) . "</td>";
                echo "</tr>";
            }

            echo "</table>";
            echo "$asnCount different ASNs found";

            if ($asnIsGood)
                echo "<p class=\"pt-4\">The \"Good ASN\" column is experimental. By default, all ASNs are considered good, but special attention is given to those listed in the file <em>plugins/IPCloneDetectorAndASNAnalyzer/badasn/list.txt</em>.</p>";
            ?>


        </div>
        <div class="col-md-6 div-item">
            <canvas id="Chart" width="400" height="400" class="mb-5"></canvas>

            <script>
                <?php

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

                // Sort the country counts in descending order
                arsort($countryCounts);

                $countries = [];
                $counts = [];
                //echo "Country Codes and their Counts:\n";
                foreach ($countryCounts as $countryCode => $count) {
                    //echo "Country Code: $countryCode - Count: $count\n";
                    $countries[] = (!empty($countryCode) ? $countryCode : 'localhost');
                    $counts[] = $count;
                }

                ?>

                // PHP-generated countries array
                const countries = <?php echo json_encode($countries); ?>;
                const internetUsers = <?php echo json_encode($counts); ?>;

                // Predefined set of colors
                const predefinedColors = [
                    'rgba(255, 99, 132, 0.2)', // Red
                    'rgba(54, 162, 235, 0.2)', // Blue
                    'rgba(255, 206, 86, 0.2)', // Yellow
                    'rgba(75, 192, 192, 0.2)', // Green
                    'rgba(153, 102, 255, 0.2)', // Purple
                    'rgba(255, 159, 64, 0.2)', // Orange
                    'rgba(199, 199, 199, 0.2)', // Grey
                    'rgba(83, 102, 255, 0.2)', // Another Blue
                    'rgba(255, 99, 255, 0.2)', // Pink
                    'rgba(99, 255, 132, 0.2)', // Light Green
                    'rgba(255, 219, 88, 0.2)', // Light Yellow
                    'rgba(255, 99, 64, 0.2)', // Light Red
                    'rgba(75, 255, 192, 0.2)', // Mint Green
                    'rgba(192, 75, 255, 0.2)', // Light Purple
                    'rgba(159, 255, 64, 0.2)', // Light Orange
                    'rgba(132, 99, 255, 0.2)', // Periwinkle
                    'rgba(64, 255, 199, 0.2)', // Turquoise
                    'rgba(255, 64, 159, 0.2)', // Salmon
                    'rgba(255, 192, 75, 0.2)', // Gold
                    'rgba(102, 255, 83, 0.2)' // Lime Green
                ];

                const predefinedBorderColors = [
                    'rgba(255, 99, 132, 1)', // Red
                    'rgba(54, 162, 235, 1)', // Blue
                    'rgba(255, 206, 86, 1)', // Yellow
                    'rgba(75, 192, 192, 1)', // Green
                    'rgba(153, 102, 255, 1)', // Purple
                    'rgba(255, 159, 64, 1)', // Orange
                    'rgba(199, 199, 199, 1)', // Grey
                    'rgba(83, 102, 255, 1)', // Another Blue
                    'rgba(255, 99, 255, 1)', // Pink
                    'rgba(99, 255, 132, 1)', // Light Green
                    'rgba(255, 219, 88, 1)', // Light Yellow
                    'rgba(255, 99, 64, 1)', // Light Red
                    'rgba(75, 255, 192, 1)', // Mint Green
                    'rgba(192, 75, 255, 1)', // Light Purple
                    'rgba(159, 255, 64, 1)', // Light Orange
                    'rgba(132, 99, 255, 1)', // Periwinkle
                    'rgba(64, 255, 199, 1)', // Turquoise
                    'rgba(255, 64, 159, 1)', // Salmon
                    'rgba(255, 192, 75, 1)', // Gold
                    'rgba(102, 255, 83, 1)' // Lime Green
                ];

                // Generate colors for each country based on index
                const backgroundColors = countries.map((_, index) => predefinedColors[index % predefinedColors.length]);
                const borderColors = countries.map((_, index) => predefinedBorderColors[index % predefinedBorderColors.length]);

                // Configuration of the chart
                const data = {
                    labels: countries,
                    datasets: [{
                        label: 'Users',
                        data: internetUsers,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
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
                            },
                            title: {
                                display: true,
                                text: 'Number of Connected Users by Country'
                            },
                            datalabels: {
                                color: '#343a40',
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
            <?php
            $totalUsers = array_sum($countryCounts);
            echo "<table class='country table-striped m-auto mb-5' border='1'>
            <tr>
                <th>Country</th>
                <th>Online number</th>
                <th>Percentage</th>
            </tr>";

            foreach ($countryCounts as $countryCode => $count) {
                $percentage = $totalUsers > 0 ? ($count / $totalUsers) * 100 : 0;
                //echo "Country Code: $countryCode - Count: $count\n";
                $countries[] = $countryCode;
                $counts[] = $count;
                $country = getCountryName($countryCode);
                echo "<tr>
                <td>" . (empty($countryCode) ? '' : "<img src=\"https://flagcdn.com/48x36/" . strtolower($countryCode) . ".png\" width=\"20\" height=\"15\">") . " " . (!empty($countryCode) ? getCountryName($countryCode) : 'localhost') . "</td>
                <td><button type=\"button\" class=\"btn btn-outline-primary\" onclick=\"showListUsersModal(this)\" data-country=\"{$country}\" data-country_code=\"{$countryCode}\">" . $count . "</button></td>
                <td>" . number_format($percentage, 2) . "%</td>
            </tr>";
            }

            echo "<tr class='font-weight-bold'>
            <td>Total</td>
            <td>" . count($users) . "</td>
            <td></td>
        </tr>";

            echo "</table>";

            ?>


            <h4 class="mt-5">Show clones on IP</h4>
            <?php
            $ipList = [];
            foreach ($users as $key => $obj) {
                if (isset($obj->ip)) {
                    $ip = $obj->ip;
                    if (array_key_exists($ip, $ipList)) {
                        $ipList[$ip]['count']++;
                        $ipList[$ip]['names'][] = $obj->name;
                    } else {
                        $ipList[$ip] = [
                            'count' => 1,
                            'names' => [$obj->name]
                        ];
                    }
                }
            }

            $duplicateList = [];
            foreach ($ipList as $ip => $info) {
                if ($info['count'] > 1) {
                    $duplicateList[] = [
                        'ip' => $ip,
                        'count' => $info['count'],
                        'names' => implode(', ', $info['names'])
                    ];
                }
            }

            echo "<table class='table-striped' border='1'>
                <tr>
                    <th>IP</th>
                    <th>Number<br>of<br>duplicates</th>
                    <th>List of names</th>
                </tr>";

            foreach ($duplicateList as $entry) {
                echo "<tr>
                    <td>{$entry['ip']}</td>
                    <td>{$entry['count']}</td>
                    <td>{$entry['names']}</td>
                </tr>";
            }

            echo "</table>";
            ?>
            <hr>
            <h4>Show clones based on the first 4 segments of IPv6 addresses</h4>
            <?php
            $ipList = [];
            foreach ($users as $key => $obj) {
                if (isset($obj->ip) && strpos($obj->ip, ':') !== false) {
                    // Extraire les quatre premiers segments de l'IPv6
                    $ipSegments = explode(':', $obj->ip);
                    $shortIp = implode(':', array_slice($ipSegments, 0, 4));

                    if (array_key_exists($shortIp, $ipList)) {
                        $ipList[$shortIp]['count']++;
                        $ipList[$shortIp]['names'][] = $obj->name;
                    } else {
                        $ipList[$shortIp] = [
                            'count' => 1,
                            'names' => [$obj->name]
                        ];
                    }
                }
            }

            $duplicateList = [];
            foreach ($ipList as $ip => $info) {
                if ($info['count'] > 1) {
                    $duplicateList[] = [
                        'ip' => $ip,
                        'count' => $info['count'],
                        'names' => implode(', ', $info['names'])
                    ];
                }
            }

            echo "<table class='table-striped' border='1'>
                <tr>
                    <th>IP</th>
                    <th>Number<br>of<br>duplicates</th>
                    <th>List of names</th>
                </tr>";

            foreach ($duplicateList as $entry) {
                echo "<tr>
                    <td>{$entry['ip']}</td>
                    <td>{$entry['count']}</td>
                    <td>{$entry['names']}</td>
                </tr>";
            }

            echo "</table>";
            ?>

            <hr>
            <h4>Statistics Users</h4>
            <?php
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

            echo "<table class='table-striped' border='1'>
                        <tr>
                            <th>Type</th>
                            <th>Online number</th>
                        </tr>
                        <tr>
                            <td>Number of total users</td>
                            <td>{$usersCount}</td>
                        </tr>
                        <tr>
                            <td>Number of IPv4</td>
                            <td>{$ipv4Count}</td>
                        </tr>
                        <tr>
                            <td>Number of IPv6</td>
                            <td>{$ipv6Count}</td>
                        </tr>
                        <tr>
                            <td>Number of account</td>
                            <td>{$accountCount}</td>
                        </tr>
                        <tr>
                            <td>Number of no account</td>
                            <td>{$noAccountCount}</td>
                        </tr>
                    </table>";
            ?>


            <h4 class="mt-4">Bans server corresponding to '~asn:'</h4>
            <?php
            echo "<table class='table-striped' border='1'>
                    <tr>
                        <th>Type ban</th>
                        <th>Found</th>
                        <th>Affected users</th>
                        <th>duration</th>
                    </tr>
                ";
            foreach ($server_ban as $obj) {
                $usersCount++;
                if (isset($obj->name)) {
                    if (strpos($obj->name, "~asn:") !== false && preg_match('/^\%/', $obj->name, $matches) === 1) {
                        echo "<tr>
                            <td>{$obj->type}</td>
                            <td>{$obj->name}</td>
                            <td><a href=\"https://www.unrealircd.org/docs/Soft_ban\" target=\"_blank\">unregistered</a></td>
                            <td>{$obj->duration_string}</td>
                        </tr>";
                    } else if (strpos($obj->name, "~asn:") !== false) {
                        echo "<tr>
                            <td>{$obj->type}</td>
                            <td>{$obj->name}</td>
                            <td>all</td>
                            <td>{$obj->duration_string}</td>
                        </tr>";
                    }
                }
            }
            echo "</table>";
            ?>


            <h4 class="mt-4">Bans server corresponding to other type of '~'</h4>
            <?php

            echo "<table class='table-striped' border='1'>
                    <tr>
                        <th>Type ban</th>
                        <th>Found</th>
                        <th>Affected users</th>
                        <th>duration</th>
                    </tr>
                ";
            foreach ($server_ban as $obj) {
                $usersCount++;
                if (isset($obj->name)) {
                    if (strpos($obj->name, "~asn:") === false && strpos($obj->name, "~") !== false && preg_match('/^\%/', $obj->name, $matches) === 1) {
                        echo "<tr>
                            <td>{$obj->type}</td>
                            <td>{$obj->name}</td>
                             <td><a href=\"https://www.unrealircd.org/docs/Soft_ban\" target=\"_blank\">unregistered</a></td>
                            <td>{$obj->duration_string}</td>
                        </tr>";
                    } else if (strpos($obj->name, "~asn:") === false && strpos($obj->name, "~") !== false) {
                        echo "<tr>
                            <td>{$obj->type}</td>
                            <td>{$obj->name}</td>
                            <td>all</td>
                            <td>{$obj->duration_string}</td>
                        </tr>";
                    }
                }
            }
            echo "</table>";
            ?>
        </div>
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
    <div class="modal-dialog" style="max-width: 600px;">
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
    document.getElementById('searchForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const searchValue = document.getElementById('searchValue').value;
        const searchFromChannel = document.getElementById('searchFromChannel').value;
        const modeStrict = document.getElementById('modeStrict').checked;
        const results = document.getElementById('results');
        results.innerHTML = '';

        fetch(window.location.href + 'search.php?modeStrict=' + (modeStrict ? '1' : '0') + '&searchValue=' + encodeURIComponent(searchValue) + '&searchFromChannel=' + encodeURIComponent(searchFromChannel))
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById('results');
                resultsDiv.innerHTML = data.length + ' results';

                if (data.length > 0) {
                    const ul = document.createElement('ul');

                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.innerHTML = '<a class="link-opacity-50-hover" href="<?php echo get_config("base_url"); ?>users/details.php?nick=' + item.name + '" target="_blank">' + item.name + '</a>';
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
        `;
    });


    function showCustomModal(button) {
        var dataAsn = button.getAttribute('data-asn');
        var modalTitle = document.getElementById('customModalLabel');
        var modalBody = document.querySelector('#customModal .modal-body');
        var resultsDiv = document.getElementById('custom_results');

        resultsDiv.innerHTML = '';

        modalTitle.textContent = 'List of users with AS' + dataAsn;

        fetch(window.location.href + 'users_by_asn.php?asn=' + dataAsn)
            .then(response => response.json())
            .then(data => {

                let html = `<table class='table table-bordered'>`;
                html += `<thead><tr><th>Nickname</th><th>Country</th><th>ASN</th><th>Asname</th></tr></thead>`;
                html += `<tbody>`;

                for (let i = 0; i < data.length; i++) {
                    const item = data[i];
                    html += `<tr>`;
                    html += `<td><a href="<?= get_config("base_url") ?>users/details.php?nick=${item.name || ''}"><strong>${item.name || 'N/A'}</strong></a></td>`;
                    html += `<td>${item.geoip.country_code + ' <img src="https://flagcdn.com/48x36/' + item.geoip.country_code.toLowerCase() + '.png" width="20" height="15">' || 'N/A'}</td>`;
                    html += `<td>${item.geoip.asn || 'N/A'}</td>`;
                    html += `<td>${item.geoip.asname || 'N/A'}</td>`;
                }

                if (Object.keys(data).length === 0) {
                    html += `<tr><td colspan="6">The value was not found.</td></tr>`;
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
        var modalBody = document.querySelector('#customModal .modal-body');
        var resultsDiv = document.getElementById('custom_results');

        resultsDiv.innerHTML = '';

        modalTitle.innerHTML = '<p>List of users of country ' + dataCountry + ' (' + dataCountry_code + ')</p>';

        fetch(window.location.href + 'users_by_country.php?country=' + dataCountry_code)
            .then(response => response.json())
            .then(data => {

                let numberAccount = 0;
                let numberNoAccount = 0;
                let html = `<table class='table table-bordered'>`;
                html += `<thead><tr><th>Nickname</th><th>Country</th><th>Account</th></tr></thead>`;
                html += `<tbody>`;

                for (let i = 0; i < data.length; i++) {
                    const item = data[i];
                    if (item.user.account)
                        numberAccount++;
                    else
                        numberNoAccount++;
                    html += `<tr>`;
                    html += `<td><a href="<?= get_config("base_url") ?>users/details.php?nick=${item.name || ''}"><strong>${item.name || 'N/A'}</a></strong></td>`;
                    html += `<td>${item.geoip.country_code + ' <img src="https://flagcdn.com/48x36/' + item.geoip.country_code.toLowerCase() + '.png" width="20" height="15">' || 'N/A'}</td>`;
                    html += `<td>${item.user.account || '<span class="text-danger">N/A</span>'}</td>`;
                }

                if (Object.keys(data).length === 0) {
                    html += `<tr><td colspan="6">The value was not found.</td></tr>`;
                }

                html += `</tbody></table>`;

                modalTitle.innerHTML += `<p style="font-size:13px">Number of accounts: ${numberAccount} | Number of no-accounts: ${numberNoAccount}</p>`;

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
<!--
<div class="container d-flex justify-content-center align-items-center container-center">
    <div class="row">
    </div>
</div>
-->
<?php
require_once "../../inc/footer.php";
?>