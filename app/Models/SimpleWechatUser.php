<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/4
 * Time: 下午9:59
 */

namespace App\Models;


/**
 * 用于书单等对象的小程序用户简版信息
 */
class SimpleWechatUser extends WechatUser
{
    protected $visible = ['id', 'nickname', 'avatar'];
}