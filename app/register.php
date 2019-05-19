<?php
/**
 * 注册接口
 * 
 * 提供注册操作，需要POST输入用户名与密码参数。
 * 
 * @author  MikuAlpha
 * @version 1.0
 */

include ( '../includes/auth.php' );
include_once ( '../includes/functions.php' );

$username = strtolower($_POST['username']);
$password = $_POST['password'];

//检查用户名及密码格式的合法性
if (!checkUsername($username) || !checkPassword($password)) returnJson(403);

//检查用户名是否存在
if (isUserExists($username)) returnJson(403);

//生成Token
$token = regist($username, $password);
if ($token) {
    returnJson(200, array('token' => $token));
} else {
    returnJson(404);
}

