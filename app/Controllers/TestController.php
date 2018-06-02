<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/3
 * Time: 下午3:32
 */

namespace App\Controllers;


use App\Authorization\Authorization;
use App\Models\WechatUser;
use Firebase\JWT\JWT;

class TestController
{
    public function index($request, $response, $args)
    {
        $id = $request->getQueryParams()['id'];
        print_r(Authorization::encode([
            'user_type' => 'wechat',
            'user_id' => 15
        ]));
    }
}