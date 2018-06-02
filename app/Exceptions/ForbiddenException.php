<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/6
 * Time: 下午9:49
 */

namespace App\Exceptions;

/**
 * 403 错误对象
 */
class ForbiddenException extends BaseException
{
    public function __construct($message, $errMsg = 'Forbidden', $code = 403)
    {
        parent::__construct($message, $errMsg, $code);
    }
}