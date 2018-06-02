<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/2
 * Time: 下午3:44
 */

use Respect\Validation\Validator as v;
use App\Middlewares\validateStartAndCount;

// 获取一本图书
$app->group('/api/books', function () {
    $this->get('/{id:[0-9]+}', PREFIX . 'BookController:getBookById');
    $this->get('/isbn/{isbn:[0-9]+}', PREFIX . 'BookController:getBookByISBN');
});

// 获取多本图书
$app->group('/api/books', function () {
    $this->get('/search', PREFIX . 'BookController:getBooksByTitle')->add(function ($request, $response, $next) {
        /**
         * 关键字不能为空
         */
        v::arrayType()->key('keyword', v::notEmpty())->check($request->getQueryParams());
        return $next($request, $response);
    });
    $this->get('/authors/{author}', PREFIX . 'BookController:getBooksByAuthor');
    $this->get('/tags/{tag}', PREFIX . 'BookController:getBooksByTag');
    $this->get('/classifications/{class}', PREFIX . 'BookController:getBooksByClassificationNumber');
    $this->get('/search/advanced', PREFIX . 'BookController:getBooksByAdvancedSearch')->add(function ($request,
                                                                                                      $response,
                                                                                                      $next) {
        /**
         * 校验高级搜索条件
         */
        $params = $request->getQueryParams();
        $validator = v::arrayVal()
            ->key('title', v::notEmpty(), false)
            ->key('author', v::notEmpty(), false)
            ->key('translator', v::notEmpty(), false)
            ->key('publisher', v::notEmpty(), false)
            ->key('pubdate_start', v::date('Y-m-d'), false)
            ->key('pubdate_end', v::date('Y-m-d'), false)
            ->key('language', v::notEmpty(), false)
            ->setName('params');
        $validator->check($params);
        return $next($request, $response);
    });

    $this->get('/recommend/{user_id:[0-9]+}', PREFIX . 'BookController:getRecommendedBooksByUserId');
    $this->get('/ranking', PREFIX . 'BookController:getRankingBooks');
})->add(function ($request, $response, $next) {
    /**
     * 校验图书排序规则
     */
    $query = $request->getQueryParams();
    v::arrayType()
        ->key('sort', v::in(["comprehensive", "pubdateAsc", "pubdateDesc", "totalScoreDesc"]), false)
        ->check($query);
    return $next($request, $response);
})->add(validateStartAndCount::class); // 校验start和count