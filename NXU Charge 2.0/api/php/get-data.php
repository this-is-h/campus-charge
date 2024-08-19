<?php
require("total/data.php");
require("total/secret.php");
require("data_get/get-data_from-url.php");
if (empty($_GET)) {
    return;
}
main($Secret, $Data, $_GET);
?>