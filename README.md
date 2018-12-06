# think-light-doc
A light weight document tool for ThinkPHP v5.1
面向ThinkPHP v5.1的轻量级文档工具

[前端项目地址](https://github.com/finntenzor/light-doc)

## 如何使用

### 添加依赖
``` sh
$ composer require finntenzor/think-light-doc
```

### 编写文档

[文档详细规则（建设中）](document.md)

+ 某一个文档文件（例如项目根目录/docs/v1_1_3.php）
    ``` php
    <?php
    //  文档文件必须返回一个数组，可以在返回数组前执行其他代码

    return [
        // 按照规则编写文档描述即可
        ...
    ];

    ```

### 指定路由

在你的ThinkPHP项目中，找到/route/route.php。在你需要的位置添加如下代码：
``` php
// 引入LightDoc
use finntenzor\lightdoc\LightDoc;

// 
// 指定路由
// 
// 第一个参数表示路由组(Group)的组名
// 第二个参数为一个关联数组
//     每一个键值对表示一个版本的文档
//     键名表示这个版本文档的版本号（路由中使用此版本号标识）
//     键值表示这个版本文档的文档文件路径（物理路径，不是url）
//
LightDoc::route('lightdoc', [
    //
    // 路径中可以使用@表示项目根目录
    //
    'v1.1.3' => '@/docs/v1_1_3.php',
    //
    // 它等价于如下代码
    //
    'v2.0.1' => substr(Env::get('root_path'), 0, -1) . '/docs/v2_0_1.php',
]);
```

它等价于自动编写一个Group以及对应的一组路由，因此你可以很轻松地将它和其他路由混合在一起：
``` php
use finntenzor\lightdoc\LightDoc;

Route::group('app', function () {
    Route::group('api', function () {
        // 一些其他路由
    });
    // 将app/docs注册为文档查看路径
    LightDoc::route('docs', [
        'v1.1.3' => '@/docs/v1_1_3.php',
        'v2.0.1' => '@/docs/v2_0_1.php',
    ]);
});
```

并且，它返回了一个RouteGroup，因此你还可以将它跟中间件等其他功能结合在一起。
``` php
use finntenzor\lightdoc\LightDoc;

Route::group('app', function () {
    Route::group('api', function () {
        // 一些其他路由
    });
    // 将app/docs注册为文档查看路径
    LightDoc::route('docs', [
        'v1.1.3' => '@/docs/v1_1_3.php',
        'v2.0.1' => '@/docs/v2_0_1.php',
    ])->middleware('MustAdmin'); // 定制为仅管理员可以查看
});
```

### 使用
按照路由访问对应地址即可
