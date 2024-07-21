<?php
require_once "../../inc/common.php";
require_once "../../inc/header.php";

require_once(UPATH . "/inc/connection.php");
global $rpc;
$users = $rpc->user()->getAll();

function readFileContent($filePath) {
    $content = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($content === false) {
        echo "Impossible d'ouvrir le fichier.";
        return [];
    }
    return $content;
}

$asnFileContent = readFileContent('badasn/list.txt');

function asnExists($asn, $fileContent) {
    foreach ($fileContent as $line) {
        if (trim($line) == $asn) {
            return true;
        }
    }
    return false;
}

//print_r($users);

?>
<style>
table td, table th {
  padding: 2px;
  text-align: center
}
</style>
<div class="container d-flex justify-content-center align-items-center container-center">
    <div class="row">
        <div class="col-md-6 div-item">
            <h4>Number of ASN duplicates sorted from largest to smallest</h4>
            <?php
            $asnCounts = [];
            foreach ($users as $entry) {
                $asn = $entry->geoip->asn;
                $asname = $entry->geoip->asname;
                $country_code = $entry->geoip->country_code;

                if (!isset($asnCounts[$asn])) {
                    $asnCounts[$asn] = [
                        'asn' => $asn,
                        'asname' => $asname,
                        'country_code' => $country_code,
                        'count' => 0
                    ];
                }
                $asnCounts[$asn]['count']++;
            }

            // Convertir en tableau indexé
            $asnCounts = array_values($asnCounts);

            // Trier par nombre d'occurrences en ordre décroissant
            usort($asnCounts, function ($a, $b) {
                return $b['count'] - $a['count'];
            });


            echo "<table border='1'>";
            echo "<tr><th>ASN</th><th>ASName</th><th>Country Code</th><th>Count</th><th>Good ASN</th></tr>";

            foreach ($asnCounts as $info) {
                echo "<tr>";
                echo "<td>" . (empty($info['asn']) ? '-' : '<a href="WhoisASN.php?asn='.$info['asn'].'">'.$info['asn'].'</a>') . "</td>";
                echo "<td>" . (empty($info['asname']) ? 'Localhost ?' : $info['asname']) . "</td>";
                echo "<td>" . (empty($info['country_code']) ? '-' : "{$info['country_code']} <img src=\"https://flagcdn.com/48x36/" . strtolower($info['country_code']) . ".png\" width=\"20\" height=\"15\">") . "</td>";
                echo "<td>{$info['count']}</td>";
                echo "<td>" . (empty($info['asn']) ? '-' : (asnExists($info['asn'], $asnFileContent) ? '⚠️' : '✅')) . "</td>";
                echo "</tr>";
            }

            echo "</table>";
            ?>
        </div>
        <div class="col-md-6 div-item">
            <h4>Show clones on IP</h4>
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

            echo "<table border='1'>
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

            echo "<table border='1'>
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
        </div>
    </div>
</div>

<div class="container d-flex justify-content-center align-items-center container-center">
    <div class="row">


    </div>
</div>
<?php

require_once "../../inc/footer.php";



?>