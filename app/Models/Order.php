<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/7
 * Time: 上午10:17
 */

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /* —————————— 1001 - 1010: order is in process 订单进行中 —————————— */

    /**
     * 1001: User is waiting for others to return the book and the
     * user will get a notification when someone returns the book.
     * 预约中: 图书无库存，用户预约此图书，正在等待其他用户归还.
     */
    const STATUS_WAITING_FOR_OTHERS_TO_RETURN = 1001;

    /**
     * 1002: others have returned. Wait for user to take the book.
     * 其他用户已经归还图书，正在等待用户取书.
     */
    const STATUS_WAITING_TO_TAKE_RETURNED_BOOK = 1002;

    /**
     * 1003: Wait for user to take the book at the planed time.
     * 预订中: 图书充足，用户预订此图书，将在约定的时间前往图书馆取书.
     */
    const STATUS_WAITING_TO_TAKE_AT_PLANED_TIME = 1003;

    /**
     * 1004: User have taken the book, not returned yet.
     * 借书借阅中.
     */
    const STATUS_BORROWING = 1004;


    /* —————————— 1011-1020: order has been closed 订单结束 —————————— */

    /**
     * 1011: Return book and close order normally.
     * 正常关闭.
     */
    const STATUS_NORMAL_CLOSE = 1011;

    /**
     * 1012: Return book and close order abnormally.
     * 非正常关闭, 需要支付罚金. 如图书损坏.
     */
    const STATUS_ABNORMAL_CLOSE = 1012;


    /* —————————— 1021-1030: order has been canceled 订单取消 —————————— */

    /**
     * 1021: User cancel the order. Or. Not take book at the point time.
     * 用户自己取消预约/预订订单. 或, 未在规定时间取书，系统自动取消订单.
     */
    const STATUS_CANCELED_BY_USER = 1021;


    use SoftDeletes;

    protected $guarded = [];
    protected $hidden = ['deleted_at', 'wechat_user_id', 'library_id', 'isbn'];
    protected $appends = ['is_overdue', 'status_text', 'user', 'library', 'book'];
    protected $dates = ['should_take_time', 'actual_take_time', 'should_return_time', 'actual_return_time'];
    protected $casts = [
        'should_take_time' => 'date:Y-m-d',
        'should_return_time' => 'date:Y-m-d'
    ];

    /**
     * 用户关联
     */
    public function wechatUser()
    {
        return $this->belongsTo('App\Models\SimpleWechatUser', 'wechat_user_id', 'id');
    }

    /**
     * 图书关联
     */
    public function book()
    {
        return $this->belongsTo('App\Models\SimpleBook', 'isbn', 'isbn');
    }

    /**
     * 图书馆关联
     */
    public function library()
    {
        return $this->belongsTo('App\Models\SimpleLibrary', 'library_id', 'id');
    }

    /**
     * 获取不同类型的订单
     * @param $query
     * @param string $type 订单类型. enum: ['ongoing', 'booking', 'borrowing', 'history']
     * @return mixed
     */
    public function scopeType($query, $type)
    {
        switch($type) {
            case 'ongoing': // 进行中
                return $query->where('status', '<=', self::STATUS_BORROWING);
            case 'booking': // 预约/预订
                return $query->where('status', '<', self::STATUS_BORROWING);
            case 'borrowing': // 借阅中
                return $query->where('status', self::STATUS_BORROWING);
            case 'history': // 已完成
                return $query->where('status', '>=', self::STATUS_NORMAL_CLOSE);
            default:
                return $query;
        }
    }

    /**
     * 用户信息
     */
    public function getUserAttribute()
    {
        return $this->wechatUser()->first();
    }

    /**
     * 图书信息
     */
    public function getBookAttribute()
    {
        return $this->book()->first();
    }

    /**
     * 图书馆信息
     */
    public function getLibraryAttribute()
    {
        return $this->library()->first();
    }

    /**
     * 是否逾期未归还
     */
    public function getIsOverdueAttribute()
    {
        return $this->status == self::STATUS_BORROWING && $this->should_return_time < Carbon::now();
    }

    /**
     * 订单状态说明文本
     */
    public function getStatusTextAttribute(){
        switch ($this->status) {
            case self::STATUS_WAITING_FOR_OTHERS_TO_RETURN:
                return '预约中，等待他人归还';
            case self::STATUS_WAITING_TO_TAKE_RETURNED_BOOK:
            case self::STATUS_WAITING_TO_TAKE_AT_PLANED_TIME:
                return '可取书';
            case self::STATUS_BORROWING:
                if ($this->is_overdue) {
                    return '已逾期';
              } else {
                    return '借阅中';
              }
            case self::STATUS_NORMAL_CLOSE:
            case self::STATUS_ABNORMAL_CLOSE:
                return '已归还';
            case self::STATUS_CANCELED_BY_USER:
                return '已取消';
            default:
                return '未知状态';
        }
    }
}