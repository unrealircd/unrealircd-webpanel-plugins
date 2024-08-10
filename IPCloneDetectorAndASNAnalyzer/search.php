<?php
require_once "../../inc/common.php";
require_once(UPATH . "/inc/connection.php");
global $rpc;
$users = $rpc->user()->getAll();
?>
<?php
header('Content-Type: application/json');

$searchValue = isset($_GET['searchValue']) ? $_GET['searchValue'] : '';
$foundObjects = [];
$modeStrict = isset($_GET['modeStrict']) && $_GET['modeStrict'] == '1' ? true : false;

function searchInArray($array, $searchValue, &$foundObjects, $currentObject) {
    global $modeStrict;
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            searchInArray($value, $searchValue, $foundObjects, $currentObject);
        } elseif (is_object($value)) {
            searchInArray((array) $value, $searchValue, $foundObjects, $currentObject);
        } elseif ($value == $searchValue || !$modeStrict && is_string($value) && stripos($value, $searchValue) !== false) {
            $foundObjects[] = $currentObject;
            return;
        }
    }
}

foreach ($users as $obj) {
    searchInArray((array) $obj, $searchValue, $foundObjects, $obj);
}

echo json_encode($foundObjects);

