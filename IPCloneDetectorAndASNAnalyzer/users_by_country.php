<?php
require_once "../../inc/common.php";
require_once(UPATH . "/inc/connection.php");
global $rpc;
$users = $rpc->user()->getAll();

header('Content-Type: application/json');

usort($users, function ($a, $b) {
    return strcasecmp($a->name, $b->name);
});

$countryFilter = htmlentities($_GET['country']);

// Filtrer les utilisateurs en fonction du pays
$filteredUsers = array_filter($users, function ($entry) use ($countryFilter) {
    return isset($entry->geoip->country_code) && $entry->geoip->country_code == $countryFilter;
});

$foundObjects = [];

if (!empty($filteredUsers)) {
    foreach ($filteredUsers as $entry) {
        $account = ($entry->user->account ? htmlspecialchars($entry->user->account) : null);
        $country_code = htmlspecialchars($entry->geoip->country_code);
        $nickname = htmlspecialchars($entry->name);
        $foundObjects[] = $entry;
    }
}
echo json_encode($foundObjects);
