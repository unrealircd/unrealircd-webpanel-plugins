<?php
require_once "../../inc/common.php";
require_once(UPATH . "/inc/connection.php");
global $rpc;
$users = $rpc->user()->getAll();

header('Content-Type: application/json');

usort($users, function ($a, $b) {
    return strcmp($a->name, $b->name);
});

$asnFilter = htmlentities($_GET['asn']);

// Filtrer les utilisateurs en fonction du numéro ASN
$filteredUsers = array_filter($users, function ($entry) use ($asnFilter) {
    return isset($entry->geoip->asn) && $entry->geoip->asn == $asnFilter;
});

$foundObjects = [];

if (!empty($filteredUsers)) {
    foreach ($filteredUsers as $entry) {
        $asn = htmlspecialchars($entry->geoip->asn);
        $asname = htmlspecialchars($entry->geoip->asname);
        $country_code = htmlspecialchars($entry->geoip->country_code);
        $nickname = htmlspecialchars($entry->name);

        $foundObjects[] = $entry;
    }
}
echo json_encode($foundObjects);
