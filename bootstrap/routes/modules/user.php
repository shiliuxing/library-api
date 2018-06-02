<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/1
 * Time: 上午1:06
 */

use Respect\Validation\Validator as v;

$app->post('/api/users', PREFIX . 'WechatUserController:createUserFromWxLogin')->add(function ($request, $response, $next) {
    /**
     * 校验用户信息对象
     */
    $userInfo = $request->getParsedBody();
    $userValidator = v::ArrayVal()->keySet(
        v::key('phone', v::chinaPhone()),
        v::key('openid', v::stringType()->notEmpty()),
        v::key('nickname', v::stringType()->notEmpty(), false),
        v::key('avatar', v::stringType()->notEmpty(), false)
    )->setName('info');
    $userValidator->check($userInfo);
    return $next($request, $response);
});

$app->group('/api/users/{id:[0-9]+}', function () {
    $this->get('', PREFIX . 'WechatUserController:getUserInfoById');
    $this->post('', PREFIX . 'WechatUserController:updateUserInfoById')->add(function ($request, $response, $next) {
        /**
         * 校验用户信息对象
         */
        $userInfo = $request->getParsedBody();
        $userValidator = v::ArrayVal()->keySet(
            v::key('openid', v::stringType()->notEmpty(), false),
            v::key('nickname', v::stringType()->notEmpty(), false),
            v::key('avatar', v::stringType()->notEmpty(), false),
            v::key('name', v::stringType()->notEmpty(), false),
            v::key('birthday', v::date('Y-m-d'), false),
            v::key('id_number', v::stringType()->length(18, 18), false),
            v::key('id_card_img', v::keySet(
                v::key('front', v::stringType()->notEmpty(), false),
                v::key('back', v::stringType()->notEmpty(), false)
            ), false),
            v::key('address', v::stringType()->notEmpty(), false),
            v::key('postcode', v::stringType()->notEmpty(), false)
        )->setName('info');
        $userValidator->check($userInfo);
        return $next($request, $response);
    });
});