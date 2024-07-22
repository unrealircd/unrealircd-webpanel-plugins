<?php

require_once "../../inc/common.php";
require_once "../../inc/header.php";

$asn = 'AS' . htmlentities($_GET['asn']);
$whoisServer = 'whois.arin.net';

$command = "whois -h $whoisServer $asn";
$result = shell_exec($command);

?>
<style>
    .back-button {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        color: white;
        background-color: #007BFF;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .back-button:hover {
        background-color: #0056b3;
    }
</style>
<button class="back-button" onclick="goBack()">Previous page</button>

<script>
    function goBack() {
        window.history.back();
    }
</script>
<?php
echo "<pre>$result</pre>";

require_once "../../inc/footer.php";

?>