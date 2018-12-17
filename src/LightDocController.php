<?php
namespace finntenzor\lightdoc;

use think\facade\Env;
use think\facade\Response;
use think\facade\Url;
use think\Request;

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
        $path = \realpath(\preg_replace('/^@\//', $rootPath, $path));
        if ($path === false) {
            return $this->error('找不到文档，请联系管理员');
        }
        $data = LightDocCache::cache($path);
        $response = new \think\Response($data);
        $response->contentType('application/json');
        return $response;
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
     * 重定向至文档页面以保证路径正确
     */
    public function redirect()
    {
        return Response::create('\finntenzor\lightdoc\LightDocController@index', 'redirect', 302);
    }

    /**
     * 打开文档页面
     */
    public function index()
    {
        return file_get_contents(static::getLibPath() . '/dist/index.html');
    }

    /**
     * miss路由
     */
    public function miss(Request $request)
    {
        // 计算相对路径
        $root = Url::build('\finntenzor\lightdoc\LightDocController@redirect', '', false);
        $url = $request->url(true);
        $relativePath = \preg_replace('#^' . $root . '#', '', $url);
        // 如果访问的路径是/则跳转
        if ($relativePath === '/') {
            return $this->redirect();
        }
        // 路径验证
        $path = \realpath(static::getLibPath() . '/dist' . $relativePath);
        if ($path === false) {
            return $this->error('404 Not Found', 404);
        }
        // 禁止访问目录或者其他非文件路径
        if (!is_file($path)) {
            return $this->error('403 Forbidden', 403);
        }
        // 检查是否是dist以内的目录，防范URL攻击
        $distPath = realpath(static::getLibPath() . '/dist');
        $index = strpos($path, $distPath);
        if ($index !== 0) {
            return $this->error('403 Forbidden', 403);
        }
        // 获取文件内容
        $content = file_get_contents($path);
        // 计算文件类型并返回合适的响应
        return Response::create($content, '', 200, [
            'Content-Type' => static::getContentType($relativePath)
        ]);
    }

    /**
     * 根据文件名判断ContentType
     * @param string $path 文件名/路径
     * @return string 类型
     */
    private static function getContentType($path)
    {
        preg_match('/\.(\w+)$/', $path, $matches);
        $ext = isset($matches[1]) ? $matches[1] : '_';
        return Util::getMimeType($ext);
    }

    /**
     * 返回当前库的根目录
     * @return string 当前库根目录
     */
    private static function getLibPath()
    {
        $src = \preg_replace('/\w+\.php$/', '', __FILE__);
        return \realpath($src . '../');
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
