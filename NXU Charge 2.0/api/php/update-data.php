<?php
require("total/secret.php");
$data = file_get_contents("php://input");
if (empty($data)) {
    return;
}
$data_json = json_decode($data, true);

$mongo_url = "mongodb+srv://" . $Secret["mongodb.username"] . ":" . $Secret["mongodb.password"] . "@" . $Secret["mongodb.server"] . "/?retryWrites=true&w=majority&appName=h";
$manager = new MongoDB\Driver\Manager($mongo_url);

foreach ($data_json["data"] as $pile => $value_pile) {
    $bulk = new MongoDB\Driver\BulkWrite;
    foreach ($value_pile as $id => $value_id) {
        foreach ($value_id as $port => $time) {
            $bulk->update(
                ['productid' => $id],
                ['$set' => [$port => $time]],
                ['upsert' => true]
            );
        }
    }
    $manager->executeBulkWrite('nxu_charge.' . $pile, $bulk);
}

$now_time = $data_json["update_time"];
$seepower_pid = $data_json["end_id"];
$token_usability = $data_json["token"];
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->update(
    ['id' => 1],
    ['$set' => ["timestamp" => $now_time, "num" => $seepower_pid, "token-usability" => $token_usability]],
    ['upsert' => true]
);
$manager->executeBulkWrite('nxu_charge.data', $bulk);
?>