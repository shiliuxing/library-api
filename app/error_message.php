<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/21
 * Time: 下午4:35
 */

define('NOT_LOGIN', '您还未登录');
define('INVALID_TOKEN', 'token不合法');
define('NO_PERMISSION', '您无权访问');

define('BOOK_NOT_FOUNT_FRIENDLY', '暂无该图书信息');
define('BOOK_NOT_FOUND', '图书不存在');
define('LIBRARY_NOT_FOUND', '图书馆不存在');
define('USER_NOT_FOUND', '用户不存在');
define('UNSUPPORTED_SERVICE', '暂不支持该功能');

define('SEND_VERIFICATION_CODE_FAILED', '发送验证码失败');
define('WRONG_VERIFICATION_CODE', '验证码错误');
define('VERIFICATION_CODE_OVERDUE', '验证码已过期, 请重新获取');
define('SEND_VERIFICATION_TOO_FREQUENTLY', '发送短信验证码过于频繁');

define('ORDER_EXIST', '订单已存在, 不可重复创建');
define('ORDER_NOT_EXIST', '该订单不存在');
define('ORDER_CANNOT_CANCEL', '该订单不可取消');
define('CANNOT_RENEW_UNBORROWING', '该订单不是借阅中订单, 无法续借');
define('CANNOT_RENEW_HAS_RENEWED', '该订单无法续借: 已续借一次');
define('ORDER_CANNOT_TAKE_UNRETURN', '该订单无法取书: 其他用户还未归还该图书');
define('ORDER_HAS_TAKEN', '该订单已取书');

define('ONLY_IMAGE', '只能上传图片：jpg、png或jpeg');
define('MAX_2M', '图片最大不得超过2M');

define('INVALID_PARAM', '参数校验错误');
define('UNKNOWN_ERROR', '发生未知错误, 请联系开发者');
define('PAGE_NOT_FOUND', '接口错误：Page Not Found');