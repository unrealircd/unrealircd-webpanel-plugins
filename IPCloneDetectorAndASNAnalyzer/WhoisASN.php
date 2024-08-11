<?php

require_once "../../inc/common.php";
require_once "../../inc/header.php";

$asn = htmlentities($_GET['asn']);
$rdapUrl = "https://stat.ripe.net/data/as-overview/data.json?resource=$asn";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $rdapUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if ($response === false) {
    die('Error occurred while fetching RDAP data: ' . curl_error($ch));
}

curl_close($ch);

?>
<!-- Highlight.js CSS via CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
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

    pre {
        padding: 10px;
        background-color: #f4f4f4;
        border: 1px solid #ddd;
        border-radius: 5px;
        white-space: pre-wrap;
        /* CSS3 */
        white-space: -moz-pre-wrap;
        /* Firefox */
        white-space: -pre-wrap;
        /* Opera <7 */
        white-space: -o-pre-wrap;
        /* Opera 7 */
        word-wrap: break-word;
        /* IE */
    }
</style>

<button class="back-button" onclick="goBack()">Previous page</button>
<pre><code id="json" class="json"></code></pre>

<!-- Highlight.js JS via CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
<script>
    function goBack() {
        window.history.back();
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        var json = <?php echo json_encode($response); ?>;
        var formattedJson = JSON.stringify(JSON.parse(json), null, 2);
        var codeElement = document.getElementById('json');
        codeElement.textContent = formattedJson;
        hljs.highlightElement(codeElement);
    });
</script>