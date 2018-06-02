<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/7
 * Time: 下午2:41
 */

namespace App\Models;

/**
 * 简版图书馆模型, 用于除了"getLibraryById"之外的所有接口
 */
class SimpleLibrary extends Library
{
    // TODO book_type_num, book_total_num
    protected $visible = ['id', 'status', 'name', 'phone', 'address', 'introduction', 'photos', 'book_type_num', 'book_total_num'];
}