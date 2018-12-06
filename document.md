# 文档详细规则（建设中）

``` php
<?php
//  文档文件必须返回一个数组，可以在返回数组前执行其他代码

return [
  // 1级属性 version 表示该文档对应API的版本号，请尽量和路由中的版一致
  'version' => 'v1.1.3',
  // 1级属性 intro 表示该文档的综述
  'intro' => [
    // 2级属性 intro/description 综述的具体内容
    'description' => '此项目接口具有统一规范，响应体ret为200时表示请求有效运行正常。'
    . '响应体ret为400以及以上时表示请求无效不能完成请求，并且响应的HTTP状态码也会随之改变。',
    // 2级属性 intro/examples 综述样例请求，可以为空数组
    'examples' => [
      // 3级属性 intro/examples/* ResponseExample对象 响应样例
      [
        // 4级属性 intro/examples/*/description 样例描述，这个请求样例的小标题
        'description' => '成功',
        // 4级属性 intro/examples/*/response 响应样例，样例响应的响应体
        'response' => [
          'ret' => 200,
          'data' => '...'
        ]
      ],
      [
        'description' => '失败',
        'response' => [
          'ret' => 500,
          'msg' => '错误信息'
        ]
      ],
    ]
  ],
  // 1级属性 groups 该文档各个模块组
  'groups' => [
    // 2级属性 groups/* Group对象 键名为该模块组的标识符，键值为Group对象
    'UserAndAuth' => [
      // 3级属性 groups/*/name 该模块组的名称，显示为小标题
      'name' => '注册与登录',
      // 3级属性 groups/*/apis 该模块的各个API接口
      'apis' => [
        // 4级属性 groups/*/apis/* Api对象 某一个API接口
        'register' => [
          // 5级属性 groups/*/apis/*/name 该接口的名称，显示为小标题
          'name' => '注册',
          // 5级属性 groups/*/apis/*/method 该接口的请求方法
          'method' => 'POST',
          // 5级属性 groups/*/apis/*/method 该接口的路径，可以存在url参数，请按照后面的样例编写
          'url' => '/api/v1.1.3/user/register',
          // 5级属性 groups/*/apis/*/description 该接口的详情描述
          'description' => '注册一个新用户，已有用户则返回错误。请求体中username是用户名，'
          . 'password是密码，用户名和密码均有长度限制和字符范围限制，注册失败时返回ret为500。',
          // 5级属性 groups/*/apis/*/examples 接口请求与响应样例，！!至少！！含有一个元素
          'examples' => [
            // 6级属性 groups/*/apis/*/examples/* ApiExample对象 接口请求与响应样例
            [
              // 7级属性 groups/*/apis/*/examples/*/description 样例描述，显示为小标题
              'description' => '正常注册',
              // 7级属性 groups/*/apis/*/examples/*/request 请求描述
              'request' => [
                // 8级属性 groups/*/apis/*/examples/*/request/params URL参数，没有可以写null但不可缺省
                'params' => null,
                // 8级属性 groups/*/apis/*/examples/*/request/query GET参数（查询字符串），没有可以写null但不可缺省
                'query' => null,
                // 8级属性 groups/*/apis/*/examples/*/request/body 请求体，没有可以写null但不可缺省
                'body' => [
                  'username' => 'myusername',
                  'password' => 'secret'
                ]
              ],
              // 7级属性 groups/*/apis/*/examples/*/response 响应样例
              'response' => [
                'ret' => 200,
                'data' => '注册成功'
              ]
            ],
            [
              'description' => '如果用户已经存在',
              'request' => [
                'params' => null,
                'query' => null,
                'body' => [
                  'username' => 'myusername',
                  'password' => 'secret'
                ]
              ],
              'response' => [
                'ret' => 500,
                'msg' => '用户已存在'
              ]
            ],
          ]
        ],
        // 这是一个使用了url参数的例子
        'getUserById' => [
          'name' => '根据ID获取用户信息',
          'method' => 'GET',
          // 请注意这里使用了 “:id” 来表示这是一个url参数，其中冒号是url参数的标识
          'url' => '/api/v1.1.3/users/:id',
          'description' => '根据ID获取用户信息，传入url参数id。',
          'examples' => [
            [
              'description' => '正常获取',
              'request' => [
                // 注意这里使用了一个关联数组（JSON对象）来描述url参数
                // 请保证这里的参数数目和url中参数数目完全一致，并且参数名也一致
                'params' => [
                  'id' => 123
                ],
                'query' => null,
                'body' => null
              ],
              'response' => [
                'ret' => 200,
                'data' => [
                  'id' => 123,
                  'usernam' => 'username'
                ]
              ]
            ]
          ]
        ],
        // 这是一个使用了GET参数（查询字符串）的例子
        'queryUsers' => [
          'name' => '获取所有用户信息',
          'method' => 'GET',
          // 请注意这里不要出现查询参数，也不要出现问号标识符(?)
          'url' => '/api/v1.1.3/users',
          'description' => '获取所有用户信息，传入page表示分页',
          'examples' => [
            [
              'description' => '正常登录时',
              'request' => [
                'params' => null,
                // 注意这里使用了一个关联数组（JSON对象）来描述GET参数情况
                // LightDoc会自动将其拼接为查询参数的形式
                'query' => [
                  'page' => 2
                ],
                'body' => null
              ],
              'response' => [
                'ret' => 200,
                'data' => [
                  'current_page' => 2,
                  'total' => 600,
                  'per_page' => 20,
                  'data' => [
                    [
                      'id' => 21,
                      'username' => 'user_21'
                    ],
                    [
                      'id' => 22,
                      'username' => 'user_22'
                    ],
                    [
                      // 如果需要省略中间过多信息，可以采用这种形式
                      // 保证整个关联数组可以json化即可
                      '...' => '...'
                    ],
                    [
                      'id' => 40,
                      'username' => 'user_40'
                    ]
                  ]
                ]
              ]
            ],
          ]
        ],
      ]
    ],
  ]
];
```
