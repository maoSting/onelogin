<?php

namespace addons\onelogin\validate;

use think\Validate;

/**
 * 微信绑定
 */
class WechatBindValidate extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'mobile' => 'require',
        "code" => 'require',
        "state" => 'require',
        "openid" => 'require',
    ];
}
