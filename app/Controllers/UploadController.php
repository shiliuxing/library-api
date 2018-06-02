<?php
/**
 * User: ZhuKaihao
 * Date: 2018/5/16
 * Time: 上午10:21
 */

namespace App\Controllers;

use App\Exceptions\BadRequestException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class UploadController
{
    const DOMAIN_NAME = 'https://www.library-online.cn';
    const WEB_ROOT_PATH = '/usr/share/nginx/html';
    const IMG_DIRECTORY_PATH = '/imgs';
//    const DOMAIN_NAME = 'http://localhost:8080';
//    const WEB_ROOT_PATH = '/usr/local/var/www/htdocs';

    /**
     * 上传图片，返回图片的完整路径
     * @param Request $request
     * @param Response $response
     * @throws BadRequestException
     * @return string
     */
    public function upload(Request $request, Response $response)
    {
        // 获取文件对象
        $file = $request->getUploadedFiles()['picture'];
        $extension = pathinfo($file->getClientFileName(), PATHINFO_EXTENSION);

        // 检验文件类型、文件大小
        if (!in_array($extension, ['jpg', 'png', 'jpeg', 'JPG', 'PNG', 'JPEG'])) {
            throw new BadRequestException(ONLY_IMAGE);
        }
        if ($file->getSize() > 2 * 1024 * 1024) {
            throw new BadRequestException(MAX_2M);
        }

        // 上传图片
        $basename = uniqid();
        $fileName = sprintf('%s.%s', $basename, $extension);
        $relativePath = sprintf('%s/%s', self::IMG_DIRECTORY_PATH, $fileName);
        $file->moveTo(sprintf(self::WEB_ROOT_PATH . $relativePath));

        // 返回完整路径
        return self::DOMAIN_NAME . $relativePath;
    }
}