<?php
namespace app\api\model;
use app\api\model\BaseModel;

class Product extends BaseModel
{
    protected $hidden = [
        'delete_time', 'main_img_id', 'pivot', 'from', 'category_id',
        'create_time', 'update_time'
    ];

    public function getMainImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }

    //获取最新商品列表信息
    public static function getMostRecent($count)
    {
        $products = self::limit($count)
            ->order('create_time desc')
            ->select();

        return $products;
    }

    //获取分类商品信息
    public static function getProductsByCategoryID($categoryID)
    {
        $products = self::where('category_id','=',$categoryID)
            ->select();
        
        return $products;
    }
}