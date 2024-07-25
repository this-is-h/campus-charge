<?php
require("secret.php");
require("data.php");
$mongo_url = "mongodb+srv://" . $Secret["mongodb.username"] . ":" . $Secret["mongodb.password"] . "@" . $Secret["mongodb.server"] . "/?retryWrites=true&w=majority&appName=h";
$manager = new MongoDB\Driver\Manager($mongo_url);

// 创建main
$main = '{"num":0,"timestamp":0,"writing":false,"token":"token","token-time":0}';
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert(json_decode($main));
$manager->executeBulkWrite('nxu_charge.data', $bulk);

// 创建各充电桩
foreach($DataMap as $key => $value) {
    $bulk = new MongoDB\Driver\BulkWrite;
    for ($i = 0; $i < count($value); $i++) {
        $data = array(
            "productid" => $value[$i],
        );
        for ($j = 0; $j < 10; $j++) {
            $data[$j+1] = 0;
        }
        $bulk->insert($data);
    }
    $manager->executeBulkWrite('nxu_charge.' . $key, $bulk);
}