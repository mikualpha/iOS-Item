<?php
/**
 * 文章内容接口
 * 
 * 进行鉴权，从上游数据源取得数据处理后返回
 * 
 * @author  MikuAlpha
 * @version 1.0
 */

include ( '../includes/auth.php' );
include ( '../includes/functions.php' );

const URL = 'https://i.snssdk.com/course/article_content';

$verify = verifyToken();
if ($verify === false) returnJson(401);

$param = array();
$param['groupId'] = $_POST['groupId'];

$json = json_decode(POST(URL, $param), true);

if ($json['code'] == -1) returnJson(400);

returnJson(200, $json['data']);