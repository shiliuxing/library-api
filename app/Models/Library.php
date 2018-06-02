<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/5
 * Time: 下午7:49
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * 完整图书信息, 仅用于"getLibraryById"
 */
class Library extends Model
{
    protected $table = 'libraries';
    protected $hidden = ['admin_password'];
    protected $casts = [
        'photos'         => 'array',
        'qualifications' => 'array'
    ];
    protected $appends = ['book_type_num', 'book_total_num'];

    /**
     * 馆藏关联
     */
    public function collections(){
        return $this->hasMany('App\Models\Collection', 'library_id', 'id');
    }

    // TODO 图书种类数, 借阅历史等
    public function getBookTypeNumAttribute(){
        return $this->collections()->count();
    }

    public function getBookTotalNumAttribute(){
        return $this->collections()->sum('total_num');
    }
}