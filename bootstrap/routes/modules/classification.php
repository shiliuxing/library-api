<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/3
 * Time: 上午10:05
 */


// 获取分类号信息
use App\Middlewares\validateStartAndCount;

$app->get('/api/classifications/{number}/sons', PREFIX . 'ClassificationController:getSonNumbersByNumber')->add(validateStartAndCount::class);