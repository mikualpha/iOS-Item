<?php
/**
 * 文件上传类
 * 
 * 利用OSS进行文件上传并进行管理
 * 
 * @author  MikuAlpha
 * @version 1.0
 */

require_once('../vendor/autoload.php');

include('../settings/settings.php');

use OSS\Core\OssException;
use OSS\OssClient;

function uploadImage($path = '', $filename = '') {
    try {
        $ossClient = new OssClient(
            OSS_ACCESS_KEY_ID,
            OSS_ACCESS_KEY_SECRET,
            OSS_ENDPOINT,
            false
        );
    } catch (OssException $e) {
        printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
        printf($e->getMessage() . "\n");
        return null;
    }

    if ($path == '') $path = $_FILES['file']['tmp_name'];
    if ($filename == '') $filename = generateFileName();

    $ossClient->uploadFile(OSS_BUCKET_NAME, $filename, $path);
    return "https://" . OSS_BUCKET_NAME . "." .  OSS_ENDPOINT . "/" . $filename . "?x-oss-process=style/thumbnail";
}

function generateFileName() {
    $ext = substr($_FILES['file']['name'], strrpos($_FILES['file']['name'], '.') + 1); //上传文件后缀
    list($msec, $sec) = explode(' ', microtime());
    $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    $dst = md5($msectime) . '.' . $ext; //上传文件名称
    return $dst;
}