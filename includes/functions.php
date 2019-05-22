<?php
/**
 * 杂项函数类
 * 
 * 此处放置一些无法明确分类的函数。
 * 
 * @author  MikuAlpha
 * @version 1.1
 */

//状态码列表
const STATUS_CODE = array(
    200 => 'OK',
    201 => 'Created',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not Found',
    429 => 'Too Many Requests',
    500 => 'Internal Server Error',
    502 => 'Bad Gateway',
    504 => 'Gateway Timeout',
    1001 => 'Username Length Too Short(<4)',
    1002 => 'Password Length Too Short(<8)',
    1003 => 'Username Length Too Long(>=20)',
    1004 => 'Password Length Too Long(>=20)',
    1005 => 'Invaild Username',
    1006 => 'Invaild Password',
    1010 => 'Invaild Token',
    1011 => 'Token Already Expired'
);

/**
 * 为JSON对象附加状态码，返回并中止此次处理
 * 
 * @param int $httpCode HTTP状态码
 * @param array $jsonObj 输入的JSON对象
 */
function returnJson($httpCode, $jsonObj = array())
{
    //如果状态码不存在于列表中，则返回500
    if (!array_key_exists($httpCode, STATUS_CODE)) {
        $httpCode = 500;
    }
    $status = array();
    $status['status'] = $httpCode;
    $status['msg'] = STATUS_CODE[$httpCode];
    header('Content-type:application/json');
    die(json_encode(array_merge($status, $jsonObj)));
}

/**
 * 进行POST通信
 * 
 * @param string $url URL地址
 * @param string $param 访问时的传参
 * @return string 该URL返回的内容
 */
function POST($url, $param = '')
{
    if (empty($url) || empty($param)) {
        return false;
    }
    $postUrl = $url;
    $curlPost = $param;
    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1");
    //curl_setopt($ch, CURLOPT_PROXYPORT, 8088);
    curl_setopt($ch, CURLOPT_URL, $postUrl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
