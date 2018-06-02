<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/5
 * Time: 下午7:46
 */

$app->get('/api/libraries/{id:[0-9]+}', PREFIX . 'LibraryController:getLibraryById');