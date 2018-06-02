<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/29
 * Time: 上午10:21
 */

namespace App\Models;

use App\Authorization\Authorization;
use Illuminate\Database\Eloquent\Model;

class WechatUser extends Model
{
    protected $table = 'wechat_users';
    protected $guarded = [];
    protected $casts = [
        'id_card_img' => 'array'
    ];
    protected $appends = ['reading_statistics'];

    /**
     * 创建或收藏的所有书单的关联
     */
    public function booklists()
    {
        return $this->belongsToMany('App\Models\SimpleBooklist', 'booklist_wechat_user', 'wechat_user_id', 'booklist_id');
    }

    /**
     * 评论关联
     */
    public function reviews(){
        return $this->hasMany('App\Models\Review');
    }

    /**
     * 订单关联
     */
    public function orders(){
        return $this->hasMany('App\Models\Order');
    }

    /**
     * 阅读统计
     * @return array
     */
    public function getReadingStatisticsAttribute(){
        $orders = $this->orders()->with('book')->groupBy('isbn')->get();
        return [
            'book_num' => $orders->count(),
            'page_num' => $orders->sum("book.page")
        ];
    }


    public static function getUserByPhone($phone) {
        return self::where('phone', $phone)->first();
    }

    /**
     * 创建用户, 昵称为空时, 为用户设置一个随机昵称
     * @param $info array 用户信息
     * @return WechatUser
     */
    public static function createUser($info) {
        if(empty($info['nickname'])) {
            $info['nickname'] = uniqid('用户'); // 用户5ae5f1aec4761
        }
        return self::create($info);
    }

    public function getToken()
    {
        return Authorization::getWechatTokenById($this->id);
    }
}