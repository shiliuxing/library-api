<?php
/**
 * 入口文件
 * User: ZhuKaihao
 * Date: 2018/4/27
 * Time: 上午10:08
 */

date_default_timezone_set("Asia/Shanghai");

require '../vendor/autoload.php';

// 初始化APP
$config = require '../bootstrap/configuration.php';
$app = new \Slim\App($config);

// 注入依赖
require '../bootstrap/dependencies.php';

// 错误处理
require '../bootstrap/error_handlers.php';

// 添加路由
require '../bootstrap/routes/index.php';

// 引入其他必要文件
require '../app/error_message.php';

// 运行APP
$app->run();