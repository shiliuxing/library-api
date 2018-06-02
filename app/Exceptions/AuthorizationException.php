<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/3
 * Time: 下午4:01
 */

namespace App\Exceptions;


/**
 * token不合法/权限不足
 */
class AuthorizationException
{
    /**
     * 未登录: token不合法/过期
     */
    public static function invalidToken()
    {
        return new ForbiddenException(NOT_LOGIN, INVALID_TOKEN);
    }

    /**
     * 无权访问: token合法, 但无权访问该接口, 如访问其他账号的数据
     */
    public static function denied()
    {
        return new ForbiddenException(NO_PERMISSION, '权限不足无权访问 403 forbidden');
    }

    /**
     * token类型不合法, 如使用小程序用户token访问管理员后台接口
     */
    public static function invalidTokenType($typesArray)
    {
        return new ForbiddenException(NO_PERMISSION, 'token用户类型错误. 允许的用户类型: ' . implode(', ', $typesArray));
    }
}