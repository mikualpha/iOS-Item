<?php
/**
 * 头像上传类
 * 
 * 负责头像上传接口的实现
 * 
 * @author  MikuAlpha
 * @version 1.0
 */
include('../includes/file_upload.php');
include('../includes/functions.php');
include('../includes/database.php');
include('../includes/auth.php');

$userid = getUserId(verifyToken());
if (!$userid) returnJson(401);

$file = $_FILES['file'];

$name = $file['name'];
$type = strtolower(substr($name, strrpos($name, '.') + 1)); //得到文件类型，并且都转化成小写
$allow_type = array('jpg', 'jpeg', 'png'); //定义允许上传的类型

//判断文件类型是否被允许上传
if (!in_array($type, $allow_type)) returnJson(400);

//判断是否是通过HTTP POST上传的
if (!is_uploaded_file($file['tmp_name'])) returnJson(403);

$link = uploadImage();
editUserAvatarLink($userid, $link);

returnJson(200, array('link' => $link));