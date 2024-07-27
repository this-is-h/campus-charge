<?php
function main() { 
    $curl = curl_init();
    curl_setopt_array($curl, [
    CURLOPT_URL => "https://h5.2ye.cn/api/oauth/authurl",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_POSTFIELDS => "backurl=https://h5.2ye.cn/#/login",
    CURLOPT_HTTPHEADER => [
        "Accept: application/json, text/plain, */*",
        "Accept-Encoding: gzip, deflate",
        "Accept-Language: zh-CN,en-US;q=0.9",
        "Connection: keep-alive",
        "Content-Length: 45",
        "Content-Type: application/x-www-form-urlencoded",
        "Host: h5.2ye.cn",
        "Origin: https://h5.2ye.cn",
        "Referer: https://h5.2ye.cn/",
        "User-Agent: Mozilla/5.0 (Linux; Android 9; ASUS_I005DA Build/PI; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/68.0.3440.70 Mobile Safari/537.36 MMWEBID/4846 MicroMessenger/8.0.47.2560(0x28002F51) WeChat/arm64 Weixin NetType/WIFI Language/zh_CN ABI/arm64",
        "X-Requested-With: com.tencent.mm",
        "clientid: tffGh78Yurte54t5b",
        "content-type: application/x-www-form-urlencoded",
        "tls: 1721557175936",
        "version: 1.0.1"
    ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        die("cURL Error #:" . $err);
    }

    // echo str_replace('https%3A%2F%2Fh5.2ye.cn%2F%40%2Flogin', 'https%3A%2F%2Fnxu-charge.thisish.cn%2Fapi%2Fphp%2Fback-token', json_decode($response, true)["data"]["authurl"]);
    header("location:" . str_replace('https%3A%2F%2Fh5.2ye.cn%2F%40%2Flogin', 'https%3A%2F%2Fnxu-charge.thisish.cn%2Fapi%2Fphp%2Fback-token', json_decode($response, true)["data"]["authurl"]));
}