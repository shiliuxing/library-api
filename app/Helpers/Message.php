<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/29
 * Time: 下午8:36
 */

namespace App\Helpers;
use App\Exceptions\InternalErrorException;


/**
 * 发送短信
 */
class Message
{
    /**
     * 发送验证码
     * @param $phone string 手机号
     * @param $code string 验证码
     * @return bool
     * @throws InternalErrorException
     */
    public static function code($phone, $code)
    {
        $project = "huLPb4";
        $vars = json_encode(['code' => $code]);
        return self::send($project, $phone, $vars);
    }

    /**
     * 取书提醒
     * @param $phone string 手机号
     * @param $title string 图书标题
     * @param $library_name string 图书馆名称
     * @param $library_address string 图书馆地址
     * @return bool
     * @throws InternalErrorException
     */
    public static function takeBookNotice($phone, $title, $library_name, $library_address)
    {
        $project = "P2pSc";
        $vars = json_encode([
            'title'            => $title,
            'library_name'     => $library_name,
            'library_location' => $library_address
        ]);
        return self::send($project, $phone, $vars);
    }

    /**
     * 还书提醒
     * @param $phone string 手机号
     * @param $title string 图书标题
     * @return bool
     * @throws InternalErrorException
     */
    public static function returnBookNotice($phone, $title)
    {
        $project = "3KFQG";
        $vars = json_encode(['title' => $title]);
        return self::send($project, $phone, $vars);
    }

    /**
     * 逾期还书提醒
     * @param $phone string 手机号
     * @param $title string 图书标题
     * @return bool
     * @throws InternalErrorException
     */
    public static function returnOverdueBookNotice($phone, $title)
    {
        $project = "PbXZI";
        $vars = json_encode(['title' => $title]);
        return self::send($project, $phone, $vars);
    }

    /**
     * 调用接口，发送信息
     * @param $project
     * @param $phone
     * @param $vars
     * @throws InternalErrorException
     * @return bool
     */
    private static function send($project, $phone, $vars)
    {
        $url = "https://api.mysubmail.com/message/xsend.json";
        $params = [
            "appid"     => "14752",
            "signature" => "10ca61ae1032d2ef407c30528744813e",
            "to"        => $phone,
            "project"   => $project,
            "vars"      => $vars
        ];

        $res = json_decode(self::post($url, $params), true);

        if ($res['status'] != 'success'){
            throw new InternalErrorException(SEND_VERIFICATION_CODE_FAILED, $res['msg']);
        }

        return true;
    }

    /**
     * post请求
     * @param $url
     * @param $params
     * @return mixed
     */
    private static function post($url, $params)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);//POST数据
        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
        return $output;
    }
}