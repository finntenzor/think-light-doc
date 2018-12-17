<?php
namespace finntenzor\lightdoc;

use think\Container;

class LightDocCache
{
    /**
     * 原文档文件路径
     * @var string
     */
    protected $opath;

    /**
     * 缓存路径
     * @var string
     */
    protected $path;

    /**
     * 构造器
     * @param $opath 原文档文件路径
     */
    public function __construct($opath)
    {
        $id = sha1($opath);
        $this->opath = $opath;
        $this->path = static::getCacheDirOrCreate() . $id;
    }

    /**
     * 缓存是否有效
     * @return bool
     */
    public function isCacheValid()
    {
        $otime = filemtime($this->opath);
        if (file_exists($this->path)) {
            $ptime = filemtime($this->path);
            return $ptime > $otime;
        } else {
            return false;
        }
    }

    /**
     * 获取缓存
     * @return string|null
     */
    public function getCache()
    {
        if (file_exists($this->path)) {
            return file_get_contents($this->path);
        } else {
            return null;
        }
    }

    /**
     * 写入缓存
     * @param array $data 数据
     */
    public function writeCache($data)
    {
        file_put_contents($this->path, $data);
    }

    /**
     * 通过缓存
     * @param string $opath 原文档路径
     * @return string json格式化的内容
     */
    public static function cache($opath)
    {
        $cache = new self($opath);
        if ($cache->isCacheValid()) {
            return $cache->getCache();
        } else {
            $data = include $opath;
            $data = static::build($data);
            $cache->writeCache($data);
            return $data;
        }
    }

    /**
     * 获取缓存目录
     * @return string
     */
    public static function getCacheDir()
    {
        return Container::get('app')->getRuntimePath() . 'lightdoc' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取缓存目录，不存在则创建
     * @return string
     */
    public static function getCacheDirOrCreate()
    {
        $dir = static::getCacheDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * 将数据转化成响应体内容
     * @return string
     */
    protected static function build($data)
    {
        return json_encode([
            'ret' => 200,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
    }
}
