<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/1
 * Time: 上午1:06
 */

use Respect\Validation\Validator as v;

$app->group('/api/codes', function () {
    $this->post('', PREFIX . 'CodeController:send')->add(function ($request, $response, $next) {
        // 经费有限，一天只发10条短信
        $count = \App\Models\Code::whereDate('updated_at', '>=', \Carbon\Carbon::today())->count();
        if ($count >= 1) {
            throw new \App\Exceptions\BadRequestException('服务器今日短信发送数目已达上限，您可登录测试账号使用：手机号13000000000, 验证码123123');
        }
        return $next($request, $response);
    });
    $this->get('/check', PREFIX . 'CodeController:check');
})->add(function ($request, $response, $next) {
    $query = $request->getQueryParams();
    v::chinaPhone()->setName('phone')->check($query['phone']);
    v::in(['wechat', 'library'])->setName('type')->check($query['type']);
    if ($request->isGet()) {
        // 如果访问的是'/check', 还需要校验code
        v::digit()->between(100000, 999999)->setName('code')->check($query['code']);
    }
    return $next($request, $response);
});
