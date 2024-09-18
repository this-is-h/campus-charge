<?php
require("total/data.php");
require("total/secret.php");
require("data_get/get-data_from-url_multi.php");
if (empty($_GET)) {
    return;
}
main($Secret, $Data, $_GET);
?>