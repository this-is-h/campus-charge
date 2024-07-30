<?php
require("total/secret.php");
date_default_timezone_set('Asia/Shanghai');

ob_end_clean();
header("Connection: close");
header("HTTP/1.1 200 OK");
header("Content-Type: application/json;charset=utf-8");// 如果前端要的是json则添加，默认是返回的html/text
ob_start();
echo "SUCCESS<br>";// 输出结果到前端
echo round(microtime(true) * 1000);
$size = ob_get_length();
header("Content-Length: $size");
ob_end_flush();
flush();
// if (function_exists("fastcgi_finish_request")) { // yii或yaf默认不会立即输出，加上此句即可（前提是用的fpm）
//     fastcgi_finish_request(); // 响应完成, 立即返回到前端,关闭连接
// }
ignore_user_abort(true);// 在关闭连接后，继续运行php脚本

sleep(50);
$mongo_url = "mongodb+srv://" . $Secret["mongodb.username"] . ":" . $Secret["mongodb.password"] . "@" . $Secret["mongodb.server"] . "/?retryWrites=true&w=majority&appName=h";
$manager = new MongoDB\Driver\Manager($mongo_url);

$time = round(microtime(true) * 1000);
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert(["time" => $time]);
$manager->executeBulkWrite('nxu_charge.test', $bulk);