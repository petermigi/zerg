<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    //图片完整url路径处理 
    protected function prefixImgUrl($value,$data)
    {
        $finalUrl = $value;

        if($data['from'] ==1 )
        {
            $finalUrl = config('setting.img_prefix').$value;
        }

        return $finalUrl;
    }
}
