<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/29
 * Time: 下午3:15
 */

namespace App\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\InternalErrorException;
use App\Helpers\Message;
use App\Models\Code;
use App\Models\WechatUser;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CodeController
{
    /**发送验证码, 同一个手机号一分钟只能发送一次
     * @param Request $request
     * @param Response $response
     * @throws BadRequestException
     * @throws InternalErrorException 发送验证码失败
     */
    public function send(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $phone = $params['phone'];
        $type = $params['type'];

        $codeModel = Code::where([
            ['phone', $phone],
            ['type', $type]
        ])->first();

        // 一分钟只能发送一条短信
        if ($codeModel && $codeModel->updated_at->getTimestamp() + 60 > time()) {
            throw new BadRequestException(SEND_VERIFICATION_TOO_FREQUENTLY);
        }

        // 验证码没过期时还是发送这个验证码
        if ($codeModel->updated_at->getTimestamp() + $codeModel->expiry > time()) {
            Message::code($phone, $codeModel->code);
        } else {
            // 发送短信并保存记录
            $code = rand(100000, 999999);
            Message::code($phone, $code);
            Code::updateOrCreate(['phone' => $phone], ['code' => $code, 'type' => $type, 'expiry' => 300]);
        }
    }

    /**
     * 检查验证码并自动注册微信用户
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws BadRequestException
     */
    public function check(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $phone = $params['phone'];
        $type = $params['type'];
        $code = $params['code'];

        $codeModel = Code::where([
            ['phone', $phone],
            ['type', $type]
        ])->first();

        // 验证码错误
        if (!$codeModel || $codeModel->code != $code) {
            throw new BadRequestException(WRONG_VERIFICATION_CODE);
        }
        // 验证码过期
        if ($codeModel->updated_at->getTimestamp() + $codeModel->expiry < time()) {
            throw new BadRequestException(VERIFICATION_CODE_OVERDUE);
        }
        // 验证码正确, 如果没创建用户, 则创建并返回200
        if ($type == 'wechat' && !WechatUser::getUserByPhone($phone)) {
            $user = WechatUser::createUser(['phone' => $phone]);
            return $response->withJson([
                'user' => $user,
                'token' => $user->getToken()
            ], 201);
        }
        // 默认 200
        $user = WechatUser::getUserByPhone($phone);
        return $response->withJson([
            'user' => $user,
            'token' => $user->getToken()
        ]);
    }
}