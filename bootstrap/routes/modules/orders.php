<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/7
 * Time: 上午10:37
 */


use App\Models\Order;
use Respect\Validation\Validator as v;
use App\Middlewares\validateStartAndCount;

$app->post('/api/orders', PREFIX . 'OrderController:createOrders')->add(function ($request, $response, $next) {
    $ordersInfo = $request->getParsedBody();
    v::arrayVal()->each(v::arrayType()->keySet(
        v::key('wechat_user_id', v::intVal()),
        v::key('library_id', v::intVal()),
        v::key('status', v::in([Order::STATUS_WAITING_FOR_OTHERS_TO_RETURN, Order::STATUS_WAITING_TO_TAKE_AT_PLANED_TIME])),
        v::key('isbn', v::stringType()->notEmpty()),
        v::key('should_take_time', v::date('Y-m-d')->min('today'), false)
    )->setName('order_item'))->setName('orders')->check($ordersInfo);
    return $next($request, $response);
});

$app->group('/api/orders/{id:[0-9]+}', function () {
    $this->get('', PREFIX . 'OrderController:getOrderById');
    $this->delete('', PREFIX . 'OrderController:deleteOrderById');
    $this->post('/take', PREFIX . 'OrderController:takeBookByOrderId');
    $this->post('/renew', PREFIX . 'OrderController:renewBookByOrderId');
    $this->post('/cancel', PREFIX . 'OrderController:cancelOrderById');
});

$app->post('/api/orders/return', PREFIX . 'OrderController:returnBook')->add(function ($request, $response, $next) {
    $orderInfo = $request->getParsedBody();
    v::arrayVal()->keySet(
        v::key('wechat_user_id', v::intVal()),
        v::key('library_id', v::intVal()),
        v::key('isbn', v::stringType()->notEmpty())
    )->setName('info')->check($orderInfo);
    return $next($request, $response);
});

$app->get('/api/orders/users/{id:[0-9]+}', PREFIX . 'OrderController:getOrdersByUserId')->add(function ($request,
                                                                                                        $response,
                                                                                                        $next) {
    $query = $request->getQueryParams();
    v::arrayVal()->key('type', v::in(['ongoing', 'booking', 'borrowing', 'history']), false)->check($query);
    return $next($request, $response);
})->add(validateStartAndCount::class);