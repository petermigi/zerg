<?php
namespace app\api\model;
use app\api\model\BaseModel;

class Theme extends BaseModel
{
    protected $hidden = ['delete_time','update_time','topic_img_id','head_img_id'];

    //定义关联模型方法 Theme模型和Image模型关联关系: 一对一 theme为从表 image为主表
    public function topicImg()
    {
        return $this->belongsTo('Image','topic_img_id','id');
    }

    //定义关联模型方法 Theme模型和Image模型关联关系: 一对一 theme为从表 image为主表
    public function headImg()
    {
        return $this->belongsTo('Image','head_img_id','id');
    }

    //定义关联模型方法 Theme模型和Image模型关联关系: 一对一 theme为从表 image为主表
    public function products()
    {
        return $this->belongsToMany('Product','theme_product','product_id','theme_id');
    }

    public static function getThemeWithProducts($id)
    {
        $theme = self::with('products,topicImg,headImg')
            ->find($id);
        return $theme;
    }
}