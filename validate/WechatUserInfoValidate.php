<?php

namespace addons\onelogin\validate;

use think\Validate;

/**
 * 微信
 */
class WechatUserInfoValidate extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        "state" => 'require',
        "openid" => 'require',
    ];
}
