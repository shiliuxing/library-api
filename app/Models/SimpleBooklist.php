<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/5
 * Time: 下午3:20
 */

namespace App\Models;

/**
 * 简版书单模型, 只显示部分属性
 */
class SimpleBooklist extends Booklist
{
    protected $visible = ['id', 'title', 'creator', 'image', 'total', 'favorited_num', 'status'];
}