<?php
require_once "../../inc/common.php";
require_once "../../inc/header.php";

require_once(UPATH . "/inc/connection.php");
global $rpc;
$users = $rpc->user()->getAll();

usort($users, function($a, $b) {
    return strcmp($a->name, $b->name);
});

$asnFilter = htmlentities($_GET['asn']);

// Filtrer les utilisateurs en fonction du numéro ASN
$filteredUsers = array_filter($users, function ($entry) use ($asnFilter) {
    return isset($entry->geoip->asn) && $entry->geoip->asn == $asnFilter;
});

?>
<style>
    table td,
    table th {
        padding: 5px;
        text-align: center;
    }
</style>
<div class="container d-flex justify-content-center align-items-center container-center">
    <div class="row">
        <div class="div-item">
            <h4>List of users with AS<?= htmlspecialchars($asnFilter) ?></h4>
            <?php
            if (empty($filteredUsers)) {
                echo "<p>No users found with ASN " . htmlspecialchars($asnFilter) . ".</p>";
            } else {
                echo "<table border='1'>";
                echo "<tr><th>Country</th><th>ASN</th><th>Asname</th><th>Nickname</th><th>Whois</th></tr>";

                foreach ($filteredUsers as $entry) {
                    $asn = htmlspecialchars($entry->geoip->asn);
                    $asname = htmlspecialchars($entry->geoip->asname);
                    $country_code = htmlspecialchars($entry->geoip->country_code);
                    $nickname = htmlspecialchars($entry->name);

                    echo "<tr>";
                    echo "<td>" . (empty($country_code) ? '-' : "{$country_code} <img src=\"https://flagcdn.com/48x36/" . strtolower($country_code) . ".png\" width=\"20\" height=\"15\">") . "</td>";
                    echo "<td>" . (empty($asn) ? '-' : '<a href="WhoisASN.php?asn=' . $asn . '">' . $asn . '</a>') . "</td>";
                    echo "<td>" . (empty($asname) ? 'Localhost ?' : $asname) . "</td>";
                    echo "<td>" . $nickname . "</td>";
                    echo "<td><a class=\"btn btn-primary\" href=\"".get_config("base_url")."users/details.php?nick=" . $nickname . "\">WHOIS</a></td>";
                    echo "</tr>";
                }

                echo "</table>";
            }
            ?>
        </div>
    </div>
</div>

<?php
require_once "../../inc/footer.php";
?>
