<?php
$dataMap = array(
    "c14" =>   array("88227178", "88227167", "88227166", "88227164", "89627130", "89627062", "89627114", "89627112", "89627111", "89624194", "89627126", "89627113"),
	"a2" =>    array("88227927", "88227928", "88227165"),
	"acar" =>  array("88227163", "89626666"),
	"b7" =>    array("88227942", "89623528"),
	"b10" =>   array("88227929", "88227943"),
	"c8" =>    array("86060206", "86060232", "86062777", "86062778", "86062829", "86060241", "86060202", "86062776", "86060208", "86060236"),
	"c7" =>    array("88232072", "88232071", "88232178", "88232176"),
	"c12" =>   array("88232173", "88232070", "88232177", "88232179"),
	"c13" =>   array("88232174", "88232175", "88232073", "88232172"),
	"c8-1" =>  array("88230806", "88230807", "88230461", "88230805", "88230704", "88230810", "88230812", "88230815", "88230816", "88230686"),
);
$dataArray = array(
    "88232072" => "c7",
	"88232071" => "c7",
	"88232178" => "c7",
	"88232176" => "c7",
	
	"88232173" => "c12",
	"88232070" => "c12",
	"88232177" => "c12",
	"88232179" => "c12",
	
	"88232174" => "c13",
	"88232175" => "c13",
	"88232073" => "c13",
	"88232172" => "c13",
	
	"88230806" => "c8-1",
	"88230807" => "c8-1",
	"88230461" => "c8-1",
	"88230805" => "c8-1",
	"88230704" => "c8-1",
	"88230810" => "c8-1",
	"88230812" => "c8-1",
	"88230815" => "c8-1",
	"88230816" => "c8-1",
	"88230686" => "c8-1",
	
	"88227178" => "c14",
	"88227167" => "c14",
	"88227166" => "c14",
	"88227164" => "c14",
	"89627130" => "c14",
	"89627062" => "c14",
	"89627114" => "c14",
	"89627112" => "c14",
	"89627111" => "c14",
	"89624194" => "c14",
	"89627126" => "c14",
	"89627113" => "c14",
	
	"88227927" => "a2",
	"88227928" => "a2",
	"88227165" => "a2",
	
	"88227163" => "acar",
	"89626666" => "acar",
	
	"88227942" => "b7",
	"89623528" => "b7",
	
	"88227929" => "b10",
	"88227943" => "b10",
	
	"86060206" => "c8",
	"86060232" => "c8",
	"86062777" => "c8",
	"86062778" => "c8",
	"86062829" => "c8",
	"86060241" => "c8",
	"86060202" => "c8",
	"86062776" => "c8",
	"86060208" => "c8",
	"86060236" => "c8",
);

// // 允许跨源请求的域名列表
// $allowedOrigins = array(
//     "http://nxu.imqde.gq",
//     "http://localhost:5173",
// );

// // 获取请求头中的 Origin
// $headers = getallheaders();
// if (array_key_exists('Origin', $headers)) {
//     die(111);
// }
// $origin = $headers['Origin'];
// $origin_continue = false;

// // 检查 Origin 是否在允许的域名列表中
// // 如果 Origin 存在于允许列表中，则设置对应的 CORS 头部
// foreach ($allowedOrigins as $allowedOrigin) {
//     if ($allowedOrigin == $origin) {
//         header("Access-Control-Allow-Origin: $origin");
//         $origin_continue = true;
//         break;
//     }
// }

// if (!$origin_continue) {
//     return;
// }

$servername = "your server name";
$username = "your username";
$password = "your password";
$connname = "your con name";

// 创建连接
$conn = new mysqli($servername, $username, $password, $connname);
// 检测连接
if ($conn->connect_error) {
    die('{"state": 301, "success": false, "error_msg": "数据库连接失败"}');
}

$pile = $_GET['pile'];
if (!array_key_exists($pile, $dataMap)) {
    die('{"state": 300, "success": false, "error_msg": "pile参数有误"}');
}
$locate = $dataMap[$pile];

$sqlStr = sprintf("select * from `%s` where id > 0", $pile);
$rows = $conn->query($sqlStr);
if (!$rows) {
    // printf("query failed, err:%s\n", $conn->error);
    die('{"state": 301, "success": false, "error_msg": "数据获取失败"}');
}

$pile_data = array();

foreach ($locate as $value) {
    $pile_data[$value] = array();
}

while ($row = $rows->fetch_assoc()) {
    $id = $row['id'];
    $value1 = $row['1'];
    $value2 = $row['2'];
    $value3 = $row['3'];
    $value4 = $row['4'];
    $value5 = $row['5'];
    $value6 = $row['6'];
    $value7 = $row['7'];
    $value8 = $row['8'];
    $value9 = $row['9'];
    $value10 = $row['10'];
    // 将1到4的数据拼接成字符串数组
    $dataSlice = array($value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8, $value9, $value10);
    // 将id和数据数组映射到dataMap中
    $pile_data[$id] = $dataSlice;
}

$sqlStr = "select timestamp from data where id=1";
$result = $conn->query($sqlStr);
if (!$result) {
    die('{"state": 301, "success": false, "error_msg": "获取数据更新时间失败"}');
}
$row = $result->fetch_assoc();
$timestamp = $row['timestamp'];

$response = array(
    "code" => 200,
    "success" => true,
    "data" => $pile_data,
    "timestamp" => $timestamp
);

$jsonString = json_encode($response);
if (!$jsonString) {
    return;
}

echo $jsonString;
?>
