<?php
/**
 * 点赞模块
 * 
 * 进行鉴权，并根据请求方法进行相关操作
 * 
 * @author  MikuAlpha
 * @version 1.0
 */

include('../includes/auth.php');
include('../includes/functions.php');
include_once('../includes/database.php');

$userid = getUserId(verifyToken());
if (!$userid) returnJson(401);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getLikeCount();
        break;
    case 'PUT':
        addLikeCount();
        break;
    case 'DELETE':
        removeLikeCount();
        break;
    default:
        returnJson(400);
}

function getLikeCount()
{
    global $userid;
    $article_id = $_GET['id'];
    returnJson(200, array(
        'liked' => isUserLiked($article_id, $userid),
        'count' => getLikeCountByArticle($article_id)
    ));
}

function addLikeCount()
{
    global $userid;
    parse_str(file_get_contents('php://input'), $data);
    addLike($data['id'], $userid);
    returnJson(200);
}

function removeLikeCount()
{
    global $userid;
    parse_str(file_get_contents('php://input'), $data);
    removeLike($data['id'], $userid);
    returnJson(200);
}
