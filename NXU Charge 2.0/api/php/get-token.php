<?php

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://h5.2ye.cn/api/oauth/authurl",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_SSL_VERIFYPEER => FALSE,
  CURLOPT_POSTFIELDS => "backurl=https%3A%2F%2Fh5.2ye.cn%2F%23%2Flogin",
  CURLOPT_HTTPHEADER => [
    "Accept: application/json, text/plain, */*",
    "Accept-Encoding: gzip, deflate",
    "Accept-Language: zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7",
    "Connection: keep-alive",
    "Content-Length: 45",
    "Content-Type: application/x-www-form-urlencoded",
    "Host: h5.2ye.cn",
    "Origin: https://h5.2ye.cn",
    "Referer: https://h5.2ye.cn/",
    "Sec-Fetch-Dest: empty",
    "Sec-Fetch-Mode: cors",
    "Sec-Fetch-Site: same-origin",
    "User-Agent: Mozilla/5.0 (Linux; Android 8.0.0; LND-AL40 Build/HONORLND-AL40; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/116.0.0.0 Mobile Safari/537.36 XWEB/1160065 MMWEBSDK/20231202 MMWEBID/4846 MicroMessenger/8.0.47.2560(0x28002F30) WeChat/arm64 Weixin NetType/WIFI Language/zh_CN ABI/arm64",
    "X-Requested-With: com.tencent.mm",
    "clientid: tffGh78Yurte54t5b",
    "tls: 1710519717396",
    "version: 1.0.1"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  die("cURL Error #:" . $err);
}

header("location:" . json_decode($response, true)["data"]["authurl"]);