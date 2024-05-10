<?php

namespace addons\onelogin\controller;

use addons\onelogin\model\UserAttr;
use addons\onelogin\validate\AppleLoginValidate;
use addons\onelogin\validate\WechatBindValidate;
use addons\onelogin\validate\WechatUserInfoValidate;
use app\common\library\Auth;
use app\common\library\Sms;
use app\common\model\User;
use think\Cache;
use AppleSignIn\ASDecoder;

/**
 * sign in with apple
 */
class Apple extends OneLoginBase
{
    protected $noNeedLogin = ['appLogin', 'bind', 'getUserInfo'];
    
    protected $noNeedRight = ['bindExist'];
    
    public function appLogin()
    {
        $params = $this->request->post();
        $validate = validate(AppleLoginValidate::class);
        if (!$validate->check($params)) {
            return $this->error($validate->getError());
        }
        $storeState = Cache::pull($params['state']);
        $cacheState = $storeState !== null ? $params['state'] : null;
        if (empty($cacheState)) {
            return $this->error("state不能为空");
        }
        $clientUser = $params['user'];
        $identityToken = $params['identityToken'];
        
        $appleSignInPayload = ASDecoder::getAppleSignInPayload($identityToken);
        $user = $appleSignInPayload->getUser();
        $isValid = $appleSignInPayload->verifyUser($clientUser);
        if (!$isValid) {
            return $this->error("苹果验证失败");
        }
        
        $userAttr = (new UserAttr())->getValueByGroup($user, UserAttr::GROUP_APPLE, UserAttr::GROUP_APPLE_OPENID);
        $isRegister = false;
        if (empty($userAttr)) {
            (new UserAttr())->createApple(0, $user, $params['state']);
        } else {
            if ($userAttr['user_id'] > 0) {
                $isRegister = true;
            }
            $userAttr->extend = $params['state'];
            $userAttr->save();
        }
        $result = [
            'openid' => $user,
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
        $userAttr = (new UserAttr())->getTmpByGroup($params['openid'], $params['state'], UserAttr::GROUP_APPLE, UserAttr::GROUP_APPLE_OPENID);
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
        
        $userAttr = (new UserAttr())->getTmpByGroup($openid, $state, UserAttr::GROUP_APPLE, UserAttr::GROUP_APPLE_OPENID);
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
        
        $userAttr = (new UserAttr())->getTmpByGroup($openid, $state, UserAttr::GROUP_APPLE, UserAttr::GROUP_APPLE_OPENID);
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
