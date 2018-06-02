<?php
/**
 * 全局错误处理
 * User: ZhuKaihao
 * Date: 2018/4/29
 * Time: 下午4:05
 */

use App\Exceptions\ForbiddenException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Exceptions\InternalErrorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Respect\Validation\Exceptions\ValidationException;

$container = $app->getContainer();
$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        // Validator 参数校验错误
        if ($exception instanceof ValidationException) {
            return $c['response']
                ->withStatus(400)
                ->withJson(new BadRequestException(INVALID_PARAM, $exception->getMessage()));
        }

        // Eloquent 批量插入数据错误
        if ($exception instanceof MassAssignmentException) {
            return $c['response']
                ->withStatus(500)
                ->withJson(new InternalErrorException(
                    UNKNOWN_ERROR,
                    '批量插入错误. 不能插入该列: ' . $exception->getMessage()
                ));
        }

        // Eloquent 模型未找到(firstOrFail)
        if ($exception instanceof ModelNotFoundException) {
            $message = $exception->getModel() . ' 资源不存在';
            $errMsg = $exception->getMessage();
            return $c['response']->withJson(new NotFoundException($message, $errMsg))->withStatus(404);
        }

        // 自定义 Not Found
        if ($exception instanceof NotFoundException) {
            return $c['response']->withJson($exception)->withStatus(404);
        }

        // 自定义 400 Bad Request
        if ($exception instanceof BadRequestException) {
            return $c['response']->withJson($exception)->withStatus(400);
        }

        // 自定义 403 Forbidden
        if ($exception instanceof ForbiddenException) {
            return $c['response']->withJson($exception)->withStatus(403);
        }

        // 自定义 500 Internal Error
        if ($exception instanceof InternalErrorException) {
            return $c['response']->withJson($exception)->withStatus(500);
        }

        // 其他错误
        return $c['response']
            ->withStatus(500)
            ->withJson(new InternalErrorException(UNKNOWN_ERROR,  $exception->getMessage()));
    };
};


// 路由失败 Page Not Found
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']->withJson(new NotFoundException(PAGE_NOT_FOUND))->withStatus(404);
    };
};