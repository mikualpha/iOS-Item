<?php
/**
 * 用户验证类
 * 
 * 主要负责与数据库进行交互并进行逻辑判断，以进行用户登录验证。
 * 
 * @author  Mikualpha
 * @version 1.1
 */

use \Lcobucci\JWT\Builder;
use \Lcobucci\JWT\Signer\Hmac\Sha256;
use \Lcobucci\JWT\Parser;

include ( '../vendor/autoload.php' );
include_once ( '../includes/database.php' );
include_once ( '../settings/settings.php' );

/**
 * 验证已存在的用户名与密码是否匹配
 * 
 * @param string $username 用户名(须事先验证是否存在)
 * @param string $password 密码
 * @return string|bool 成功时返回Token，失败返回false
 */
function verifyPassword($username, $password)
{
    $hash = getUserPasswdHash($username);
    if (!$hash) return false;

    if (passwordHash($password) !== $hash) return false;

    return generateToken($username, passwordHash($password));
}

/**
 * 根据用户名生成Token
 * 
 * @param string $username 用户名(须事先验证是否存在)
 * @param string $passwd 密码的Hash值
 * @return string 生成的Token
 */
function generateToken($username, $passwd)
{
    $builder = new Builder();
    $signer = new Sha256();
    // 设置发行人
    $builder->setIssuer(HOST_NAME);
    // 设置接收人
    $builder->setAudience(HOST_NAME);
    // 设置id
    $builder->setId($username, true);
    // 设置生成token的时间
    $builder->setIssuedAt(time());
    // 当前时间在这个时间前，token不能使用
    //$builder->setNotBefore(time() + 1);
    // 设置过期时间
    $builder->setExpiration(time() + EXPIRE_TIME);
    // 对上面的信息使用sha256算法签名
    $builder->sign($signer, SHA256_PRIVATE_KEY . getTokenCaptcha($passwd));
    // 获取生成的token
    $token = $builder->getToken();
    return (string)$token;
}

/**
 * 检验Token是否合法
 * 
 * @param string $input Token(可选输入,不输入则默认从HTTP Header的Authorization取出)
 * @return string|bool 成功时返回用户名，失败时返回false
 */
function verifyToken($input = '')
{
    $token = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
    if (!empty($input)) $token = $input;
    if (!isset($token)) return false;

    try {
        $signer  = new Sha256();
        $parse = (new Parser())->parse($token);

        $username = $parse->getClaim('jti');
        $hash = getUserPasswdHash($username);
        //验证token合法性
        if (!$parse->verify($signer, SHA256_PRIVATE_KEY . getTokenCaptcha($hash))) return false;

        //验证是否已经过期
        if ($parse->isExpired()) return false;

        return $username;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * 检查用户是否存在
 * 
 * @param string $user 用户名
 * @return bool 该用户是否存在
 */
function isUserExists($user)
{
    return findUser($user);
}

/**
 * 执行注册操作
 * 
 * @param string $username 用户名
 * @param string $password 密码
 * @return string 生成的Token
 */
function regist($username, $password)
{
    $passwd = passwordHash($password);
    addUser($username, $passwd);
    return generateToken($username, $passwd);
}

/**
 * 密码散列函数，对输入的密码进行Hash运算
 * 
 * @param string $passwd 密码
 * @return string 该密码的HASH结果
 */
function passwordHash($passwd)
{
    return md5(md5($passwd) . PASSWORD_SALT);
}

/**
 * 获取用户对应的特殊信息，以生成Token
 * 
 * @param string $passwd 用户的密码Hash值
 * @return string 验证字符串
 */
function getTokenCaptcha($passwd)
{
    return substr($passwd, -8);
}

/**
 * 获取用户ID
 * 
 * @param string $username 用户名
 * @return int|bool 成功时返回用户ID，失败时返回false
 */
function getUserId($username) {
    return (int)getIdByUsername($username);
}

/**
 * 检查用户名格式合法性
 * 
 * @param string $username 用户名
 * @return bool 是否合法
 */
function checkUsername($username)
{
    $username = strtolower($username);

    //检查注册用户名长度
    if (strlen($username) < 4) returnJson(1001);
    if (strlen($username) >= 20) returnJson(1003);

    $check = preg_match('/^[a-z]\w{3,19}$/is', $username);
    if ($check <= 0) returnJson(1005);

    return true;
}

/**
 * 检查密码格式合法性
 * 
 * @param string $password 密码
 * @return bool 是否合法
 */
function checkPassword($password)
{
    //检查注册密码长度
    if (strlen($password) < 8) returnJson(1002);
    if (strlen($password) >= 20) returnJson(1004);

    $check = preg_match('/^\w{8,20}$/is', $password);
    if ($check <= 0) returnJson(1005);

    return true;
}
