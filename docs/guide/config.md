
# 环境配置
本项目服务器环境：Linux + Nginx + MySQL + PHP 7.0 / PHP 5.6。

## 配置 HTTPS
小程序要求域名必须采用`HTTPS`协议。

1. 在[腾讯云](https://qcloud.com)购买域名与服务器，完成备案
2. 在腾讯云为域名[申请SSL证书](https://console.cloud.tencent.com/ssl)
3. [下载](https://console.cloud.tencent.com/ssl)申请成功的证书，下载好的证书包含两个文件：`.key`与`.crt`
4. 在服务器的 Nginx 配置目录下新建一个目录`cert`（Ubuntu：`/etc/nginx/sites-available/default`），将这两个文件复制进去
5. 配置文件`site.default`相关内容如下，具体值需要从腾讯云获取：
```{4-5}
server {
    listen 443; # HTTPS 监听 443端口
    ssl on;
    ssl_certificate cert/xxxxxx.crt; # 这里填写你下载的 .crt 文件路径
    ssl_certificate_key cert/xxxxxx.key; # 这里填写你下载的 .key 文件路径
    ssl_session_timeout 5m;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_prefer_server_ciphers on;

    # ... 完整配置文件见下方
}
```

## URL 重写
ThinkPHP 或 Slim 框架支持 RESTful API，但是必须通过它们的入口文件访问 API。例如，获取`id`为 1 的图书信息时，URL如下：
```
https://www.my-api-server.cn/api/public/index.php/api/v1/books/1
```
我们希望省略入口文件路径`/api/public/index.php`，以这样的方式访问 API：
```
https://www.my-api-server.cn/api/v1/books/1
```
配置文件相关内容如下：

```{7-16}
server {
    # ... 完整配置文件见下方

    location / {
        root   $root;
        index  index.html index.php;
        # 如果访问内容是文件，返回文件内容
        if ( -f $request_filename) {
            break; 
        }
        # 如果访问内容不是文件，则重定向至/api/index.php/$1，$1是所访问的URL
        # 如/api/v1/books/1会被重定向至/api/index.php/api/v1/books/1
        if ( !-e $request_filename) {
            rewrite ^(.*)$ /api/index.php/$1 last;
            break;
        }
    }

    # ... 完整配置文件见下方
}
```


## 两个域名共用一台服务器
本项目共有两个版本的小程序：“附近的图书馆”（旧、稳定）与“在线借书平台”（新，重构），它们使用不同的域名。通过设置域名服务商的解析值，使两个域名指向一台服务器。通过在 Nginx 配置文件中添加`server`配置项即可支持多个域名：

```{5-7,16,28-30,39}
# 第一个域名的配置
server {
    listen 443; 
    ssl on;
    ssl_certificate cert/xxxxxx.crt; # 第一个域名的 .crt 文件路径
    ssl_certificate_key cert/xxxxxx.key; # 第一个域名的 .key 文件路径
    server_name xxxxxx.cn; # 监听第一个域名

    # ... 完整配置文件见下方

    location / {
        # ... 完整配置文件见下方

        # 第一个域名的根目录是 /api
        if ( !-e $request_filename) {
            rewrite ^(.*)$ /api/index.php/$1 last;
            break;
        }
    }

    # ... 完整配置文件见下方
}

# 第二个域名的配置
server {
    listen 443; 
    ssl on;
    ssl_certificate cert/yyyyyy.crt; # 第二个域名的 .crt 文件路径
    ssl_certificate_key cert/yyyyyy.key; # 第二个域名的 .key 文件路径
    server_name yyyyyy.cn; # 监听第二个域名

    # ... 完整配置文件见下方

    location / {
        # ... 完整配置文件见下方

        # 第二个域名的根目录是 /old_api
        if ( !-e $request_filename) {
            rewrite ^(.*)$ /old_api/index.php/$1 last;
            break;
        }
    }

    # ... 完整配置文件见下方
}
```

## 同一个服务器，两个PHP版本
上一节说过：本项目共有两个版本的小程序，它们使用不同的域名。同时，它们也使用不同的后台框架：“附近的图书馆”使用 ThinkPHP  + PHP 5.6，“在线借书平台”使用 Slim + PHP 7.0。因此，我们需要在不同的域名下使用不同版本的 PHP 解释器。

配置步骤如下：

1. 安装 PHP 5.6、php5.6-fpm、PHP 7.0、php7.0-fpm
2. 设置 php5.6-fpm 监听 9000 端口（默认），php7.0-fpm 监听 9001 端口
3. 为不同域名使用不同版本的 php-fpm，配置文件相关内容如下：

```{8,22}
# 第一个域名的配置
server {
    # ... 完整配置文件见下方

    location ~ [^/]\.php(/|$) {
        # 域名1的 PHP 版本为 7.0
        # 这么写也可以：fastcgi_pass  127.0.0.1:9001; 
        fastcgi_pass  unix:/run/php/php7.0-fpm.sock; 

        # ... 完整配置文件见下方
    }

    # ... 完整配置文件见下方
}

# 第二个域名的配置
server {
    # ... 完整配置文件见下方

    location ~ [^/]\.php(/|$) {
        # 域名2的 PHP 版本为 5.6
        fastcgi_pass  127.0.0.1:9000; 

        # ... 完整配置文件见下方
    }

    # ... 完整配置文件见下方
}
```

## 完整配置文件
```
# 第一个域名的配置
server {
    listen 443;
    ssl on;
    ssl_certificate cert/domain-name-1.cn_bundle.crt; # 域名1 .crt 文件路径
    ssl_certificate_key cert/domain-name-1.cn.key; # 域名1 .key 文件路径
    ssl_session_timeout 5m;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_prefer_server_ciphers on;
    
    set $root /usr/share/nginx/html; # 网站根目录
    error_log /usr/share/nginx/html/nginx.error.log;
    access_log /usr/share/nginx/html/nginx.access.log;
    index index.html index.htm index.php;

    server_name domain-name-1.cn; # 域名1
    client_max_body_size 50M;

    location / {
        root   $root;
        index  index.html index.php;
        if ( -f $request_filename) {
            break;
        }
        if ( !-e $request_filename) {
            rewrite ^(.*)$ /api/index.php/$1 last; # 域名1的入口文件
            break;
        }
    }

    location ~ [^/]\.php(/|$) {
        #listen unix socket
        #fastcgi_pass  unix:/tmp/php-cgi.sock;
        #listen tcp socket

        fastcgi_pass  unix:/run/php/php7.0-fpm.sock; # 域名1的 PHP 版本为 7.0

        #pathinfo
        fastcgi_split_path_info ^((?U).+.php)(/?.+)$;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_param    SCRIPT_FILENAME    $root$fastcgi_script_name;
        include        fastcgi_params;
    }
}

# 第二个域名的配置
server {
    listen 443;
    ssl on;
    ssl_certificate cert/domain-name-2.pem; # 域名2 .crt 文件路径
    ssl_certificate_key cert/domain-name-2.key; # 域名2 .crt 文件路径
    ssl_session_timeout 5m;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_prefer_server_ciphers on;
    
    set $root /usr/share/nginx/html;
    error_log /usr/share/nginx/html/nginx.error.log;
    access_log /usr/share/nginx/html/nginx.access.log;
    index index.html index.htm index.php;

    server_name domain-name-2.cn; # 域名2
    client_max_body_size 50M;

    location / {
        root   $root;
        index  index.html index.php;
        if ( -f $request_filename) {
            break;
        }
        if ( !-e $request_filename) {
            rewrite ^(.*)$ /old_api/index.php/$1 last; # 域名2的入口文件
            break;
        }
    }

    location ~ [^/]\.php(/|$) {
        #listen unix socket
        #fastcgi_pass  unix:/tmp/php-cgi.sock;
        #listen tcp socket

        fastcgi_pass  127.0.0.1:9000; # 域名2的 PHP 版本为 5.6

        #pathinfo
        fastcgi_split_path_info ^((?U).+.php)(/?.+)$;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_param    SCRIPT_FILENAME    $root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
```