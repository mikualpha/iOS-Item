<?php
/**
 * 用户页面相关操作
 * 
 * 负责用户页面的修改、获取信息等。
 * 
 * @author  MikuAlpha
 * @version 1.0
 */

include('../includes/auth.php');
include('../includes/functions.php');
include_once('../includes/database.php');

$userid = getUserId(verifyToken());
if ($userid === false) returnJson(401);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getUserInfo();
        break;
    case 'POST':
        modifyUserInfo();
        break;
    default:
        returnJson(400);
}

function getUserInfo()
{
    global $userid;
    returnJson(
        200,
        array_merge(
            getUserInfoById($userid),
            array('like_count' => getUserLikeCount($userid))
        )
    );
}

function modifyUserInfo()
{
    global $userid;
    if (!isset($_POST['nickname']) || !isset($_POST['signment'])) returnJson(400);
    addUserInfo($userid, $_POST['nickname'], $_POST['signment']);
    returnJson(200);
}
