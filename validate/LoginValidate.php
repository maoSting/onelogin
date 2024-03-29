<?php

namespace addons\onelogin\validate;

use think\Validate;

class LoginValidate extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        "wechatapp_code" => 'require',
        "state" => 'require'
    ];
}
