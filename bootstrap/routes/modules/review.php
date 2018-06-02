<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/5
 * Time: 下午7:59
 */

use App\Middlewares\Auth;
use App\Middlewares\validateStartAndCount;
use Respect\Validation\Validator as v;

$app->group('/api/books/{id:[0-9]+}/reviews', function ()  {
    $this->get('', PREFIX . 'ReviewController:getReviewsByBookId')->add(validateStartAndCount::class);
    $this->post('', PREFIX . 'ReviewController:addReviewByBookId')->add(function ($request, $response, $next) {
        $reviewInfo = $request->getParsedBody();
        v::arrayType()->keySet(
            v::key('wechat_user_id', v::intVal()),
            v::key('score', v::intVal()->between(1, 10)),
            v::key('content', v::stringType()->notEmpty()->length(1, 200))
        )->setName('info')->check($reviewInfo);
        return $next($request, $response);
    })->add(Auth::wechatType());
});


$app->delete('/api/reviews/{id:[0-9]+}', PREFIX . 'ReviewController:deleteReviewById');

$app->get('/api/users/{id:[0-9]+}/reviews', PREFIX . 'ReviewController:getReviewsByUserId')->add(validateStartAndCount::class);