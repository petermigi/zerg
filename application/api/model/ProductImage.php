<?php

namespace app\api\model;

use think\Model;
use app\api\model\BaseModel;

class ProductImage extends BaseModel
{
    protected $hidden = ['img_id', 'delete_time', 'product_id'];

     //定义关联模型方法 ProductImage模型和Image模型关联关系: 一对一 ProductImage为从表 image为主表
     public function imgUrl()
     {
         return $this->belongsTo('Image','img_id','id');
     }
}
