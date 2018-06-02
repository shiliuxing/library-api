<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/5
 * Time: 下午8:13
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    /**
     * 引入软删除
     */
    use SoftDeletes;

    protected $guarded = [];
    protected $hidden = ['deleted_at', 'wechat_user_id', 'book_id'];
    protected $appends = ['user', 'book'];

    /**
     * 创建者关联
     */
    public function creator()
    {
        return $this->belongsTo('App\Models\SimpleWechatUser', 'wechat_user_id', 'id');
    }

    /**
     * 所属图书关联
     */
    public function book()
    {
        return $this->belongsTo('App\Models\SimpleBook', 'book_id', 'id');
    }

    /**
     * 创建者信息
     */
    public function getUserAttribute()
    {
        return $this->creator()->first();
    }

    /**
     * 图书信息
     */
    public function getBookAttribute()
    {
        return $this->book()->first();
    }

    /**
     * 根据用户id(一般在token中)设置此评论对象的is_creator属性.
     * @param $id integer 用户id
     */
    public function setIsCreatorByUserId($id)
    {
        if ($id == $this->wechat_user_id) {
            $this->is_creator = true;
        } else {
            $this->is_creator = false;
        }
        return $this;
    }
}