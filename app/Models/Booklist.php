<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/3
 * Time: 下午3:10
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Booklist extends Model
{
    /**
     * 用户是否创建或收藏此书单
     */
    const STATUS_NO_RELATION = 0; // 没有任何关系
    const STATUS_IS_CREATOR = 1; // 创建者
    const STATUS_IS_FAVORITE = 2; // 收藏者

    protected $table = 'booklists';
    protected $guarded = [];
    protected $appends = ['creator', 'image', 'total', 'favorited_num', 'items'];
    protected $hidden = ['wechat_user_id'];

    /**
     * 书单内的图书关联
     */
    public function books()
    {
        return $this->belongsToMany('App\Models\SimpleBook', 'book_booklist', 'booklist_id', 'book_id')->withPivot('comment');
    }

    /**
     * 创建者关联
     */
    public function creator()
    {
        return $this->belongsTo('App\Models\SimpleWechatUser', 'wechat_user_id', 'id');
    }

    /**
     * 收藏者关联
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\WechatUser', 'booklist_wechat_user', 'booklist_id', 'wechat_user_id');
    }

    /**
     * 创建者信息
     */

    public function getCreatorAttribute()
    {
        return $this->creator()->first();
    }

    /**
     * 书单封面: 书单第一本图书的封面
     */
    public function getImageAttribute()
    {
        return $this->books()
            ->orderBy('book_booklist.created_at', 'desc')
            ->first()
            ->imgs['small'];
    }

    /**
     * 书单内图书总数
     */
    public function getTotalAttribute()
    {
        return $this->books()->count();
    }

    /**
     * 书单收藏者数量
     */
    public function getFavoritedNumAttribute()
    {
        return $this->users()->count();
    }

    /**
     * 书单内前20本书
     */
    public function getItemsAttribute()
    {
        return $this->getItems(0);
    }

    /**
     * 获取书单内的图书，默认获取前20本书
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getItems($offset = 0, $limit = 20)
    {
        $books = $this->books()
            ->orderBy('book_booklist.created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        return $books->map(function($book) {
            return [
                'book' => $book,
                'comment' => $book->pivot->comment
            ];
        });
    }

    /**
     * 根据用户id(一般在token中)设置此书单对象的status属性.
     * 当用户是此书单的创建者且在"书单-用户关系表"中有记录时, 该用户是创建者, 可以编辑书单;
     * 当用户不是创建者但在"书单-用户关系表"中有记录时, 该用户是收藏者, 可以取消收藏;
     * 当用户在"书单-用户关系表"中无记录时, 该用户与此书单没有任何关系, 仅能收藏, 收藏后才能编辑.
     * @param $id integer 用户id
     * @return Booklist
     */
    public function setStatusAttributeByUserId($id)
    {
        if ($id == $this->wechat_user_id && $this->users()->find($id)) {
            $this->status = self::STATUS_IS_CREATOR;
        } elseif ($this->users()->find($id)) {
            $this->status = self::STATUS_IS_FAVORITE;
        } else {
            $this->status = self::STATUS_NO_RELATION;
        }
        return $this;
    }

    public function setStatusAttributeAsDefault(){
        $this->status = self::STATUS_NO_RELATION;
        return $this;
    }
}