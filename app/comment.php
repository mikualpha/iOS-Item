<?php
/**
 * 评论模块
 * 
 * 实现拉取、提交、删除评论等的功能
 * 
 * @author  MikuAlpha
 * @version 1.0
 */
ini_set("display_errors", 1);
error_reporting(E_ALL);

include_once('../includes/functions.php');
include_once('../includes/auth.php');
include_once('../includes/database.php');

$userid = getUserId(verifyToken());
if ($userid === false) returnJson(401);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getComment();
        break;
    case 'PUT':
        addComment();
        break;
    default:
        returnJson(400);
}

function getComment() {
    $data = getArticleComment($_GET['groupId'], $_GET['offset']);
    returnJson(200, count($data) > 0 ? $data : null);
}

function addComment() {
    global $userid;
    parse_str(file_get_contents('php://input'), $data);
    
    putArticleComment($userid, $data['groupId'], $data['content']);
    returnJson(200);
}