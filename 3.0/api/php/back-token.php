<?php
require("total/data.php");
require("total/secret.php");
require("token/back-token_mongo.php");
main($Secret, $_GET);
?>