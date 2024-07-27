<?php
// require("../total/secret_dev.php");
// require("../total/data.php");
function main($Secret, $Data, $Get) {
    date_default_timezone_set('Asia/Shanghai');
    $time = microtime(true);
    $result = array(
        "code" => 200,
        "succesful" => true,
    );

    if (empty($Get['pile'])) {
        $result["code"] = 300;
        $result["succesful"] = false;
        $result["error_msg"] = "pile参数为空";
        die(json_encode($result));
    }
    $pile = $Get['pile'];
    if (!array_key_exists($pile, $Data["DataMap"])) {
        $result["code"] = 300;
        $result["succesful"] = false;
        $result["error_msg"] = "pile参数有误";
        die(json_encode($result));
    }

    $mongo_url = "mongodb+srv://" . $Secret["mongodb.username"] . ":" . $Secret["mongodb.password"] . "@" . $Secret["mongodb.server"] . "/?retryWrites=true&w=majority&appName=h";
    $manager = new MongoDB\Driver\Manager($mongo_url);

    $filter = ["id" => 1];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options);
    $documents = $manager->executeQuery('nxu_charge.data', $query);
    foreach($documents as $document){
        $document = json_decode(json_encode($document),true);
        $result["time"] = $document['timestamp'];
        $result["token"] = $document['token-usability'];
    }

    $result_data = array();
    foreach ($Data["DataMap"][$Get['pile']] as $productid) {
        $filter = ["productid" => $productid];
        $options = [];
        $query = new MongoDB\Driver\Query($filter, $options);
        $documents = $manager->executeQuery('nxu_charge.' . $Get['pile'], $query);
        foreach($documents as $document){
            $document = json_decode(json_encode($document),true);
            for ($i = 1; $i <= 10; $i++) {
                $result_data[$productid][] = $document[$i];
            }
        }
    }
    $result["data"] = $result_data;

    echo json_encode($result);
}
?>