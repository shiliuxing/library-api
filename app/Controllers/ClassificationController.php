<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/3
 * Time: 上午10:07
 */

namespace App\Controllers;

use App\Models\Classification;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class ClassificationController
{
    public function getSonNumbersByNumber(Request $request, Response $response, $args)
    {
        $query = $request->getQueryParams();
        $res = [
            'son_numbers' => Classification::sonOf($args['number'])
                ->offset($query['start'])
                ->limit($query['count'])
                ->get(),
            'start'       => $query['start'],
            'count'       => $query['count']
        ];
        return $response->withJson($res);
    }
}