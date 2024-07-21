<?php
require('secret.php');
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
    // $output = curl_exec($ch);
    // $output = curl_exec($ch);
    // // var_dump($output);
    // // 检查是否有错误发生
    // if ($output === false) {
    //     // 获取错误信息和错误代码
    //     $error_message = curl_error($ch);
    //     $error_code = curl_errno($ch);
    //     echo "$number - Error: $error_message ($error_code)<br>";
    //     curl_close($ch);
    //     return null;
    // } else {
    //     curl_close($ch);
    //     preg_match("/\{.*\}/", $output, $result);
    //     $result = json_decode($result[0], true);
    //     return $result;
    // }
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

// 查询数据库并更新数据
// $sql = "SELECT `using` FROM `data` WHERE id = 1";
// $result = $conn->query($sql);

// if ($result->num_rows > 0) {
//     while($row = $result->fetch_assoc()) {
//         $using = $row["using"];
//     }
// } else {
//     die("0 结果");
// }

// if ($using) {
//     // echo "正在更新中";
//     die("正在更新中");
// }

// $update_using = "UPDATE `data` SET `using` = TRUE WHERE id = 1";

// if ($conn->query($update_using) === TRUE) {
//     // echo "设置为正在更新";
// } else {
//     // echo "更新数据库失败: " . $conn->error;
// }

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
$seepower_pid_end = array();

$finish_loop = false;
$confirm_loop = false;

$multiHandle = curl_multi_init();
$curlHandles = [];

while (!$finish_loop || !$confirm_loop) {
    echo $seepower_pid . "<br>";
    // var_dump($curlHandles);
    if ($finish_loop) {
        if (count($curlHandles) == 0) {
            $confirm_loop = true;
        }
    } else {
        if ($seepower_pid - $seepower_pid_start > 500) {
            // echo "数额过大<br>";
            $seepower_pid_end[] = $seepower_pid;
            $finish_loop = true;
            continue;
        }
        for ($i = 0; $i < 10; $i++) {
            $seepower_pid_now = $seepower_pid + $i;
            $ch_now = data($seepower_pid_now);
            $curlHandles[$seepower_pid_now] = $ch_now;
            curl_multi_add_handle($multiHandle, $ch_now);
        }
    }
    // 执行多个 cURL 句柄
    $running = null;
    do {
        curl_multi_exec($multiHandle, $running);
        curl_multi_select($multiHandle); // 等待I/O事件
    } while ($running > 0);
    foreach ($curlHandles as $id => $ch) {
        unset($curlHandles[$id]);
        // // 检查请求是否出错
        // $curlError = curl_error($ch);
        $response = curl_multi_getcontent($ch);
        if (empty($response)) {
            // 处理出错的情况
            echo $id . ': error<br>';
            // 关闭出错的句柄
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
            
            // 重新创建句柄并添加到多句柄中
            $newCh = data($id); // 使用相同的 URL 重新创建句柄
            
            $curlHandles[$id] = $newCh;
            curl_multi_add_handle($multiHandle, $newCh);
            continue;
        }
        echo $id . ': ok<br>';
        curl_multi_remove_handle($multiHandle, $ch);
        curl_close($ch);
        $data = json_decode($response, true);
        if ($data && isset($data['err_code']) && $data['err_code'] === 502 && isset($data['err_msg']) && $data['err_msg'] === '记录不存在') {
            // echo "Record not found. Exiting loop.";
            $seepower_pid_end[] = $id;
            $finish_loop = true;
            continue;
        } else if (count($seepower_pid_end) > 0 && $id > min($seepower_pid_end)) {
            $seepower_pid_end = array();
            $finish_loop = false;
        }
        // 从响应数据中获取需要的字段
        $product_id = $data['data']['productid'];
        $port = $data['data']['port'];
        $start_time = $data['data']['start_date'];
        $total_time = $data['data']['total_time'];

        if (!array_key_exists($product_id, $dataArray)) {
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
            echo "数据库更新成功";
        } else {
            echo "更新数据库失败: " . $conn->error;
        }
    }
    $seepower_pid += 10;
}
echo $seepower_pid - $seepower_pid_start;
// 更新数据库
$now_time = round(microtime(true) * 1000);
$seepower_pid_end = min($seepower_pid_end);
$query = "UPDATE `data` SET `num` = $seepower_pid_end, `timestamp` = $now_time, `using` = FALSE WHERE id = 1";
if ($conn->query($query) === TRUE) {
    echo "数据库更新成功";
} else {
    echo "更新数据库失败: " . $conn->error;
}

echo microtime(true) - $time;