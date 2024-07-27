<?php
function data_start($number) {
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

function data_status($number, $token) {
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
    // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 800);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt($ch, CURLOPT_ENCODING, '');
    return $ch;
}

function handler($event, $context) {
    require("../total/data.php");
    require("../total/data.php");

    date_default_timezone_set('Asia/Shanghai');
    $time = microtime(true);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://nxu-charge.thisish.cn/api/get-id",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            "content-type: application/x-www-form-urlencoded"
        ],
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "get id failed" . $err;
        return;
    } else {
        $response = json_decode($response, true);
        $seepower_pid_start = $response["id"];
        $token = $response["token"];
    }

    echo microtime(true) - $time . '<br>';

    $data_json = array();

    $seepower_pid = $seepower_pid_start;
    $seepower_pid_end = array();
    $multiHandle = curl_multi_init();
    $curlHandles = [];
    while (true) {
        echo "<br>";
        while (true) {
            if (count($seepower_pid_end) > 0 || $seepower_pid - $seepower_pid_start >= 500) {
                break;
            }
            $ch_now = data_start($seepower_pid);
            $curlHandles[$seepower_pid] = $ch_now;
            curl_multi_add_handle($multiHandle, $ch_now);
            echo "add $seepower_pid";
            echo "<br>";
            if (count($curlHandles) >= 15) {
                break;
            }
            $seepower_pid++;
        }
        if (count($curlHandles) == 0) {
            break;
        }
        // 执行多个 cURL 句柄
        echo "run";
        echo "<br>";
        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle); // 等待I/O事件
        } while ($running > 0);
        echo "done";
        echo "<br>";

        foreach ($curlHandles as $id => $ch) {
            unset($curlHandles[$id]);
            // // 检查请求是否出错
            // $curlError = curl_error($ch);
            $response = curl_multi_getcontent($ch);
            if (empty($response)) {
                // 处理出错的情况
                echo "error $id";
                echo "<br>";
                // 关闭出错的句柄
                curl_multi_remove_handle($multiHandle, $ch);
                curl_close($ch);
                
                // 重新创建句柄并添加到多句柄中
                $newCh = data_start($id); // 使用相同的 URL 重新创建句柄
                
                $curlHandles[$id] = $newCh;
                curl_multi_add_handle($multiHandle, $newCh);
                echo "retry $id";
                echo "<br>";
                continue;
            }
            echo "ok $id";
            echo "<br>";
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
            $data = json_decode($response, true);
            if ($data && isset($data['err_code']) && $data['err_code'] === 502 && isset($data['err_msg']) && $data['err_msg'] === '记录不存在') {
                // echo "Record not found. Exiting loop.";
                $seepower_pid_end[] = $id;
                continue;
            } else if (count($seepower_pid_end) > 0 && $id > min($seepower_pid_end)) {
                $seepower_pid_end = array();
            }
            // 从响应数据中获取需要的字段
            $product_id = $data['data']['productid'];
            $port = $data['data']['port'];
            $start_time = $data['data']['start_date'];
            $total_time = $data['data']['total_time'];
            if (!array_key_exists($product_id, $DataArray)) {
                echo "not $id";
                echo "<br>";
                continue;
            }
            echo "++++++++++++++++++++++++++++ $id";
            echo "<br>";
            $pile = $DataArray[$product_id];
            // 将开始时间转换为时间戳
            $timeObj = strtotime($start_time);
            if ($timeObj === false) {
                // echo "时间解析错误\n";
                die();
            }
            // 加上指定的分钟数
            $timePlusBMinutes = strtotime("+" . $total_time . " minutes", $timeObj);
            // 将加上分钟数后的时间对象转换为毫秒级时间戳
            $end_time = $timePlusBMinutes * 1000; // 转换为毫秒级时间戳

            $data_json[$pile][$product_id][$port] = $end_time;
        }
    }

    $id_num = 0;
    $total_num = count($DataTotalId);
    $data_result['token'] = true;
    echo "<br><br><br><br>";
    while (true) {
        if (!$data_result['token']) {
            break;
        }
        echo "<br>";
        while (true) {
            if ($id_num > $total_num) {
                break;
            }
            $product_id = $DataTotalId[$id_num];
            $ch_now = data_status($product_id, $token);
            $curlHandles[$product_id] = $ch_now;
            curl_multi_add_handle($multiHandle, $ch_now);
            echo "add $product_id";
            echo "<br>";
            if (count($curlHandles) >= 15) {
                break;
            }
            $seepower_pid++;
        }
        if (count($curlHandles) == 0) {
            break;
        }
        // 执行多个 cURL 句柄
        echo "run";
        echo "<br>";
        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle); // 等待I/O事件
        } while ($running > 0);
        echo "done";
        echo "<br>";

        foreach ($curlHandles as $id => $ch) {
            unset($curlHandles[$id]);
            // // 检查请求是否出错
            // $curlError = curl_error($ch);
            $response = curl_multi_getcontent($ch);
            if (empty($response)) {
                // 处理出错的情况
                echo "error $id";
                echo "<br>";
                // 关闭出错的句柄
                curl_multi_remove_handle($multiHandle, $ch);
                curl_close($ch);
                
                // 重新创建句柄并添加到多句柄中
                $newCh = data_status($id); // 使用相同的 URL 重新创建句柄
                
                $curlHandles[$id] = $newCh;
                curl_multi_add_handle($multiHandle, $newCh);
                echo "retry $id";
                echo "<br>";
                continue;
            }
            echo "ok $id";
            echo "<br>";
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
            $data = json_decode($response, true);
            if ($data['err_msg'] == 'token已失效') {
                $data_result['token'] = false;
                break;
            }
            foreach ($data['data'] as $index => $pile_data) {
                if ($pile_data['enable'] == 1) {
                    $data_json[$DataIdToPile[$id]][$id][$index+1] = '1702374170000';
                }
            }
            $data_json[$pile][$product_id][$port] = $end_time;
        }
    }

    $data_result["data"] = $data_json;
    $data_result["update_time"] = round(microtime(true) * 1000);
    $data_result["end_id"] = min($seepower_pid_end);

    var_dump($data_result);
    echo json_encode($data_result);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://nxu-charge.thisish.cn/api/update-data",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data_result),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            "content-type: application/json"
        ],
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "update failed" . $err;
        return;
    } else {
        echo $response;
        echo "update succesful";
    }
}


// 如果缓冲区没有开启，直接调用ob_end_clean()会报错的，要先判断缓冲区有没有开启
// 如果ob_get_contents()不是返回false,说明有开启缓冲区(ob_start())
$buf = ob_get_contents();
if ($buf !== false) {
    // 输出header前，不能有任何输出内容，否则会报错，所以缓冲区里的内容要全部清空
    ob_end_clean();
}
ob_implicit_flush(); // 每次输出后都自动flush，这样就不需要咱们手动flush了

// 输出header，让Nginx不要使用buffer
header('X-Accel-Buffering: no');
handler(0, 0);