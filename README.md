
[文档首页](https://www.library-online.cn/docs/) | [后台系统文档](https://www.library-online.cn/docs/guide/back.html)

**文件结构**：

```
.
├── app                   // 主目录
│   ├── Authorization     // 登录授权
│   ├── Controllers       // 业务逻辑控制器
│   ├── Exceptions        // 异常封装
│   ├── Helpers           
│   │   └── Message.php   // 短信发送
│   ├── Middlewares       // 中间件
│   │   ├── Auth.php      // 校验token
│   │   └── validateStartAndCount.php // 校验query中的start与count
│   ├── Models            // 模型类
│   ├── Validators        // Respect\Validation校验类
│   └── error_message.php // 错误信息文本定义
├── bootstrap             // 应用配置
│   ├── configuration.php // 配置项
│   ├── dependencies.php  // 数据库依赖
│   ├── error_handlers.php// 错误处理器
│   └── routes            // 路由配置
│       ├── index.php     
│       └── modules       // 各个模块下的路由
├── composer.json
├── composer.lock
├── vendor                // 依赖库
├── db                    // 数据库文件
│   ├── library_scheme.sql// 所有数据表结构
│   ├── library_data.sql  // 所有数据表数据
    └── ...               // 单个数据表的结构+数据
└── public
    └── index.php         // 入口文件
```