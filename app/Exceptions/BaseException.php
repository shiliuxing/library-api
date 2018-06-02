<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/29
 * Time: 下午3:32
 */

namespace App\Exceptions;


use JsonSerializable;

/**
 * 错误对象基类, 所有自定义错误对象均继承此对象
 */
class BaseException extends \Exception implements JsonSerializable
{
    /**
     * @var string
     */
    protected $errMsg;

    /**
     * @param string $message 显示给用户的错误信息
     * @param string $errMsg 显示给开发者的错误信息
     * @param int $code 自定义错误码
     */
    public function __construct($message, $errMsg, $code)
    {
        parent::__construct($message, $code);
        $this->errMsg = $errMsg;
    }

    /**
     * 使此对象能够转为 JSON 字符串
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'err_msg' => $this->errMsg
        ];
    }

}