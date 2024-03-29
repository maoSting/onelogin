<?php

namespace addons\onelogin\controller;

use addons\onelogin\constant\OneLoginConstant;
use addons\onelogin\model\UserAttr;
use addons\onelogin\service\WechatService;
use addons\onelogin\validate\LoginValidate;
use addons\onelogin\validate\WechatBindValidate;
use addons\onelogin\validate\WechatUserInfoValidate;
use app\common\library\Auth;
use app\common\library\Sms;
use app\common\model\User;
use think\Cache;

class Wechat extends OneLoginBase
{
    protected $noNeedLogin = ['preview', 'appLogin', 'bind', 'getUserInfo'];
    
    protected $noNeedRight = ['bindExist'];
    
    public function preview()
    {
        return $this->jsonSuccess(['state' => WechatService::preview()]);
    }
    
    public function appLogin()
    {
        $params = $this->request->post();
        $validate = validate(LoginValidate::class);
        if (!$validate->check($params)) {
            return $this->error($validate->getError());
        }
        $wechatAppConfig = get_addon_config(OneLoginConstant::PLUGIN_NAME);
        if (empty($wechatAppConfig['wechat_app_app_id']) || empty($wechatAppConfig['wechat_app_app_secret'])) {
            return $this->error("配置信息失败");
        }
        $oauth = new \Yurun\OAuthLogin\Weixin\OAuth2($wechatAppConfig['wechat_app_app_id'], $wechatAppConfig['wechat_app_app_secret']);
        $storeState = Cache::pull($params['state']);
        $cacheState = $storeState !== null ? $params['state'] : null;
        $accessToken = $oauth->getAccessToken($cacheState, $params['wechatapp_code'], $params['state']);
        if (empty($accessToken)) {
            return $this->error("微信获取信息失败");
        }
        $wechatUser = $oauth->getUserInfo($accessToken);
        if (empty($wechatUser)) {
            return $this->error("微信获取用户信息失败");
        }
        $userAttr = (new UserAttr())->getWechat($wechatUser['openid']);
        $isRegister = false;
        if (empty($userAttr)) {
            (new UserAttr())->createWechat(0, $params['state'], $wechatUser['openid'], null);
        } else {
            if ($userAttr['user_id'] > 0) {
                $isRegister = true;
            }
            $userAttr->extend = $params['state'];
            $userAttr->save();
        }
        $result = [
            'openid' => $wechatUser['openid'],
            'is_register' => $isRegister,
        ];
        return $this->jsonSuccess($result);
    }
    
    
    public function bind()
    {
        $params = $this->request->post();
        $validate = validate(WechatBindValidate::class);
        if (!$validate->check($params)) {
            return $this->error($validate->getError());
        }
        $mobile = $params['mobile'];
        $code = $params['code'];
        $ret = Sms::check($mobile, $code, 'bind');
        if (!$ret) {
            return $this->error("验证码错误");
        }
        
        $user = User::getByMobile($mobile);
        if (empty($user) || $user->status != 'normal') {
            return $this->error('不存在该账号或者该账号已被锁定');
        }
        $userAttr = (new UserAttr())->getTmpWechat($params['openid'], $params['state']);
        if (empty($userAttr) || !empty($userAttr['user_id'])) {
            return $this->error('操作流程错误');
        }
        $userAttr->user_id = $user['id'];
        $userAttr->save();
        
        return $this->jsonSuccess([
            'user_id' => $user['id'],
        ]);
    }
    
    
    public function bindExist()
    {
        $params = $this->request->post();
        $validate = validate(WechatUserInfoValidate::class);
        if (!$validate->check($params)) {
            return $this->error($validate->getError());
        }
        $userInfo = $this->auth->getUser();
        $state = $params['state'];
        $openid = $params['openid'];
        
        $userAttr = (new UserAttr())->getTmpWechat($openid, $state);
        if (empty($userAttr) || !empty($userAttr['user_id'])) {
            return $this->error("操作流程错误");
        }
        $userAttr->user_id = $userInfo['id'];
        $result = $userAttr->save();
        return $result == true ? $this->jsonSuccess([]) : $this->jsonError([], "绑定失败！");
    }
    
    public function getUserInfo()
    {
        $params = $this->request->post();
        $validate = validate(WechatUserInfoValidate::class);
        if (!$validate->check($params)) {
            return $this->error($validate->getError());
        }
        $state = $params['state'];
        $openid = $params['openid'];
        
        $userAttr = (new UserAttr())->getTmpWechat($openid, $state);
        if (empty($userAttr) || empty($userAttr['user_id'])) {
            return $this->error("操作流程错误");
        }
        $auth = Auth::instance();
        $ret = $auth->direct($userAttr['user_id']);
        if (!$ret) {
            return $this->error("操作流程错误");
        }
        return $this->jsonSuccess(['userinfo' => $auth->getUserinfo()]);
    }
}
