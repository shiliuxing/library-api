<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/3
 * Time: 下午4:10
 */
namespace App\Middlewares;

use Respect\Validation\Validator as v;

/**
 * 校验query中的start与count参数, 并设置默认值
 * start: 默认0
 * count: 0~100, 默认20
 */
class validateStartAndCount
{
    public function __invoke($request, $response, $next)
    {
        $query = $request->getQueryParams();
        if (!$query['start']) $query['start'] = 0;
        if (!$query['count']) $query['count'] = 20;

        v::arrayType()
            ->key('start', v::min(0), false)
            ->key('count', v::min(0)->max(100), false)
            ->check($query);
        return $next($request->withQueryParams($query), $response);
    }
}
