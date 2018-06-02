<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/3
 * Time: 下午5:13
 */

namespace App\Controllers;

use App\Authorization\Authorization;
use App\Models\Booklist;
use Illuminate\Database\Eloquent\Collection;
use App\Models\SimpleBooklist;
use App\Models\WechatUser;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * 为每个返回的Booklist对象添加status属性
 */
class BooklistController
{
    /**
     * 获取书单详细信息
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Booklist
     */
    public function getBooklistById(Request $request, Response $response, $args)
    {
        $booklist = Booklist::findOrFail($args['id']);
        return $response->withJson($this->getBooklistWithStatusAttribute($booklist, $request));
    }

    /**
     * 获取书单内图书
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return array $result 搜索结果
     *      $result = [
     *          'books' => SimpleBook[] 图书数组
     *          'start' => int 当前偏移量
     *          'count' => int 当前结果数
     *          'total' => int 书单内图书总数
     *      ]
     */
    public function getBooksByBooklistId(Request $request, Response $response, $args)
    {
        $query = $request->getQueryParams();
        $booklist = Booklist::findOrFail($args['id']);
        $res = [
            'books' => $booklist->getItems($query['start'], $query['count']),
            'start' => (int)$query['start'],
            'count' => (int)$query['count'],
            'total' => $booklist->total
        ];
        return $response->withJson($res);
    }

    /**
     * 创建书单
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @throws \Exception
     */
    public function createBooklist(Request $request, Response $response)
    {
        // 创建书单
        $info = $request->getParsedBody();
        $booklist = Booklist::create($info);

        // 在关系表内添加条目
        $uid = Authorization::getUserIdFromRequest($request);
        $booklist->users()->syncWithoutDetaching([$uid]);

        return $response->withJson($this->getBooklistWithStatusAttribute($booklist, $request));
    }

    /**
     * 更新书单
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Booklist
     */
    public function updateBooklistById(Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody();
        $booklist = Booklist::findOrFail($args['id']);

        // 保存标题与描述
        if (isset($params['title'])) {
            $booklist->title = $params['title'];
        }
        if (isset($params['description'])) {
            $booklist->description = $params['description'];
        }
        $booklist->save();

        // 添加书目
        $addItems = [];
        foreach ($params['add_items'] as $item) {
            $addItems[$item['book_id']] = ['comment' => $item['comment']];
        }
        $booklist->books()->syncWithoutDetaching($addItems);

        // 删除书目
        if ($params['delete_items']) {
            $booklist->books()->detach($params['delete_items']);
        }

        return $response->withJson($this->getBooklistWithStatusAttribute($booklist, $request));
    }

    /**
     * 收藏书单
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @throws
     * @return Booklist
     */
    public function favoriteBooklistById(Request $request, Response $response, $args)
    {
        $uid = Authorization::getUserIdFromRequest($request);
        $booklist = Booklist::findOrFail($args['id']);
        $booklist->users()->syncWithoutDetaching([$uid]);
        return $response->withJson($this->getBooklistWithStatusAttribute($booklist, $request));
    }

    /**
     * 删除书单/取消收藏
     * @param Request $request
     * @param Response $response
     * @throws
     * @param array $args
     */
    public function deleteBooklistById(Request $request, Response $response, $args)
    {
        $uid = Authorization::getUserIdFromRequest($request);
        Booklist::findOrFail($args['id'])->users()->detach($uid);
    }

    /**
     * 根据标题或作者名搜索书单
     * @param Request $request
     * @param Response $response
     * @return array $result 搜索结果
     *      $result = [
     *          'booklists' => SimpleBooklist[] 书单数组
     *          'start' => int 当前偏移量
     *          'count' => int 当前结果数
     *          'total' => int 结果总数
     *      ]
     */
    public function getBooklistsByKeyword(Request $request, Response $response)
    {
        $query = $request->getQueryParams();
        $keyword = $query['keyword'];

        // 查询构建器
        $builder = SimpleBooklist::with('users')
            // 关键字可能是书单标题, 也可能是创建者昵称
            // 关键字之间的空格替换为%，表示关键字之间可以有任意字符
            ->where('title', 'like', '%' . implode('%', preg_split('/\s+/', $keyword)) . '%')
            ->orWhereHas('creator', function ($query) use ($keyword) {
                $query->where('nickname', 'like', '%' . implode('%', preg_split('/\s+/', $keyword)) . '%');
            });

        $booklists = $builder->offset($query['start'])->limit($query['count']) ->get();
        $res = [
            'booklists' => $this->getBooklistsWithStatusAttribute($booklists, $request),
            'start'     => (int)$query['start'],
            'count'     => (int)$query['count'],
            'total'     => $builder->count()
        ];
        return $response->withJson($res);
    }

    /**
     * 获取用户创建和收藏的书单列表
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return array $result 搜索结果
     *      $result = [
     *          'create' => SimpleBooklist[] 书单数组
     *          'favorite' => SimpleBooklist[] 书单数组
     *      ]
     */
    public function getBooklistsByUserId(Request $request, Response $response, $args)
    {
        $uid = $args['id'];
        $createBooklists = WechatUser::find($uid)->booklists()
            // 找到其中该用户创建的书单
            ->whereHas('creator', function ($query) use ($uid) {
                $query->where('id', $uid);
            })->get();
        $favoriteBooklists = WechatUser::find($uid)->booklists()
            // 找到其中不是该用户创建的书单
            ->whereHas('creator', function ($query) use ($uid) {
                $query->where('id', '!=', $uid);
            })->get();
        $res = [
            // 获取该用户的所有书单
            'create'   => $this->getBooklistsWithStatusAttribute($createBooklists, $request),
            'favorite' => $this->getBooklistsWithStatusAttribute($favoriteBooklists, $request),
        ];
        return $response->withJson($res);
    }

    /**
     * 获取推荐书单
     * TODO 获取推荐书单，目前随机返回10个书单
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function getRecommendedBooklistsByUserId(Request $request, Response $response)
    {
        $booklists = SimpleBooklist::inRandomOrder()
            ->limit(10)
            ->get();
        return $response->withJson($this->getBooklistsWithStatusAttribute($booklists, $request));
    }

    /**
     * 根据当前token中的用户信息，为返回的书单对象设置status属性，未登录时设置默认值
     * @param Booklist $booklist
     * @param Request $request
     * @return Booklist
     */
    private function getBooklistWithStatusAttribute(Booklist $booklist, Request $request)
    {
        try {
            $uid = Authorization::getUserIdFromRequest($request);
            $booklist->setStatusAttributeByUserId($uid);
        } catch (\Exception $e) {
            // token不合法，未登录
            $booklist->setStatusAttributeAsDefault();
        }
        return $booklist;
    }

    /**
     * 同上，为书单对象集合中的每个元素设置status属性
     * @param Collection $booklists
     * @param Request $request
     * @return Collection|\Illuminate\Support\Collection
     */
    private function getBooklistsWithStatusAttribute(Collection $booklists, Request $request)
    {
        return $booklists->map(function ($booklist) use (
            $request
        ) {
            return $this->getBooklistWithStatusAttribute($booklist, $request);
        });
    }
}