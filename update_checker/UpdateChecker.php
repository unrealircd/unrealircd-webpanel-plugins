<?php
require_once "../../inc/common.php";
require_once "../../inc/header.php";
echo "<h4>Update Checker for UnrealIRCd Admin Panel</h4>";
echo "<button id=\"Search_update\" type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\">Search and update</button>";
echo "<div class=\"response\"></div>";
?>

<script>
    document.getElementById("Search_update").addEventListener("click", function() {
        fetch('update.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                document.querySelector('.response').innerHTML = data;
            })
            .catch(error => {
                console.error('There was a problem with your fetch operation:', error);
            });
    });
</script>


<?php

require_once "../../inc/footer.php";
?>