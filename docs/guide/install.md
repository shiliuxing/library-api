# 安装
## 小程序
1. 安装最新版微信小程序开发工具
2. 下载[“在线借书平台”小程序源代码](https://github.com/imageslr/weapp-library)  
   * 下载`zip`压缩包并解压
   * 或：命令行执行`git clone https://github.com/imageslr/weapp-library.git`
3. 打开微信开发者工具→新建项目→选择源码目录→填写`appid`
4. 运行成功

### 常见问题
#### 使用“分类查找”时，点击到某个类别后页面无法继续跳转
小程序项目没有`appid`时最多只能跳转五个页面，填写`appid`后可以跳转十个页面。

#### 点击`开发者工具→预览`时报错“代码包过大”
这是因为开发者工具会将当前项目目录的所有未忽略文件都打包。在`project.config.json`中添加下列字段以忽略无用文件：

```JSON
"packOptions": {
  "ignore": [{
      "type": "file",
      "value": "./ui.png"
  }]
}
```

## 后端
::: warning 注意
* 服务器推荐配置：Linux + Nginx + MySQL + PHP 7.0
* **必须**：PHP 7.0 以上 + 安装 Composer
* **必须**：HTTPS

您可参考：[环境配置](config.md)
:::

### 创建数据库
本项目提供了[ .sql 文件](https://github.com/imageslr/library-api/tree/master/db/)，可直接导入数据库。

#### phpMyAdmin 导入
phpMyAdmin 可能无法导入 2M 以上的文件，需修改`php.ini`->`upload_max_size`为 20M 以上，然后重启`php-fpm`使配置生效。
#### 命令行导入
好处是无需改动`php.ini`，可以参考[这篇博客](https://blog.csdn.net/linglongwunv/article/details/5212696)，步骤如下：

1. 将`.sql`文件上传至服务器
2. 从命令行启动 mysql
3. 执行以下语句
```sql
> mysqld 
create database table_name; （注意分号）
use table_name;
source /path/to/sql.sql;
```

### 安装项目

1. 下载[后台系统源代码](https://github.com/imageslr/library-api)
   * 下载`zip`压缩包并解压
   * 或：命令行执行`git clone https://github.com/imageslr/library-api.git`
2. 在源代码根目录运行：`composer install`安装依赖，请确保您已全局安装 Composer
3. 配置数据库信息：`/bootstrap/configuration.php`
4. 访问以下路径，测试后台应用程序是否安装成功：
`https://your-domain.com/path/to/library-api/public/index.php/books/1000`  
其中`/path/to/library-api`是项目根目录所在的公共位置  
5. 如果想让 URL 更简短，请参考[环境配置-URL重写](./config.md#url-重写)