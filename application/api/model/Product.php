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

    //定义关联模型方法 Product模型和ProductImage模型关联关系: 一对多
    public function imgs()
    {
        return $this->hasMany('ProductImage','product_id','id');
    }

    //定义关联模型方法 Product模型和ProductProperty模型关联关系: 一对多
    public function properties()
    {
        return $this->hasMany('ProductProperty','product_id','id');
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

    public static function getProductDetail($id)
    {
        $product = self::with([
            'imgs' => function($query){
                $query->with(['imgUrl'])
                ->order('order','asc');
            }
        ])
            ->with(['properties'])
            ->find($id);
        return $product;
    }
}