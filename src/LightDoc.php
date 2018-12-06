<?php
namespace finntenzor\lightdoc;

use think\facade\Route;

/**
 * LightDoc
 * 外部访问类
 * @author FinnTenzor <finntenzor@gmail.com>
 */
class LightDoc
{
    /**
     * @var array 文档路由描述
     */
    public static $documentMap;

    /**
     * 创建错误报告路由组
     * @param string $groupName 组名
     * @return \think\route\RuleGroup 生成的路由组
     */
    public static function route($groupName, $documentMap)
    {
        static::$documentMap = $documentMap;
        return Route::group($groupName, function () {
            Route::get('/docs/:version', '\finntenzor\lightdoc\LightDocController@getDocumentByVersion')->pattern(['version' => '[\w\-\.]+']);
            Route::get('/docs', '\finntenzor\lightdoc\LightDocController@getAllVersions');
            Route::get('/', '\finntenzor\lightdoc\LightDocController@redirect');
            Route::get('/index', '\finntenzor\lightdoc\LightDocController@index');
            Route::miss('\finntenzor\lightdoc\LightDocController@miss');
        });
    }
}
