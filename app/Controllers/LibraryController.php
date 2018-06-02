<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/5
 * Time: 下午7:49
 */

namespace App\Controllers;

use App\Models\Library;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class LibraryController
{
    /**
     * 获取图书馆详细信息
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Library
     */
    public function getLibraryById(Request $request, Response $response, $args)
    {
        return $response->withJson(Library::findOrFail($args['id']));
    }
}