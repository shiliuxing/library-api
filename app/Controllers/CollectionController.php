<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/14
 * Time: 下午9:22
 */

namespace App\Controllers;

use App\Exceptions\NotFoundException;
use App\Models\Book;
use App\Models\Collection;
use App\Models\Library;
use App\Models\SimpleBook;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class CollectionController
{
    public function getCollectionsByBookISBN(Request $request, Response $response, $args)
    {
        $book = Book::where('isbn', $args['isbn'])->first();
        if (!$book) {
            throw new NotFoundException(BOOK_NOT_FOUND);
        }
        return $this->getCollectionsByBook($book, $request, $response);
    }

    public function getCollectionsByBookId(Request $request, Response $response, $args)
    {
        $book = Book::find($args['id']);
        if (!$book) {
            throw new NotFoundException(BOOK_NOT_FOUND);
        }
        return $this->getCollectionsByBook($book, $request, $response);
    }

    public function getCollectionsByLibraryId(Request $request, Response $response, $args)
    {
        $library = Library::find($args['id']);
        if (!$library) {
            throw new NotFoundException(LIBRARY_NOT_FOUND);
        }

        // 添加查询条件
        $params = $request->getQueryParams();
        $query = $library->collections()->offset($params['start'])->limit($params['count']);
        if ($params['book_id']) {
            $query = $query->whereHas('book', function ($query) use ($params) {
                $query->where('id', $params['book_id']);
            });
        }
        if ($params['isbn']) {
            $query = $query->whereHas('book', function ($query) use ($params) {
                $query->where('isbn', $params['isbn']);
            });
        }

        $res = [
            'collections' => $query->get(),
            'start'       => (int)$params['start'],
            'count'       => (int)$params['count'],
            'total'       => $query->count()
        ];
        return $response->withJson($res);
    }

    /**
     * 根据图书馆id和isbn设置一条馆藏信息，馆藏信息不存在时自动创建
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     * @throws NotFoundException
     */
    public function setCollectionByLibraryId(Request $request, Response $response, $args)
    {
        $info = $request->getParsedBody()['info'];
        $book = Book::where('isbn', $info['isbn'])->first();
        if (!$book) {
            throw new NotFoundException(BOOK_NOT_FOUND);
        }

        $collection = Collection::updateOrCreate([
            'library_id' => $args['id'],
            'book_id'    => $book->id
        ], [
            'available_num' => $info['available_num'],
            'total_num'     => $info['total_num']
        ]);
        return $response->withJson($collection);
    }

    /**
     * 根据图书馆id和isbn入库，入库数量将加到现有可借数与总数上
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     * @throws NotFoundException
     */
    public function stockInByLibraryId(Request $request, Response $response, $args)
    {
        $info = $request->getParsedBody()['info'];
        $book = Book::where('isbn', $info['isbn'])->first();
        if (!$book) {
            throw new NotFoundException(BOOK_NOT_FOUND);
        }


        $collection = Collection::firstOrCreate([
            'library_id' => $args['id'],
            'book_id'    => $book->id
        ]);
        $collection->update([
            'available_num' => $collection->available_num + $info['in_num'],
            'total_num'     => $collection->total_num + $info['in_num']
        ]);
        return $response->withJson($collection);
    }

    /**
     * 获取某本图书的馆藏列表, 默认返回该图书馆藏数"不为0"的图书馆.
     * 根据图书馆名称查找特定图书馆内该图书的馆藏信息时, 也返回该图书馆藏数"为0"的图书馆.
     * @param $book Book 图书对象
     * @param $request
     * @param $response
     * @return string
     */
    private function getCollectionsByBook($book, Request $request, Response $response)
    {
        // 添加查询条件
        $params = $request->getQueryParams();
        $query = $book->collections()->offset($params['start'])->limit($params['count']);
        if ($params['library_name']) {
            $query = $query->whereHas('library', function ($query) use ($params) {
                $query->where('name', 'like', '%' . implode('%', preg_split('/\s+/', $params['library_name'])) . '%');
            });
        }
        if ($params['library_id']) {
            $query = $query->whereHas('library', function ($query) use ($params) {
                $query->where('id', $params['library_id']);
            });
        }

        $res = [
            'collections' => $query->get(),
            'start'       => (int)$params['start'],
            'count'       => (int)$params['count'],
            'total'       => $query->count()
        ];
        return $response->withJson($res);
    }
}