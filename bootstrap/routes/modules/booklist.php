<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/3
 * Time: 下午3:10
 */

use App\Middlewares\Auth;
use App\Middlewares\validateStartAndCount;
use Respect\Validation\Validator as v;

$app->post('/api/booklists', PREFIX . 'BooklistController:createBooklist')->add(function ($request, $response, $next) {
    $booklistInfo = $request->getParsedBody();
    v::arrayType()->keySet(
        v::key('wechat_user_id', v::intVal()),
        v::key('title', v::stringType()->length(1, 30)),
        v::key('description', v::stringType()->length(0, 300), false)
    )->setName('info')->check($booklistInfo);
    return $next($request, $response);
})->add(Auth::wechatType());

$app->group('/api/booklists/{id:[0-9]+}', function () {
    $this->get('', PREFIX . 'BooklistController:getBooklistById');
    $this->post('', PREFIX . 'BooklistController:updateBooklistById')->add(function ($request,
                                                                                     $response,
                                                                                     $next) {
        $booklistInfo = $request->getParsedBody();
        v::arrayType()->keySet(
            v::key('title', v::stringType()->length(1, 30), false),
            v::key('description', v::stringType()->length(0, 300), false),
            v::key('add_items', v::arrayType()
                ->each(v::arrayVal()
                    ->setName('item of add_items')
                    ->keySet(
                        v::key('book_id', v::intVal()),
                        v::key('comment', v::stringType()->length(null, 200), false)
                    )), false),
            v::key('delete_items', v::arrayVal()->each(v::intVal()), false)
        )->setName('info')->check($booklistInfo);
        return $next($request, $response);
    });
    $this->get('/books', PREFIX . 'BooklistController:getBooksByBooklistId')->add(validateStartAndCount::class);
    $this->delete('', PREFIX . 'BooklistController:deleteBooklistById')->add(Auth::wechatType());
    $this->post('/favorite', PREFIX . 'BooklistController:favoriteBooklistById')->add(Auth::wechatType());
});

$app->get('/api/booklists/search', PREFIX . 'BooklistController:getBooklistsByKeyword')->add(function ($request,
                                                                                                       $response,
                                                                                                       $next) {
    v::arrayType()->key('keyword', v::notEmpty())->check($request->getQueryParams());
    return $next($request, $response);
})->add(validateStartAndCount::class);


$app->get('/api/booklists/users/{id:[0-9]+}', PREFIX . 'BooklistController:getBooklistsByUserId');

$app->get('/api/booklists/recommend/{user_id:[0-9]+}', PREFIX . 'BooklistController:getRecommendedBooklistsByUserId');