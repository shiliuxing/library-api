<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/2
 * Time: 下午8:12
 */

namespace App\Models;

/**
 * 简版图书模型, 只显示部分属性
 */
class SimpleBook extends Book
{
    protected $visible = [
        'id', 'title', 'isbn', 'publisher', 'pubdate', 'class_num',
        'call_number', 'author', 'translator', 'binding', 'price', 'page', 'word',
        'total_score', 'review_num', 'tags', 'imgs', 'total_num', 'available_num'
    ];
}