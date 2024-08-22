<?php
function dataStatus($number, $token) {
    // 服务器禁止使用
    // global $token;
    $url = "https://h5.2ye.cn/api/charger/port?productid=" . $number;
    $headers = array(
        'Host: h5.2ye.cn',
        'Connection: keep-alive',
        'Content-Length: 0',
        'tls: ' . floor(microtime(true) * 1000), // Unix timestamp in milliseconds
        'Accept: application/json, text/plain, */*',
        'clientid: tffGh78Yurte54t5b',
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
    // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt($ch, CURLOPT_ENCODING, '');
    return $ch;
}

function getToken($Secret) {
    $mongo_url = "mongodb+srv://" . $Secret["mongodb.username"] . ":" . $Secret["mongodb.password"] . "@" . $Secret["mongodb.server"] . "/?retryWrites=true&w=majority&appName=h";
    $manager = new MongoDB\Driver\Manager($mongo_url);
    $seepower_pid = 0;
    $filter = ["id" => 1];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options);
    $documents = $manager->executeQuery('nxu_charge.data', $query);
    foreach($documents as $document){
        $document = json_decode(json_encode($document),true);
        $token = $document['token'];
    }
    return $token;
}

function main($Secret, $Data, $Get) {
    date_default_timezone_set('Asia/Shanghai');

    $result = array(
        "code" => 200,
        "successful" => true,
        "token" => true,
    );

    if (empty($Get['pile'])) {
        $result["code"] = 300;
        $result["successful"] = false;
        $result["error_msg"] = "pile参数为空";
        die(json_encode($result));
    }
    $pile = $Get['pile'];
    if (!array_key_exists($pile, $Data["DataMap"])) {
        $result["code"] = 301;
        $result["successful"] = false;
        $result["error_msg"] = "pile参数有误";
        die(json_encode($result));
    }

    $data_json = array();
    $token = getToken($Secret);
    $total_num = $Data["DataMap"][$pile];
    $curlHandles = [];
    $curlRetry = [];
    foreach ($total_num as $product_id) {
        $ch_now = dataStatus($product_id, $token);
        $curlHandles[$product_id] = $ch_now;
        $curlRetry[$product_id] = 0;
        // echo "add $product_id";
        // echo "<br>";
    }
    // echo "<br><br><br><br>";
    $i=0;
    while (true) {
        if (!$result['token']) {
            break;
        }
        // echo "<br>";
        if (count($curlHandles) == 0) {
            break;
        }
        $product_id = $total_num[$i];
        // 执行多个 cURL 句柄
        // echo "run";
        // echo "<br>";
        $ch = $curlHandles[$product_id];
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        unset($curlHandles[$product_id]);
        // echo "done";
        // echo "<br>";
        if (empty($response)) {
            // 处理出错的情况
            // echo "error $id";
            // echo "<br>";
            // 关闭出错的句柄
            if ($curlRetry[$product_id] > 2) {
                continue;
            }

            $curlRetry[$product_id]++;
            // 重新创建句柄并添加到多句柄中
            $newCh = dataStatus($product_id, $token); // 使用相同的 URL 重新创建句柄                
            $curlHandles[$product_id] = $newCh;
            // echo "retry $id";
            // echo "<br>";
            continue;
        }
        $data = json_decode($response, true);
        if ($data['err_msg'] == 'token已失效') {
            $result['token'] = false;
            break;
        }
        foreach ($data['data'] as $index => $pile_data) {
            $data_json[$product_id][$index+1]["enable"] = $pile_data['enable'];
            $data_json[$product_id][$index+1]["time"] = 0;
        }
        $i++;
    }
    $result["data"] = $data_json;
    $result["time"] = round(microtime(true) * 1000);
    echo json_encode($result);
}