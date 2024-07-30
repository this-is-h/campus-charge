<?php
function outputLog($manager, $log_type, $log_msg, $log_data) {
    $log_time = round(microtime(true) * 1000);
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert(["time" => $log_time, "type" => $log_type, "msg" => $log_msg, "data" => $log_data]);
    $manager->executeBulkWrite('nxu_charge.log', $bulk);
}

function main($Secret, $data) {
    date_default_timezone_set('Asia/Shanghai');
    $data_json = json_decode($data, true);
    
    $mongo_url = "mongodb+srv://" . $Secret["mongodb.username"] . ":" . $Secret["mongodb.password"] . "@" . $Secret["mongodb.server"] . "/?retryWrites=true&w=majority&appName=h";
    $manager = new MongoDB\Driver\Manager($mongo_url);

    $write_time = microtime(true);
    $writing = true;
    $filter = ["id" => 1];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options);
    $documents = $manager->executeQuery('nxu_charge.data', $query);
    foreach($documents as $document){
        $document = json_decode(json_encode($document),true);
        $write_time = $document["write-time"];
        $writing = $document["writing"];
        $update_time = $document["timestamp"];
        $seepower_pid = $document['num'];
    }
    if ($writing && round(microtime(true) * 1000) - $write_time < 10*60*1000) {
        outputLog($manager, "error", "数据库处于写入状态，终止程序", $data_json);
        return;
    }
    if (!is_numeric($data_json["end_id"])) {
        outputLog($manager, "error", "传入 end_id 有误，终止程序", $data_json);
        return;
    }
    if ($data_json["update_time"] < $update_time || $data_json["end_id"] < $seepower_pid) {
        return;
    }

    $time = round(microtime(true) * 1000);
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['id' => 1],
        ['$set' => ["write-time" => $time, "writing" => true]],
        ['upsert' => true]
    );
    $manager->executeBulkWrite('nxu_charge.data', $bulk);
    
    foreach ($data_json["data"] as $pile => $value_pile) {
        $bulk = new MongoDB\Driver\BulkWrite;
        foreach ($value_pile as $id => $value_id) {
            foreach ($value_id as $port => $time) {
                if (empty($time["time"])) {
                    $bulk->update(
                        ['productid' => $id],
                        ['$set' => [$port . ".enable" => $time["enable"]]],
                        ['upsert' => true]
                    );
                } else {
                    $bulk->update(
                        ['productid' => $id],
                        ['$set' => [$port => $time]],
                        ['upsert' => true]
                    );
                }
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
        ['$set' => ["timestamp" => $now_time, "num" => $seepower_pid, "token-usability" => $token_usability, "writing" => false]],
        ['upsert' => true]
    );
    $manager->executeBulkWrite('nxu_charge.data', $bulk);
}
?>