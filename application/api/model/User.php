<?php

namespace app\api\model;

class User extends BaseModel
{
    //定义关联模型方法 User模型和UserAddress模型关联关系: 一对一 user为主表 user_address为从表
    public function address()
    {
        return $this->hasOne('UserAddress', 'user_id', 'id');
    }

    public static function getByOpenID($openid)
    {
        $user = self::where('openid', '=', $openid)
            ->find();
        return $user;
    }
}