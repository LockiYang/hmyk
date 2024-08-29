<?php

/**
 * 构造支付数据
 */
function pay($param, $info = null)
{
    if ($info == null) {
        $plugin_path = ROOT_PATH . "content/goodpay/";
        $info = include_once "{$plugin_path}setting.php";
    }

    $host = \hehe\Network::getHostDomain();

    // 支付方式：支付宝、微信
    $pay_type = $param['pay_type'];

    $data = array(
        'appId' => $info['app_id'],
        'version' => '1.0',
        'nonceStr' => substr(md5(time()), 5, 10),
        'orderId' => $param['out_trade_no'],
        'amount' => $param['money'] * 100,
        'goodsName' => $param['subject'],
        'goodsDesc' => $param['subject'],
        'clientIp' => '127.0.0.1',
        'asyncNotifyUrl' => "{$host}/index/notify/index/plugin/goodpay/hm_type/{$param['hm_type']}",
        'payChannel' => 'ALP',
        'tradeType' => 'H5'
    );

    $data['sign'] = getSign($data, $info['md5_key']);
    $gateway_url = rtrim($info['gateway_url'], '/') . '/proxy/pay/unifiedorder';
    // $data['gateway_url'] = $gateway_url;

    $pay_data = json_decode(post($gateway_url, $data), true);

    // 判断$pay_data code 是否为0 和 data 是否存在
    if (!isset($pay_data['data'])) {
        return [
            'code' => 400,
            'msg' => '支付请求失败，请重试',
        ];
    }

    $result = [
        'qr_code' => $pay_data['data'],
        'out_trade_no' => $param['out_trade_no'],
        'hm_type' => $param['hm_type'],
        'pay_type' => $pay_type,
        'pay_data' => $pay_data
    ];

    return [
        'code' => 200,
        'data' => base64_encode(json_encode($result)),
        'mode' => 'scan'
    ];
}

/**
 * 回调回来时验签
 */
function checkSign($params = null)
{
    $plugin_path = ROOT_PATH . "content/goodpay/";
    $info = include_once "{$plugin_path}setting.php";

    $sign = $params['sign'];
    $server_sign = getSign($params, $info['md5_key']);
    if ($server_sign == $sign) {
        return [
            'out_trade_no' => $params['orderId'], // 商户订单号
            'trade_no' => $params['transactionId'] // 支付平台订单号
        ];
    }
    return false;
}

/**
 * 生成签名结果
 * return 签名结果字符串
 */
function getSign($data, $secret_key)
{
    foreach ($data as $key => $val) {
        if ($key == "sign" || $key == "sign_type") {
            unset($data[$key]);
        }
    }

    ksort($data);
    reset($data);

    $prestr  = urldecode(http_build_query($data));
    $sign = strtoupper(md5($prestr . "&key=" .  $secret_key));
    return $sign;
}

function post($url, $data)
{
    if (is_array($data)) {
        $data = http_build_query($data);
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER,  array(
        "Content-Type: application/x-www-form-urlencoded"
    ));
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}

// $data = array(
//     'appId' => 'abc',
//     'version' => '1.0',
//     'nonceStr' => substr(md5(time()), 5, 10),
//     'orderId' => 'abc',
//     'amount' => 100,
//     'goodsName' => 'abc',
//     'goodsDesc' => 'abc',
//     'clientIp' => '127.0.0.1',
//     'asyncNotifyUrl' => 'abc',
//     'payChannel' => 'ALP',
//     'tradeType' => 'H5'
// );

// print_r(getSign($data, 'abc'));
// print_r(sign($data, 'abc'));
