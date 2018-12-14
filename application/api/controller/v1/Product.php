<?php

namespace app\api\controller\v1;

use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\lib\exception\ProductException;
use app\api\validate\IDMustBePositiveInt;

class Product
{
    //获取最近新品
    public function getRecent($count=15)
    {
        (new Count())->goCheck();
        $products = ProductModel::getMostRecent($count);

        if($products->isEmpty())
        {
            throw new ProductException();
        }       

        $products = $products->hidden(['summary']);
        return $products;
    }

    //获取分类商品信息
    public function getAllInCategory($id)
    {
        (new IDMustBePositiveInt())->goCheck();

        $products = ProductModel::getProductsByCategoryID($id);

        if($products->isEmpty())
        {
            throw new ProductException();
        }

        $products = $products->hidden(['summary']);
        return $products;
    }

    public function getOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);

        if(!$product)
        {
            throw new ProductException();
        }

        return $product;
    }
}