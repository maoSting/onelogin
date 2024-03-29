<?php

namespace addons\onelogin;

use addons\epay\library\Service;
use app\common\library\Menu;
use think\Addons;
use think\Loader;

/**
 * 插件
 *
 * YurunHttp-5.0.1
 * YurunOAuthLogin-3.1.0
 * http-message-2.0
 * log-1.1.4
 */
class Onelogin extends Addons
{
    
    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }
    
    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        
        return true;
    }
    
    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        
        return true;
    }
    
    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        
        return true;
    }
    
    
    // 模块控制器方法加载开始
    public function appInit()
    {
        //添加命名空间
        if (!class_exists('\Yurun\OAuthLogin\Weixin\OAuth2')) {
            Loader::addNamespace('Yurun\OAuthLogin', ADDON_PATH . 'onelogin' . DS . 'library' . DS . 'YurunOAuthLogin' . DS . 'src' . DS);
        }
        if (!class_exists('\Psr\Log\AbstractLogger')) {
            Loader::addNamespace('Psr\Log', ADDON_PATH . 'onelogin' . DS . 'library' . DS . 'log' . DS . 'Psr' . DS . 'Log' . DS);
        }
        if (!class_exists('\Psr\Http\Message\UriInterface') || !class_exists('\Psr\Http\Message\RequestInterface')) {
            Loader::addNamespace('Psr\Http\Message', ADDON_PATH . 'onelogin' . DS . 'library' . DS . 'http-message' . DS . 'src' . DS);
        }
        if (!class_exists('\Yurun\Util\HttpRequest')) {
            Loader::addNamespace('Yurun\Util', ADDON_PATH . 'onelogin' . DS . 'library' . DS . 'YurunHttp' . DS . 'src' . DS);
        }
    }
    
}
