<?php
// vercel部署时会自动运行一次该程序

require("secret.php");
require("data.php");

$mongo_url = "mongodb+srv://" . $Secret["mongodb.username"] . ":" . $Secret["mongodb.password"] . "@" . $Secret["mongodb.server"] . "/?retryWrites=true&w=majority&appName=h";
$manager = new MongoDB\Driver\Manager($mongo_url);

$command = new MongoDB\Driver\Command([
    'listCollections' => 1,
    'filter' => ['name' => 'data'],
]);
$cursor = $manager->executeCommand('nxu_charge', $command);
// 判断是否存在
$collections = $cursor->toArray();
if (empty($collections)) {
    // 创建main
    $main = '{"id": 1, "writing": false, "write-time": 0, "num": 0, "timestamp": 0, "token": "token", "token-time": 0, "token-usability": false, "visit": 0}';
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert(json_decode($main));
    $manager->executeBulkWrite('nxu_charge.data', $bulk);
    echo "Collection 'data' created.<br>";
} else {
    // 如果存在，跳过
    echo "Collection 'data' already exists.<br>";
}

// 遍历文档名称数组
foreach ($Data["DataMap"] as $documentName => $value) {
    // 检查文档是否存在
    $command = new MongoDB\Driver\Command([
        'listCollections' => 1,
        'filter' => ['name' => $documentName],
    ]);
    $cursor = $manager->executeCommand('nxu_charge', $command);
    // 判断是否存在
    $collections = $cursor->toArray();
    if (empty($collections)) {
        $bulk = new MongoDB\Driver\BulkWrite;
        for ($i = 0; $i < count($value); $i++) {
            $data = array(
                "productid" => $value[$i],
            );
            for ($j = 0; $j < 10; $j++) {
                $data[$j+1] = array(
                    "time" => 0,
                    "enable" => 1,
                );
            }
            $bulk->insert($data);
        }
        $manager->executeBulkWrite('nxu_charge.' . $documentName, $bulk);
        echo "Collection '{$documentName}' created.<br>";
    } else {
        // 如果存在，跳过
        echo "Collection '{$documentName}' already exists.<br>";
    }
}
?>