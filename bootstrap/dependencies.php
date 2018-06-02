<?php
/**
 * 依赖配置
 * User: ZhuKaihao
 * Date: 2018/4/27
 * Time: 下午12:36
 */


// 配置Eloquent
$container = $app->getContainer();
$capsule = new \Illuminate\Database\Capsule\Manager;    // 这些语句必须放在闭包外面
$capsule->addConnection($container['settings']['db']); // 创建一个数据库的链接

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
$capsule->setEventDispatcher(new Dispatcher(new Container)); // 添加事件分发器

$capsule->setAsGlobal(); // 静态可访问
$capsule->bootEloquent(); // 启动Eloquent
$capsule->getConnection()->enableQueryLog(); // 打开SQL日志记录
$container['db'] = function ($container) use ($capsule){
    return $capsule;
};

