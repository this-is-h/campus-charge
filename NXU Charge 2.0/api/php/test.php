<?php
$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://h5.2ye.cn/api/charger/port?productid=88230816",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => [
    "Accept: application/json, text/plain, */*",
    "Accept-Encoding: gzip, deflate",
    "Accept-Language: zh-CN,en-US;q=0.9",
    "Connection: keep-alive",
    "Content-Length: 0",
    "Content-Type: application/x-www-form-urlencoded",
    "Cookie: acw_tc=0bde431a17215571530103415e7ae3bb5b2431a747d0af25dfaba57fdc1a76; Hm_lvt_2385c008d22fe4bcc4e4c630d64a5184=1721557158; Hm_lpvt_2385c008d22fe4bcc4e4c630d64a5184=1721557158; HMACCOUNT=D468159BF9AF3B1B",
    "Host: h5.2ye.cn",
    "Origin: https://h5.2ye.cn",
    "Referer: https://h5.2ye.cn/",
    "User-Agent: Mozilla/5.0 (Linux; Android 9; ASUS_I005DA Build/PI; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/68.0.3440.70 Mobile Safari/537.36 MMWEBID/4846 MicroMessenger/8.0.47.2560(0x28002F51) WeChat/arm64 Weixin NetType/WIFI Language/zh_CN ABI/arm64",
    "X-Requested-With: com.tencent.mm",
    "clientid: tffGh78Yurte54t5b",
    "tls: 1722221103564",
    "token: a8a4a54b9a82299b55520a75deaad400af80ca28",
    "version: 1.0.1"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}