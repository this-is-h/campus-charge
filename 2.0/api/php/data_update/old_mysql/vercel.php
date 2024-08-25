<?php
require('secret.php');
set_time_limit(0);
header('X-Accel-Buffering: no');
function data($number) {
    $url = "https://h5.2ye.cn/api/chargerlog/power?seepower_pid=" . $number;
    $headers = array(
        'Host: h5.2ye.cn',
        'Connection: keep-alive',
        'Content-Length: 0',
        'tls: ' . floor(microtime(true) * 1000), // Unix timestamp in milliseconds
        'Accept: application/json, text/plain, */*',
        'clientid: tffGh78Yurte54t5b',
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
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 800);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt($ch, CURLOPT_ENCODING, '');
    return $ch;
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

// 设定时区为中国
date_default_timezone_set('Asia/Shanghai');

$time = microtime(true);

echo 'start<br>';
ob_flush(); 
flush();

$servername = $Secret['mysql.server'];
$username = $Secret['mysql.username'];
$password = $Secret['mysql.password'];
$connname = $Secret['mysql.dbname'];

// 创建连接
$conn = new mysqli($servername, $username, $password, $connname);
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
// 检测连接
if ($conn->connect_error) {
    die('{"state": "error", "error": "connect error"}');
}

echo microtime(true) - $time . '<br>';

// 获取初始值
$sql = "SELECT `num` FROM `data` WHERE id = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $seepower_pid_start = $row["num"];
    }
} else {
    die();
}

if ($seepower_pid_start == -1) {
    echo "seepower_pid获取失败";
}

// $seepower_pid_start = 33566000;

$seepower_pid = $seepower_pid_start;
$seepower_pid_end = $seepower_pid_start;

$finish_loop = false;
$confirm_loop = false;

$retry_times = 0;
$error_pid = 0;

while (!$finish_loop) {
    echo $seepower_pid . "<br>";
    // var_dump($curlHandles);

    if ($seepower_pid - $seepower_pid_start > 500) {
        echo "数额过大<br>";
        $seepower_pid_end = $seepower_pid;
        $finish_loop = true;
        break;
    }

    $ch = data($seepower_pid);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo "cURL Error #:" . $err . '<br>';
        if ($seepower_pid == $error_pid) {
            if ($retry_times > 3) {
                $seepower_pid += 1;
                $retry_times = 0;
                continue;
            }
            echo $seepower_pid . ': retry ' . $retry_times . '<br>';
            $retry_times += 1;
            continue;
        }
        $error_pid = $seepower_pid;
        $retry_times += 1;
        echo $seepower_pid . ': retry ' . $retry_times . '<br>';
        continue;
    }

    if (empty($response)) {
        // 处理出错的情况
        echo $seepower_pid . ': error<br>';
        if ($seepower_pid == $error_pid) {
            if ($retry_times > 3) {
                $seepower_pid += 1;
                $retry_times = 0;
                continue;
            }
            echo $seepower_pid . ': retry ' . $retry_times . '<br>';
            $retry_times += 1;
            continue;
        }
        $error_pid = $seepower_pid;
        $retry_times += 1;
        echo $seepower_pid . ': retry ' . $retry_times . '<br>';
        continue;
    }

    $retry_times = 0;
    echo $seepower_pid . ': ok<br>';
    $data = json_decode($response, true);
    if ($data && isset($data['err_code']) && $data['err_code'] === 502 && isset($data['err_msg']) && $data['err_msg'] === '记录不存在') {
        $seepower_pid_end = $seepower_pid;
        $finish_loop = true;
        break;
    }
    // 从响应数据中获取需要的字段
    $product_id = $data['data']['productid'];
    $port = $data['data']['port'];
    $start_time = $data['data']['start_date'];
    $total_time = $data['data']['total_time'];

    if (!array_key_exists($product_id, $dataArray)) {
        $seepower_pid += 1;
        continue;
    }
    $pile = $dataArray[$product_id];
    // 将开始时间转换为时间戳
    $timeObj = strtotime($start_time);
    if ($timeObj === false) {
        echo "时间解析错误\n";
        die();
    }
    // 加上指定的分钟数
    $timePlusBMinutes = strtotime("+" . $total_time . " minutes", $timeObj);
    // 将加上分钟数后的时间对象转换为毫秒级时间戳
    $end_time = $timePlusBMinutes * 1000; // 转换为毫秒级时间戳

    // 更新数据库（假设数据库连接已建立）
    $query = "UPDATE `$pile` SET `$port` = '$end_time' WHERE `id` = '$product_id'";
    if ($conn->query($query) === TRUE) {
        echo $seepower_pid . ": mysql ok";
        echo '<br>';
    } else {
        echo $seepower_pid . ": mysql error " . $conn->error;
        echo '<br>';
    }

    $seepower_pid += 1;

    ob_flush(); 
    flush();
}

echo '<br>';
echo $seepower_pid_start;
echo '<br>';
echo $seepower_pid;
echo '<br>';
echo $seepower_pid - $seepower_pid_start;
echo '<br>';

// 更新数据库
$now_time = round(microtime(true) * 1000);
$seepower_pid_end = $seepower_pid_end - 1;
$query = "UPDATE `data` SET `num` = $seepower_pid_end, `timestamp` = $now_time, `using` = FALSE WHERE id = 1";
if ($conn->query($query) === TRUE) {
    echo "数据库更新成功";
} else {
    echo "更新数据库失败: " . $conn->error;
}

echo microtime(true) - $time;