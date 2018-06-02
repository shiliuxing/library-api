<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/29
 * Time: 下午3:42
 */

namespace App\Exceptions;


/**
 * 404 错误对象
 */
class NotFoundException extends BaseException
{
    public function __construct($message, $errMsg = 'Not Found', $code = 404)
    {
        parent::__construct($message, $errMsg, $code);
    }
}