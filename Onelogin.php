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
    }
    
}
