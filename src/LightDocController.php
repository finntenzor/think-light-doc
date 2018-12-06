<?php
namespace finntenzor\lightdoc;

use think\facade\Env;
use think\facade\Response;
use think\facade\Url;
use think\facade\Request;

/**
 * LightDocController
 * 文档控制器
 * @author FinnTenzor <finntenzor@gmail.com>
 */
class LightDocController
{
    /**
     * 返回某一个版本的文档
     * @param string $version 版本标识
     */
    public function getDocumentByVersion($version)
    {
        if (!isset(LightDoc::$documentMap[$version])) {
            return $this->error('没有此版本的文档');
        }
        $path = LightDoc::$documentMap[$version];
        $rootPath = Env::get('root_path');
        $path = \realpath(\preg_replace('/^@\//', $rootPath));
        if ($path === false) {
            return $this->error('找不到文档，请联系管理员');
        }
        $data = include $path;
        return $this->success($data);
    }

    /**
     * 返回所有的文档版本
     */
    public function getAllVersions()
    {
        if (!\is_array(LightDoc::$documentMap)) {
            return $this->error('文档路由描述无效，请联系管理员');
        }
        return $this->success(array_keys(LightDoc::$documentMap));
    }

    /**
     * 打开文档页面
     */
    public function index()
    {
        return file_get_contents('../dist/index.html');
    }

    /**
     * miss路由
     */
    public function miss(Request $request)
    {
        $root = Url::build('\finntenzor\lightdoc\LightDocController@index', '', false);
        $url = $request->url(true);
        $relativePath = \preg_replace('/^' . $root . '/', $url);
        $path = \realpath('../dist' . $relativePath);
        if ($path === false) {
            return $this->error('404 Not Found', 404);
        }
        return file_get_contents($path);
    }

    /**
     * 返回成功响应
     * @param array $data 数据
     * @return \think\reponse\Json 响应
     */
    private function success($data)
    {
        return Response::create([
            'ret' => 200,
            'data' => $data
        ], 'json');
    }

    /**
     * 返回错误响应
     * @param string $msg 错误消息
     * @return \think\reponse\Json 响应
     */
    private function error($msg, $ret = 500)
    {
        return Response::create([
            'ret' => $ret,
            'msg' => $msg
        ], 'json', $ret);
    }
}
