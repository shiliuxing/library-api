<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/29
 * Time: 下午3:54
 */

namespace App\Exceptions;


/**
 * 500 错误对象
 */
class InternalErrorException extends BaseException
{
    public function __construct($message, $errMsg = 'Internal Error', $code = 500)
    {
        parent::__construct($message, $errMsg, $code);
    }
}