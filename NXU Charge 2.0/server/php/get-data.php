<?php
// 设定时区为中国
date_default_timezone_set('Asia/Shanghai');
function data($number, $token) {
    // 服务器禁止使用
    // global $token;
    $url = "https://h5.2ye.cn/api/charger/port?productid=" . $number;
    $headers = array(
        'Host: h5.2ye.cn',
        'Connection: keep-alive',
        'Content-Length: 0',
        'tls: ' . floor(microtime(true) * 1000), // Unix timestamp in milliseconds
        'Accept: application/json, text/plain, */*',
        'clientid: your clientid', //自行从官方接口爬取 clientid
        'token: ' .  $token,
        'User-Agent: Mozilla/5.0 (Linux; Android 12; ELS-AN00 Build/HUAWEIELS-AN00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/86.0.4240.99 XWEB/4435 MMWEBSDK/20230202 Mobile Safari/537.36 MMWEBID/9699 MicroMessenger/8.0.33.2320(0x28002151) WeChat/arm64 Weixin NetType/WIFI Language/zh_CN ABI/arm64',
        'Origin: https://h5.2ye.cn',
        'X-Requested-With: com.tencent.mm',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        'Referer: https://h5.2ye.cn/',
        'Accept-Encoding: gzip, deflate',
        'Accept-Language: zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 800);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_ENCODING, '');
    $output = curl_exec($ch);
    if ($output === false) {
        // 获取错误信息和错误代码
        $error_message = curl_error($ch);
        $error_code = curl_errno($ch);
        curl_close($ch);
        return array(false, "Error: $error_message ($error_code)<br>");
    } else {
        curl_close($ch);
        preg_match("/\{.*\}/", $output, $result);
        $result = json_decode($result[0], true);
        return array(true, $result);
    }
}

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

$token_get = true;
$sql = "SELECT `token` FROM `data` WHERE id = 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $token = $row["token"];
    }
} else {
    $token_get = false;
    $response['warning'] = true;
    $response['warning_msg'] = 'token获取错误';
}
// echo $token . '<br>';

$finish_loop = false;
while ($token_get && !$finish_loop) {
    // echo $seepower_pid . "<br>";
    // var_dump($curlHandles);
    if ($finish_loop && count($locate) == 0) {
        break;
    }
    foreach ($locate as $pile) {
        $response_now = data($pile, $token);
        // var_dump($response_now);
        if (!$response_now[0]) {
            continue;
        }
        unset($locate[$pile]);
        $data = $response_now[1];
        if ($data['err_msg'] == 'token已失效') {
            $finish_loop = true;
            $locate = array();
            $response['warning'] = true;
            $response['warning_msg'] = 'token过期';
            break;
        }
        foreach ($data['data'] as $index => $pile_data) {
            if ($pile_data['enable'] == 1) {
                $response['data'][$pile][$index] = '1702374170000';
            }
        }
    }
    $finish_loop = true;
}

$jsonString = json_encode($response);
if (!$jsonString) {
    return;
}

echo $jsonString;
?>