<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/29
 * Time: 上午10:21
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 验证码
 */
class Code extends Model
{
    protected $fillable = ['phone', 'type', 'code'];
}