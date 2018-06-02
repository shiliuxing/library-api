<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/27
 * Time: 下午12:34
 */

return [
    'settings' => [
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails'               => true,
        'db'                                => [
            'driver'    => 'mysql',
            'host'      => 'localhost',     // mysql 地址
            'database'  => 'library',       // 数据库名
            'username'  => 'root',          // mysql 账号
            'password'  => 'your_password', // mysql 密码
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict' => false
        ]
    ]
];