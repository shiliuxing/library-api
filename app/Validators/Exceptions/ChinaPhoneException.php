<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/30
 * Time: 下午1:45
 */

namespace App\Validators\Exceptions;


use Respect\Validation\Exceptions\ValidationException;

class ChinaPhoneException extends ValidationException
{
    protected $template = "{{name}} 必须是一个11位中国大陆手机号";
}