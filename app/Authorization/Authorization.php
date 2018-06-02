<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/3
 * Time: 下午3:44
 */

namespace App\Authorization;

use App\Exceptions\AuthorizationException;
use App\Exceptions\ForbiddenException;
use Firebase\JWT\JWT;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Authorization
{
    /**
     * 加密key
     */
    const KEY = 'love_r';

    public static function getWechatTokenById($uid, $otherPayload = []) {
        return self::encode('wechat', $uid, $otherPayload);
    }

    public static function getLibraryTokenById($uid, $otherPayload = []) {
        return self::encode('library', $uid, $otherPayload);
    }

    public static function getWikiTokenById($uid, $otherPayload = []) {
        return self::encode('wiki', $uid, $otherPayload);
    }

    public static function getAdminTokenById($uid, $otherPayload = []) {
        return self::encode('admin', $uid, $otherPayload);
    }

    /**
     * 生成token
     * @param $userType string 用户类型
     *      wechat: 微信小程序用户
     *      library: 图书馆管理员
     *      wiki: 图书wiki系统用户
     *      admin: 超级管理员
     * @param $uid integer 用户id
     * @param $otherPayload array 其他数据
     * @return string
     */
    public static function encode($userType, $uid, $otherPayload = [])
    {
        // TODO token过期: 设置exp字段
        return JWT::encode(array_merge([
            'user_type' => $userType,
            'user_id' => $uid
        ], $otherPayload), self::KEY);
    }

    /**
     * 解码, token不合法时返回403错误码
     * @param $jwt string
     * @throws \Exception token不合法
     * @return array payload
     */
    public static function decode($jwt)
    {
        return (array)JWT::decode($jwt, self::KEY, ['HS256']);
    }

    /**
     * 获取token的用户id
     * @param $request Request
     * @throws ForbiddenException token不合法
     * @return integer
     */
    public static function getUserIdFromRequest($request)
    {
        try {
            $token = $request->getHeader('TOKEN')[0];
            $payload = Authorization::decode($token);
            return $payload['user_id'];
        } catch (\Exception $e) {
            throw AuthorizationException::invalidToken();
        }
    }
}