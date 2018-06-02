<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/2
 * Time: 下午10:58
 */

namespace App\Models;

/**
 * 完整图书模型, 显示全部属性, 包括"相关图书"和"包含这本书的书单"
 * WholeBook的自定义属性"相关图书"中使用了SimpleBook, 因此WholeBook与
 * SimpleBook不能是继承关系, 否则在取得自定义属性的值时会陷入死循环
 */
class WholeBook extends Book
{
    protected $appends = ['total_score', 'tags', 'review_num', 'total_num', 'available_num', 'relevant_books', 'relevant_booklists'];

    /**
     * 获取相关图书
     */
    public function getRelevantBooksAttribute()
    {
        return SimpleBook::where('class_num', 'like', "{$this->class_num}%")
            ->where('id', '!=', $this->id)
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }

    /**
     * 获取包含这本书的书单
     */
    public function getRelevantBooklistsAttribute()
    {
        return SimpleBooklist::whereHas('books', function ($query) {
                $query->where('id', $this->id);
            })
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }
}