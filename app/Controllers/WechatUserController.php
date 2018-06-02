<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/30
 * Time: 下午11:39
 */

namespace App\Controllers;

use App\Models\WechatUser;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class WechatUserController
{
    public function getUserInfoById(Request $request, Response $response, $args)
    {
        return $response->withJson(WechatUser::findOrFail($args['id']));
    }

    public function updateUserInfoById(Request $request, Response $response, $args)
    {
        $info = $request->getParsedBody();
        $user = WechatUser::findOrFail($args['id']);
        $user->update($info);
        return $response->withJson($user);
    }

    // TODO 微信登录
    public function createUserFromWxLogin(Request $request, Response $response){

    }
}