<?php
/**
 * 路由
 * 文件夹结构
 * --- modules/         各个模块的路由配置, 为每个路由添加参数校验及权限控制中间件
 * --- validator.php    通用的参数校验方法
 * --- permission.php   token 校验方法
 * --- index.php        配置路由
 * User: ZhuKaihao
 * Date: 2018/4/27
 * Time: 下午12:35
 */

use App\Authorization\TokenValidator;
use Respect\Validation\Validator as v;

/**
 * Controller 命名空间
 */
define('PREFIX', 'App\Controllers\\');

/**
 * 添加自定义规则
 */
v::with('App\\Validators\\Rules', true);

/**
 * 引入各模块路由配置
 */
require 'modules/code.php';
require 'modules/user.php';
require 'modules/book.php';
require 'modules/booklist.php';
require 'modules/classification.php';
require 'modules/library.php';
require 'modules/review.php';
require 'modules/orders.php';
require 'modules/collection.php';
require 'modules/upload.php';


/**
 * 测试页面
 */
$app->any('/api/test', PREFIX . 'TestController:index')->add(TokenValidator::class);