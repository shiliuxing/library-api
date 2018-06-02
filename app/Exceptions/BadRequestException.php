<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/29
 * Time: 下午3:42
 */

namespace App\Exceptions;


/**
 * 400 错误对象
 */
class BadRequestException extends BaseException
{
    public function __construct($message, $errMsg = 'Bad Request', $code = 400)
    {
        parent::__construct($message, $errMsg, $code);
    }
}