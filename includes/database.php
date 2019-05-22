<?php
/**
 * 数据库管理类
 * 
 * 负责构建SQL语句，与数据库进行交互，并生成接口提供给上一层。
 * 
 * @author  MikuAlpha
 * @version 1.1
 */

include_once('../settings/settings.php');

createTables();

/**
 * 初始化MySQL连接
 * 
 * @return mysqli MySQL连接对象
 */
function initConnection()
{
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysql->connect_error) die($mysql->connect_error);
    return $mysql;
}

/**
 * 当数据库表不存在时，创建表
 * 
 * @return void
 */
function createTables()
{
    $mysql = initConnection();
    //用户表
    $mysql->query('CREATE TABLE IF NOT EXISTS user (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(32) UNIQUE NOT NULL,
        passwd VARCHAR(255) NOT NULL
    ) DEFAULT CHARSET = utf8');

    //点赞表
    $mysql->query('CREATE TABLE IF NOT EXISTS liked (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
        userid INTEGER NOT NULL,
        article VARCHAR(255) NOT NULL
    ) DEFAULT CHARSET = utf8');

    //用户信息表
    $mysql->query( 'CREATE TABLE IF NOT EXISTS userinfo (
        id INTEGER PRIMARY KEY,
        nickname VARCHAR(255) NOT NULL,
        signment VARCHAR(255) DEFAULT \'\'
    ) DEFAULT CHARSET = utf8');

    //ENGINE = InnoDB 
    if ($mysql->error) die($mysql->error);
    $mysql->close();
}

/**
 * 获取对应用户存储在数据库的哈希值，便于进行验证
 * 
 * @param string $user
 * @return string|bool 成功时返回Hash值，失败时返回false
 */
function getUserPasswdHash($user)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT passwd FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return false;

    $stmt->bind_result($passwd_hash);
    $stmt->fetch();

    $stmt->close();
    $mysql->close();

    return $passwd_hash;
}

/** 
 * 检查用户是否存在（安全起见，不应直接调用此函数）
 * 
 * @param string $user 用户名
 * @return bool 是否存在
 */
function findUser($user)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    $result = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($result > 0);
}

/** 
 * 添加用户
 * 
 * @param string $username 用户名
 * @param string $passwd 密码Hash值
 * @return void
 */
function addUser($username, $passwd)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("INSERT IGNORE INTO user (username, passwd) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $passwd);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
 * 获取用户名对应的ID
 * 
 * @param string $username 用户名
 * @return int|bool 成功时返回用户ID，失败时返回false
 */
function getIdByUsername($username)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT id FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return false;

    $stmt->bind_result($id);
    $stmt->fetch();

    $stmt->close();
    $mysql->close();

    return $id;
}

/**
 * 根据文章ID返回点赞数量
 * 
 * @param string $articleId 文章ID
 * @return int 点赞数量
 */
function getLikeCountByArticle($articleId)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM liked WHERE article = ?");
    $stmt->bind_param("s", $articleId);
    $stmt->execute();
    $stmt->store_result();

    $count = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return $count;
}

/**
 * 检查该用户是否已点赞某文章
 * 
 * @param string $articleId 文章ID
 * @param int $userId 用户ID
 * @return bool 用户是否已点赞
 */
function isUserLiked($articleId, $userId)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM liked WHERE article = ? AND userid = ?");
    $stmt->bind_param("si", $articleId, $userId);
    $stmt->execute();
    $stmt->store_result();

    $count = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($count > 0);
}

/**
 * 点赞某文章
 * 
 * @param string $articleId 文章ID
 * @param int $userId 用户ID
 * @return void
 */
function addLike($articleId, $userId)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("INSERT IGNORE INTO liked (userid, article) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $articleId);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
 * 取消点赞某文章
 * 
 * @param string $articleId 文章ID
 * @param int $userId 用户ID
 * @return void
 */
function removeLike($articleId, $userId)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("DELETE IGNORE FROM liked WHERE userid = ? AND article = ?");
    $stmt->bind_param("is", $userId, $articleId);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
 * 获取用户的点赞数量
 * 
 * @param int $userId 用户ID
 * @return int 该用户的点赞总数
 */
function getUserLikeCount($userId) {
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM liked WHERE userid = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();

    $count = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return $count;
}

/**
 * 获取用户信息（昵称，签名等）
 * 
 * @param int $userId 用户ID
 * @return array 包含昵称($nickname)和签名($signment)的数组
 */
function getUserInfoById($userId) {
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT nickname, signment FROM userinfo WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();

    $stmt->bind_result($nickname, $signment);
    $stmt->fetch();

    $stmt->close();
    $mysql->close();

    return array('nickname' => $nickname, 'signment' => $signment);
}

/**
 * 添加/修改用户信息
 * 
 * @param int $userId 用户ID
 * @param string $nickname 用户昵称
 * @param string $signment 用户签名
 * @return void
 */
function addUserInfo($userId, $nickname, $signment = "") {
    $mysql = initConnection();
    $stmt = $mysql->prepare("REPLACE INTO userinfo (id, nickname, signment) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $nickname, $signment);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}