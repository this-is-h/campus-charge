<?php
require("total/secret.php");
require("data_update/vercel_update-data.php");
$data = file_get_contents("php://input");
if (empty($data)) {
    return;
}
main($Secret, $data);
?>