<?php
include ('../includes/functions.php');

const URL = 'https://i.snssdk.com/course/article_content';

$param = array();
$param['groupId'] = $_POST['groupId'];

$json = json_decode(POST(URL, $param), true);

if ($json['code'] == -1) returnJson(400);

returnJson(200, $json['data']);