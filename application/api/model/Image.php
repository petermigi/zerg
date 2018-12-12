<?php

namespace app\api\model;

use think\Model;
use app\api\model\BaseModel;

class Image extends BaseModel
{
    //黑名单隐藏字段(属性$hidden)
    protected $hidden = ['id','from','delete_time','update_time'];

    //图片完整url路径处理 读取器
    public function getUrlAttr($value,$data)
    {
        return $this->prefixImgUrl($value,$data);
    }
}
