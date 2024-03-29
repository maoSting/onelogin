<?php

namespace addons\onelogin\service;

use fast\Random;
use think\Cache;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;

class WechatService
{
    public static function preview()
    {
        $random = Random::alnum(128);
        Cache::set($random, 1, 24 * 3600);
        return $random;
    }
    
    /**
     * 登录信息
     *
     * @param $params
     * @return array
     * @throws \Yurun\OAuthLogin\ApiException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function login($params)
    {
    
    }
    
    
    /**
     * 绑定信息
     *
     * @param $params
     * @return array
     * @throws \Yurun\OAuthLogin\ApiException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function bind($params)
    {
    
    }
    
    
    /**
     * 绑定已存在用户
     *
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public static function bindExist($params)
    {
    
    }
    
    
    /**
     * 获取用户信息
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserInfo($params)
    {
    
    }
    
    
}