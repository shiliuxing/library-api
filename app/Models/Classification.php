<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/3
 * Time: 上午9:51
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * 中图法分类号
 */
class Classification extends Model
{
    protected $visible = ['number', 'parent_number', 'name', 'has_son'];
    protected $appends = ['has_son'];

    public function scopeSonOf($query, $parent) {
        return $query->where('parent_number', $parent);
    }

    /**
     * 是否有子分类号
     */
    public function getHasSonAttribute(){
        return self::where('parent_number', $this->number)->exists();
    }
}