<?php
/**
 * 文章列表接口
 * 
 * 进行鉴权，从上游数据源取得数据处理后返回
 * 
 * @author  MikuAlpha
 * @version 1.0
 */

include ( '../includes/auth.php' );
include ( '../includes/functions.php' );

define('URL', 'https://i.snssdk.com/course/article_feed');

$id = getUserId(verifyToken());
if (!$id) returnJson(401);

$param = array();
$param['uid'] = $id;
$param['offset'] = $_POST['offset'];
$param['count'] = $_POST['count'];

$json = json_decode(POST(URL, $param), true);

if ($json['code'] == -1) returnJson(400);

returnJson(200, $json['data']);