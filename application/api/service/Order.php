<?php

namespace app\api\service;

class Order
{
    //订单的商品的列表,也就是客户端传递过来的products参数
    protected $oProducts;

    //真实的商品信息(包括库存量)
    protected $products;
    protected $uid;

    public function place($uid, $oProducts)
    {
        //oProducts和products 作对比
        //products 从数据库中查询出来
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;
    }

    //根据订单信息查找真实的商品信息
    private function getProductsByOrder($oProducts)
    {
        $oPIDs = [];
        foreach ($oProducts as $item) 
        {
            array_push($oPIDs, $item['product_id']);
        }
        //真实的商品信息
        $products = Product::all($oPIDs)
            ->visiable(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();

        return $products;
        
    }


}