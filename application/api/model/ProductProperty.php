<?php

namespace app\api\model;

use think\Model;
use app\api\model\BaseModel;

class ProductProperty extends BaseModel
{
    protected $hidden = ['product_id', 'delete_time', 'id'];
     
}
