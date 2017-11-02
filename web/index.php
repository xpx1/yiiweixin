<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');
/**
 * 加入版本号
**/
if(file_exists('../data/www/version/version.txt')){
    define("VERSION",trim(file_get_contents('../data/www/version/version.txt')));//define定义一个常量
}else{
    define("VERSION",time());
}
(new yii\web\Application($config))->run();
