<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/14
 * Time: 下午8:49
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $hidden = ['library_id', 'book_id'];
    protected $appends = ['book', 'library'];
    protected $casts = [
        'is_available' => 'boolean'
    ];
    protected $guarded = [];

    /**
     * 图书关联
     */
    public function book()
    {
        return $this->belongsTo('App\Models\SimpleBook', 'book_id');
    }

    /**
     * 图书馆管理
     */
    public function library()
    {
        return $this->belongsTo('App\Models\Library', 'library_id');
    }

    /**
     * 图书信息
     */
    public function getBookAttribute(){
        return $this->book()->first();
    }

    /**
     * 图书馆信息
     */
    public function getLibraryAttribute(){
        return $this->library()->first();
    }
}