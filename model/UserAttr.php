<?php

namespace addons\onelogin\model;

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
    
    const GROUP_WECHAT = 'wechat';
    const NAME_WECHAT_OPENID = 'openid';
    const NAME_WECHAT_UNIONID = 'unionid';
    
    
    // 追加属性
    protected $append = [
    
    ];
    
    public function user()
    {
        return $this->belongsTo('\app\common\model\User', 'user_id', 'id', [], 'LEFT');
    }
    
    /**
     * 获取登录信息
     *
     * @param $openid
     * @return array|bool|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWechat($openid)
    {
        return $this->where('group', self::GROUP_WECHAT)->where('name', self::NAME_WECHAT_OPENID)->where('value', $openid)->find();
    }
    
    /**
     * 获取临时账号
     *
     * @param $openid
     * @param $extend
     * @return array|bool|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTmpWechat($openid, $extend)
    {
        return $this->where('group', self::GROUP_WECHAT)->where('name', self::NAME_WECHAT_OPENID)->where('value', $openid)->where('extend', $extend)->find();
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
    
    public function getUserWechatOpenid($userId)
    {
        return $this->where('group', self::GROUP_WECHAT)->where('name', self::NAME_WECHAT_OPENID)->where('user_id', $userId)->find();
    }
    
    public function updateAttr($userId, $group, $name, $value, $extend)
    {
    
    }
    
}
