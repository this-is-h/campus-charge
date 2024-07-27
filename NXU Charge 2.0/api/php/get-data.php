<?php
require("total/data.php");
require("total/secret.php");
require("data_get/get-data.php");
if (empty($_GET)) {
    return;
}
main($Secret, $Data, $_GET);
?>