# fastadmin 一键登录插件

> 现仅支持微信快捷登录，使用APP登录方式

## 文档地址

https://console-docs.apipost.cn/preview/8d459cb00ae12313/95034a107f3dacc5

## 环境要求

1、PHP >= 7.4

2、"karsonzhang/fastadmin-addons": "~1.3.2",

## 安装说明

> v2.0.1 版本开始不会遵守fastadmin插件开发标准（把第三方类库安装到library目录），改为composer管理依赖。


1、下载该项目，onelogin目录复制到addons

2、安装依赖：composer require yanlongli/php-apple-signin 2.0.2 -vvv

3、安装依赖：composer require yurunsoft/yurun-oauth-login 3.1.0 -vvv

4、进入后台管理系统，配置微信app登录

## 业务流程

1、请求第一步获取state（唯一随机字符串）

2、获取openid

3、绑定新账号或者绑定已存在账号

4、获取新的token

## log

### 1.0.1

支持微信app登录

### 2.0.1

支持 sign in with apple