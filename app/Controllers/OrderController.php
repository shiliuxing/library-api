<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/7
 * Time: 上午10:54
 */

namespace App\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Models\Book;
use App\Models\Library;
use App\Models\Order;
use App\Models\WechatUser;
use Carbon\Carbon;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


class OrderController
{
    /**
     * 创建订单, 可以一次性创建多个订单
     * @param Request $request
     * @param Response $response
     * @throws BadRequestException
     * @throws NotFoundException
     * @return string
     */
    public function createOrders(Request $request, Response $response)
    {
        $info = $request->getParsedBody();
        $res = [];
        foreach ($info as $order) {
            if (!Book::where('isbn', $order['isbn'])->first()) {
                throw new NotFoundException(BOOK_NOT_FOUND);
            }
            if (!WechatUser::find($order['wechat_user_id'])) {
                throw new NotFoundException(USER_NOT_FOUND);
            }
            if (!Library::find($order['library_id'])) {
                throw new NotFoundException(LIBRARY_NOT_FOUND);
            }
            if (Order::where([
                'isbn'           => $order['isbn'],
                'wechat_user_id' => $order['wechat_user_id'],
                'library_id'     => $order['library_id']
            ])->where('status', '<', Order::STATUS_NORMAL_CLOSE)->first()
            ) {
                throw new BadRequestException(ORDER_EXIST);
            }
            $res[] = Order::create($order);
        }
        return $response->withJson($res);
    }

    public function getOrderById(Request $request, Response $response, $args)
    {
        return $response->withJson(Order::findOrFail($args['id']));
    }

    // 取书
    public function takeBookByOrderId(Request $request, Response $response, $args)
    {
        $order = Order::findOrFail($args['id']);
        if ($order->status == Order::STATUS_WAITING_FOR_OTHERS_TO_RETURN) {
            throw new BadRequestException(ORDER_CANNOT_TAKE_UNRETURN);
        }
        if ($order->status != Order::STATUS_WAITING_TO_TAKE_AT_PLANED_TIME && $order->status != Order::STATUS_WAITING_TO_TAKE_RETURNED_BOOK) {
            throw new BadRequestException(ORDER_HAS_TAKEN);
        }
        $order->update([
            'status'             => Order::STATUS_BORROWING,
            'actual_take_time'   => Carbon::now(),
            'should_return_time' => Carbon::now()->addMonth() // 一个月后归还
        ]);
        return $response->withJson($order);
    }

    // 续借图书, 最多续借一次, 增加一个月借阅时间
    public function renewBookByOrderId(Request $request, Response $response, $args)
    {
        $order = Order::findOrFail($args['id']);
        if ($order->status !== Order::STATUS_BORROWING) {
            throw new BadRequestException(CANNOT_RENEW_UNBORROWING);
        }
        if ($order->renew_count > 0) {
            throw new BadRequestException(CANNOT_RENEW_HAS_RENEWED);
        }
        $order->update([
            'should_return_time' => $order->should_return_time->addMonth(),
            'renew_count'        => 1
        ]);
        return $response->withJson($order);
    }

    // 还书
    public function returnBook(Request $request, Response $response, $args)
    {
        $info = $request->getParsedBody()['info'];
        $order = Order::where([
            'isbn'           => $info['isbn'],
            'wechat_user_id' => $info['wechat_user_id'],
            'library_id'     => $info['library_id']
        ])->where('status', '<', Order::STATUS_NORMAL_CLOSE)->first();
        if (!$order) {
            throw new NotFoundException(ORDER_NOT_EXIST);
        }
        $order->update([
            'status'             => Order::STATUS_NORMAL_CLOSE,
            'actual_return_time' => Carbon::now()
        ]);
        return $response->withJson($order);
    }

    // 取消订单
    public function cancelOrderById(Request $request, Response $response, $args)
    {
        $order = Order::findOrFail($args['id']);
        if ($order->status >= Order::STATUS_BORROWING) {
            throw new BadRequestException(ORDER_CANNOT_CANCEL);
        }
        $order->status = Order::STATUS_CANCELED_BY_USER;
        $order->save();
        return $response->withJson($order);
    }

    // 删除订单
    public function deleteOrderById(Request $request, Response $response, $args)
    {
        $order = Order::findOrFail($args['id']);
        $order->delete();
    }

    // 获取一个用户的订单
    public function getOrdersByUserId(Request $request, Response $response, $args)
    {
        $user = WechatUser::find($args['id']);
        if(!$user){
            throw new NotFoundException(USER_NOT_FOUND);
        }

        $params = $request->getQueryParams();
        $query = $user->orders()->latest()->offset($params['start'])->limit($params['count']);
        if($params['type']) {
            $query = $query->type($params['type']);
        }

        $res = [
            'orders' => $query->get(),
            'start' => (int)$params['start'],
            'count' => (int)$params['count'],
            'total' => $query->count()
        ];
        return $response->withJson($res);
    }
}