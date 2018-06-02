<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/29
 * Time: 下午10:29
 */

namespace App\Validators\Rules;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Rules\AbstractRule;

/**
 * 自定义手机号校验规则
 */
class ChinaPhone extends AbstractRule
{
    public function validate($input)
    {
        return preg_match("/^1[34578][0-9]{9}$/", $input);
    }
}