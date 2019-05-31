<?php
/**
 * 本文件仅为settings.php的样例。
 * 
 * 若需要运行程序，请将此文件复制并重命名为settings.php，再进行设置。
 * 
 * @author  MikuAlpha
 * @version 1.0
 */

//站点域名
define('HOST_NAME', 'example.com');

//加密盐值
define('PASSWORD_SALT', 'example');

//SHA256加密密钥
define('SHA256_PRIVATE_KEY', 'example233');

//Token过期时间，单位为秒(sec)
define('EXPIRE_TIME', 3600 * 2);

//数据库-主机地址
define('DB_HOST', 'localhost');

//数据库-用户名
define('DB_USER', 'root');

//数据库-密码
define('DB_PASS', 'example');

//数据库-库名称
define('DB_NAME', 'default');

//OSS-AccessKeyId
define('OSS_ACCESS_KEY_ID', '');

//OSS-AccessKeySecret
define('OSS_ACCESS_KEY_SECRET', '');

//OSS-EndPoint
define('OSS_ENDPOINT', '');

//OSS-Bucket
define('OSS_BUCKET_NAME', '');

//默认头像地址
define('DEFAULT_AVATAR', 'https://secure.gravatar.com/avatar/');