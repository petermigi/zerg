<?php
namespace app\api\model;
use app\api\model\BaseModel;

class Category extends BaseModel
{
    protected $hidden = [
        'delete_time', 'update_time', 'create_time'
    ];

   //定义关联模型方法 Category模型和Image模型关联关系: 一对一 category为从表 image为主表
   public function img()
   {
       return $this->belongsTo('Image','topic_img_id','id');
   }
    
}