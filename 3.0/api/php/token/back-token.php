<?php
require('secret.php');
// 设定时区为中国
date_default_timezone_set('Asia/Shanghai');

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://h5.2ye.cn/api/oauth/login",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_SSL_VERIFYPEER => FALSE,
    CURLOPT_POSTFIELDS => "authcode=" . $_GET["code"],
    CURLOPT_HTTPHEADER => [
        "Accept: application/json, text/plain, */*",
        "Accept-Encoding: gzip, deflate",
        "Accept-Language: zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7",
        "Connection: keep-alive",
        "Content-Length: 41",
        "Host: h5.2ye.cn",
        "Origin: https://h5.2ye.cn",
        "Referer: https://h5.2ye.cn/",
        "Sec-Fetch-Dest: empty",
        "Sec-Fetch-Mode: cors",
        "Sec-Fetch-Site: same-origin",
        "User-Agent: Mozilla/5.0 (Linux; Android 8.0.0; LND-AL40 Build/HONORLND-AL40; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/116.0.0.0 Mobile Safari/537.36 XWEB/1160065 MMWEBSDK/20231202 MMWEBID/4846 MicroMessenger/8.0.47.2560(0x28002F30) WeChat/arm64 Weixin NetType/WIFI Language/zh_CN ABI/arm64",
        "clientid: tffGh78Yurte54t5b",
        "tls: 1710519720331",
        "version: 1.0.1"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    die("cURL Error #:" . $err);
}
var_dump(json_decode($response, true));
echo "<br><br>";

$servername = $Secret['mysql.server'];
$username = $Secret['mysql.username'];
$password = $Secret['mysql.password'];
$connname = $Secret['mysql.dbname'];

// 创建连接
$conn = new mysqli($servername, $username, $password, $connname);
// 检测连接
if ($conn->connect_error) {
    die('{"state": 301, "success": false, "error_msg": "数据库连接失败"}');
}

$token = json_decode($response, true)["data"]["refresh_token"];
$now_time = round(microtime(true) * 1000);
$sql = "UPDATE `data` SET `token`='$token',`token-time`='$now_time' WHERE id=1";
echo $sql;
echo "<br><br>";


if ($conn->query($sql) === TRUE) {
    echo "成功更新token";
} else {
    echo "出现了一些问题...";
}
