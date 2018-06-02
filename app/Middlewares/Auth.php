<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/4
 * Time: 下午11:34
 */

namespace App\Middlewares;

use App\Authorization\Authorization;
use App\Exceptions\AuthorizationException;

/**
 * 提供各个静态方法, 返回中间件，用于校验token及其类型
 * eg：
 * $app->post('/', PREFIX . 'BooklistController:createBooklist')->add(Auth::wechatType());
 */
class Auth
{
    /**
     * 校验token是否合法，不校验token类型
     * @return Callable
     */
    public static function validate()
    {
        return function ($request, $response, $next) {
            $token = $request->getHeader('TOKEN')[0];
            try {
                Authorization::decode($token);
            } catch (\Exception $e) {
                throw AuthorizationException::invalidToken();
            }
            return $next($request, $response);
        };
    }

    /**
     * 检验微信小程序用户
     * @return Callable
     */
    public static function wechatType(){
        return self::multipleType(['wechat']);
    }

    /**
     * 检验图书馆用户
     * @return Callable
     */
    public static function libraryType()
    {
        return self::multipleType(['library']);
    }

    /**
     * 检验Wiki用户
     * @return Callable
     */
    public static function wikiType()
    {
        return self::multipleType(['wiki']);
    }

    /**
     * 检验Token对应的用户类型
     * @param $typesArray array 用户类型数组
     * @return Callable
     */
    public static function multipleType($typesArray)
    {
        return function ($request, $response, $next) use ($typesArray) {
            $token = $request->getHeader('TOKEN')[0];
            try {
                $payload = Authorization::decode($token);
            } catch (\Exception $e) {
                throw AuthorizationException::invalidToken();
            }
            if (!in_array($payload['user_type'], $typesArray)) {
                throw AuthorizationException::invalidTokenType($typesArray);
            }
            return $next($request, $response);
        };
    }
}