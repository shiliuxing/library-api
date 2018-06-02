<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/14
 * Time: 下午9:24
 */

use Respect\Validation\Validator as v;
use App\Middlewares\validateStartAndCount;

$app->group('/api/books', function () {
    $this->get('/{id:[0-9]+}/collections', PREFIX . 'CollectionController:getCollectionsByBookId');
    $this->get('/isbn/{isbn:[0-9]+}/collections', PREFIX . 'CollectionController:getCollectionsByBookISBN');
})->add(function ($request, $response, $next) {
    $query = $request->getQueryParams();
    v::keySet(
        v::key('start', v::intVal(), false),
        v::key('count', v::intVal(), false),
        v::key('library_name', v::stringType()->notEmpty(), false),
        v::key('library_id',v::intVal(), false)
    )->check($query);
    return $next($request, $response);
})->add(validateStartAndCount::class);


$app->group('/api/libraries/{id:[0-9]+}/collections', function () {
    $this->get('', PREFIX . 'CollectionController:getCollectionsByLibraryId')->add(function ($request,
                                                                                             $response,
                                                                                             $next) {
        $query = $request->getQueryParams();
        v::keySet(
            v::key('start', v::intVal(), false),
            v::key('count', v::intVal(), false),
            v::key('book_id', v::intVal(), false),
            v::key('isbn', v::stringType(), false)
        )->check($query);
        return $next($request, $response);
    })->add(validateStartAndCount::class);

    $this->post('', PREFIX . 'CollectionController:setCollectionByLibraryId')->add(function ($request,
                                                                                             $response,
                                                                                             $next) {
        $collectionInfo = $request->getParsedBody();
        v::keySet(
            v::key('isbn', v::intVal()),
            v::key('total_num', v::intVal()->min(0)),
            v::key('available_num', v::intVal()->min(0))
        )->check($collectionInfo);
        return $next($request, $response);
    });


    $this->post('/stockin', PREFIX . 'CollectionController:stockInByLibraryId')->add(function ($request,
                                                                                             $response,
                                                                                             $next) {
        $collectionInfo = $request->getParsedBody();
        v::keySet(
            v::key('isbn', v::intVal()),
            v::key('in_num', v::intVal()->min(0))
        )->check($collectionInfo);
        return $next($request, $response);
    });
});
