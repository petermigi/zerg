<?php
namespace app\api\model;

use think\Exception;
use think\Db;
use app\api\model\BaseModel;

class Banner extends BaseModel
{
    //黑名单隐藏字段(属性$hidden)
    protected $hidden = ['delete_time','update_time'];

    //定义关联模型方法 Banner模型和BannerItem模型关联关系: 一对多
    public function items()
    {
        return $this->hasMany('BannerItem','banner_id','id');
    }
    public static function getBannerByID($id)
    {
        $banner= self::with(['items','items.img'])->find($id);
        return $banner;  

    }

}