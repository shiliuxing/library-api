<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/5
 * Time: 下午8:13
 */

namespace App\Controllers;


use App\Authorization\Authorization;
use App\Models\Book;
use App\Models\Review;
use App\Models\SimpleBook;
use App\Models\WechatUser;
use Illuminate\Database\Query\Builder;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ReviewController
{
    /**
     * 添加一条评论
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Review
     */
    public function addReviewByBookId(Request $request, Response $response, $args)
    {
        $info = $request->getParsedBody();
        $book = Book::findOrFail($args['id']);
        $review = new Review($info);
        $book->reviews()->save($review);
        return $response->withJson($this->getReviewWithIsCreatorAttribute($review, $request));
    }

    /**
     * 获取某本书的评论
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return array $result 搜索结果
     *      $result = [
     *          'book' => SimpleBook 图书信息
     *          'reviews' => Review[] 评论数组
     *          'start' => int 当前偏移量
     *          'count' => int 当前结果数
     *          'total' => int 该图书评论总数
     *      ]
     */
    public function getReviewsByBookId(Request $request, Response $response, $args)
    {
        $book = SimpleBook::findOrFail($args['id']);
        $res = $this->getReviews($request, $book->reviews());
        $res['book'] = $book;
        return $response->withJson($res);
    }

    /**
     * 获取某个用户的所有评论
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return array $result 搜索结果
     *      $result = [
     *          'reviews' => Review[] 评论数组
     *          'start' => int 当前偏移量
     *          'count' => int 当前结果数
     *          'total' => int 该图书评论总数
     *      ]
     */
    public function getReviewsByUserId(Request $request, Response $response, $args)
    {
        $user = WechatUser::findOrFail($args['id']);
        $res = $this->getReviews($request, $user->reviews());
        return $response->withJson($res);
    }

    /**
     * 删除评论
     */
    public function deleteReviewById(Request $request, Response $response, $args)
    {
        Review::findOrFail($args['id'])->delete();
    }

    /**
     * 获取某本图书或某个用户的评论
     * @param Request $request
     * @param Builder $query
     * @param ::class $Class Book或WechatUser
     * @return array $result 搜索结果
     *      $result = [
     *          'reviews' => Review[] 评论数组
     *          'start' => int 当前偏移量
     *          'count' => int 当前结果数
     *          'total' => int 该图书评论总数
     *      ]
     */
    private function getReviews(Request $request, $query)
    {
        $params = $request->getQueryParams();
        $reviews = $query->offset($params['start'])
            ->limit($params['count'])
            ->latest()
            ->get()
            ->map(function ($review) use ($request) {
                return $this->getReviewWithIsCreatorAttribute($review, $request);
            });
        $res = [
            'reviews' => $reviews,
            'start'   => (int)$params['start'],
            'count'   => (int)$params['count'],
            'total'   => $query->count()
        ];
        return $res;
    }

    /**
     * 根据当前token中的用户信息，为评论对象设置is_creator属性
     * @param Review $review
     * @param Request $request
     * @return Review
     */
    private function getReviewWithIsCreatorAttribute(Review $review, Request $request)
    {
        try {
            $uid = Authorization::getUserIdFromRequest($request);
            $review->setIsCreatorByUserId($uid);
        } catch (\Exception $e) {
            // 未登录时就是undefined
        }
        return $review;
    }
}
