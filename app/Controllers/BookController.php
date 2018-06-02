<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/2
 * Time: 下午3:26
 */

namespace App\Controllers;

use App\Exceptions\InternalErrorException;
use App\Exceptions\NotFoundException;
use App\Models\Book;
use App\Models\Order;
use App\Models\SimpleBook;
use App\Models\WholeBook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;

class BookController
{
    public function getBookById(Request $request, Response $response, $args)
    {
        try {
            return $response->withJson(WholeBook::findOrFail($args['id']));
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundException(BOOK_NOT_FOUNT_FRIENDLY);
        }
    }

    public function getBookByISBN(Request $request, Response $response, $args)
    {
        try {
            return $response->withJson(WholeBook::where('isbn', $args['isbn'])->firstOrFail());
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundException(BOOK_NOT_FOUNT_FRIENDLY);
        }
    }

    /**
     * 根据用户id获取推荐图书
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return string
     */
    public function getRecommendedBooksByUserId(Request $request, Response $response, $args)
    {
        // TODO 推荐图书。目前全部推荐相同的10本热门图书
        $books = SimpleBook::where('id', '>=', 15001)->where('id', '<=', 15010)->inRandomOrder()->get();
        $res = [];
        foreach ($books as $book) {
            $res[] = [
                'book'    => $book,
                'comment' => '一本好书' // TODO 选择点赞最多的一条评论
            ];
        }
        return $response->withJson($res);
    }

    /**
     * 获取借阅图书排行
     * @param Request $request
     * @param Response $response
     * @return string
     */
    public function getRankingBooks(Request $request, Response $response)
    {
        $params = $request->getQueryParams();

        // 查询条件
        $query = Order::with('book')->withTrashed()->groupBy('isbn')->orderByRaw('count(isbn) DESC');

        // 根据订单表中每本书的借阅次数排序，获取图书信息
        $orders = $query->offset($params['start'])->limit($params['count'])->get();
        $books = $orders->map(function ($order) {
            return $order->book;
        });

        $res = [
            'books' => $books,
            'start' => (int)$params['start'],
            'count' => (int)$params['count'],
            'total' => $query->get()->count() // 一共有多少种图书被借阅
        ];

        return $response->withJson($res);
    }

    /**
     * 根据标题搜索图书
     * @param Request $request
     * @param Response $response
     * @return string
     */
    public function getBooksByTitle(Request $request, Response $response)
    {
        $keyword = $request->getQueryParams()['keyword'];
        return $this->getBooks(SimpleBook::keyword($keyword), $request, $response);
    }

    /**
     * 根据作者搜索图书
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return string
     */
    public function getBooksByAuthor(Request $request, Response $response, $args)
    {
        return $this->getBooks(SimpleBook::keyword($args['author'], 'author'), $request, $response);
    }

    /**
     * TODO 根据标签搜索图书
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return string
     */
    public function getBooksByTag(Request $request, Response $response, $args)
    {
        throw new InternalErrorException(UNSUPPORTED_SERVICE);
    }

    /**
     * 根据分类号搜索图书
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return string
     */
    public function getBooksByClassificationNumber(Request $request, Response $response, $args)
    {
        // "0"是所有一级分类号的父分类号，因此分类号为0时表示搜索所有分类下的图书
        if($args['class'] == '0') {
            $this->getBooks(SimpleBook::query(), $request, $response);
        }
        return $this->getBooks(SimpleBook::where('class_num', 'like', $args['class'].'%'), $request, $response);
    }

    /**
     * 高级搜索, 可选参数: 标题, 作者, 译者, 出版社, 出版时间
     * @param Request $request
     * @param Response $response
     * @return string
     */
    public function getBooksByAdvancedSearch(Request $request, Response $response, $args)
    {
        $params = $request->getQueryParams();
        $builder = Book::query();
        if (!empty($params['title'])) {
            $builder = $builder->keyword($params['title']);
        }
        if (!empty($params['author'])) {
            $builder = $builder->keyword($params['author'], 'author');
        }
        if (!empty($params['translator'])) {
            $builder = $builder->keyword($params['translator'], 'translator');
        }
        if (!empty($params['publisher'])) {
            $builder = $builder->keyword($params['publisher'], 'publisher');
        }
        if (!empty($params['pubdate_start'])) {
            $builder = $builder->whereDate('pubdate', '>=', $params['pubdate_start']);
        }
        if (!empty($params['pubdate_end'])) {
            $builder = $builder->whereDate('pubdate', '<=', $params['pubdate_end']);
        }
        if (!empty($params['language'])) {
            $builder = $builder->keyword($params['language'], 'language');
        }
        return $this->getBooks($builder, $request, $response);
    }

    /**
     * 根据查询条件获取图书列表, 并包裹成分页结果集形式
     * @param Builder $builder 查询条件
     * @param Request $request 请求对象
     *      $request = [
     *          'query.start' => (int) 偏移量, 默认 0
     *          'query.count' => (int) 结果数量, 默认 20
     *      ]
     * @return string $result 搜索结果
     *      $result = [
     *          'books' => SimpleBook[] 图书数组
     *          'start' => int 当前偏移量
     *          'count' => int 当前结果数
     *          'total' => int 结果总数
     *      ]
     */
    private function getBooks(Builder $builder, Request $request, Response $response)
    {
        $params = $request->getQueryParams();

        // 添加查询条件
        $newBuilder = $builder->offset($params['start'])->limit($params['count']);
        if ($params['sort']) {
            // TODO getSortBuilder() 排序方法
        }

        $res = [
            'books' => $newBuilder->get(),
            'start' => (int)$params['start'],
            'count' => (int)$params['count'],
            'total' => $builder->count()
        ];
        return $response->withJson($res);
    }
}