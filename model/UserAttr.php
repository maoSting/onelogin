<?php

namespace addons\onelogin\model;

use app\common\model\User;
use think\Model;

/**
 * 第三方登录模型
 */
class UserAttr extends Model
{
    use \traits\model\SoftDelete;
    
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    
    // 微信
    const GROUP_WECHAT = 'wechat';
    const NAME_WECHAT_OPENID = 'openid';
    const NAME_WECHAT_UNIONID = 'unionid';
    
    
    // 苹果登录
    const GROUP_APPLE = 'apple';
    const GROUP_APPLE_OPENID = 'openid';
    
    
    // 设备id
    const GROUP_PLATFORM = 'platform';
    const NAME_PLATFORM_ANDROID = 'android';
    const NAME_PLATFORM_IOS = 'ios';
    
    // 追加属性
    protected $append = [
    
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', [], 'LEFT');
    }
    
    /**
     * @param $value
     * @param $group
     * @param $name
     * @return array|bool|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getValueByGroup($value, $group = self::GROUP_WECHAT, $name = self::NAME_WECHAT_OPENID)
    {
        return $this->where('group', $group)->where('name', $name)->where('value', $value)->find();
    }
    
    /**
     * 获取临时账号
     *
     * @param $openid
     * @param $extend
     * @param $groupName
     * @param $name
     * @return array|bool|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTmpByGroup($openid, $extend, $groupName = self::GROUP_WECHAT, $name = self::NAME_WECHAT_OPENID)
    {
        return $this->where('group', $groupName)->where('name', $name)->where('value', $openid)->where('extend', $extend)->find();
    }
    
    
    /**
     * 保存信息
     *
     * @param $useId
     * @param $extend
     * @param $openid
     * @param $unionId
     * @return array|false|\think\Collection|\think\model\Collection
     * @throws \Exception
     */
    public function createWechat($useId, $extend, $openid, $unionId = null)
    {
        $data[] = [
            'user_id' => $useId,
            'group' => self::GROUP_WECHAT,
            'name' => self::NAME_WECHAT_OPENID,
            'value' => $openid,
            'extend' => $extend
        ];
        if ($unionId) {
            $data[] = [
                'user_id' => $useId,
                'group' => self::GROUP_WECHAT,
                'name' => self::NAME_WECHAT_UNIONID,
                'value' => $unionId,
                'extend' => $extend
            ];
        }
        return $this->saveAll($data);
    }
    
    public function createApple($useId, $openid, $extend = null)
    {
        $data = [
            'user_id' => $useId,
            'group' => self::GROUP_APPLE,
            'name' => self::GROUP_APPLE_OPENID,
            'value' => $openid,
            'extend' => $extend
        ];
        return $this->save($data);
    }
    
    public function getValueByUser($userId, $groupName = self::GROUP_WECHAT, $name = self::NAME_WECHAT_OPENID)
    {
        return $this->where('group', $groupName)->where('name', $name)->where('user_id', $userId)->find();
    }
    
    public function getUserGroup($userId, $group)
    {
        return $this->where('group', $group)->where('user_id', $userId)->find();
    }
    
    public function createUserGroup($userId, $group, $name, $value, $extend = "")
    {
        return self::create([
            'group' => $group,
            'name' => $name,
            'value' => $value,
            'extend' => $extend,
            'user_id' => $userId
        ]);
    }
    
    
    public function updateUserGroup($userId, $group, $name, $value, $extend = "")
    {
        return $this->save([
            'group' => $group,
            'name' => $name,
            'value' => $value,
            'extend' => $extend,
        ], ['user_id' => $userId]);
    }
    
}
