<?php

namespace app\api\model;

use app\api\model\BaseModel;

class BannerItem extends BaseModel
{
    //黑名单隐藏字段(属性$hidden)
    protected $hidden = ['id','img_id','banner_id','delete_time','update_time'];

    //定义关联模型方法 BannerItem模型和Image模型关联关系: 一对一
    public function img()
    {
        return $this->belongsTo('Image','img_id','id');
    }
}
