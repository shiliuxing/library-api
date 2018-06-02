<?php
/**
 * User: ZhuKaihao
 * Date: 2018/4/27
 * Time: 下午12:18
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 基本图书模型
 */
class Book extends Model
{
    protected $table = 'books';
    protected $guarded = [];
    protected $appends = ['total_score', 'tags', 'review_num', 'total_num', 'available_num'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'author'     => 'array',
        'translator' => 'array',
        'imgs'       => 'array'
    ];

    /**
     * 馆藏关联
     */
    public function collections(){
        return $this->hasMany('App\Models\Collection', 'book_id', 'id');
    }

    /**
     * 图书评论关联
     */
    public function reviews(){
        return $this->hasMany('App\Models\Review', 'book_id');
    }

    /**
     * 根据关键字近似搜索, 多个关键字之间可用空格相隔
     * @param $query
     * @param string $keyword 关键字字符串. eg: "追风", "马克思 语录"
     * @param string $column
     * @return mixed
     */
    public function scopeKeyword($query, $keyword, $column = 'title')
    {
        return $query->where($column, 'like', '%' . implode('%', preg_split('/\s+/', $keyword)) . '%');
    }

    /**
     * TODO 根据用户评分, 计算综合评分
     * @return float
     */
    public function getTotalScoreAttribute()
    {
        return rand(50, 99) / 10;
    }

    /**
     * 获取评论数目
     */
    public function getReviewNumAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * TODO 获取标签
     */
    public function getTagsAttribute()
    {
        return [];
    }

    /**
     * 获取图书总数
     */
    public function getTotalNumAttribute()
    {
        return $this->collections()->sum('total_num');
    }

    /**
     * 获取可借数目
     */
    public function getAvailableNumAttribute()
    {
        return $this->collections()->sum('available_num');
    }
}