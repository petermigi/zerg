<?php

namespace app\api\service;

use app\lib\exception\OrderException;


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

    private function getOrderStatus()
    {
        //订单商品的整体状态
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'pStatusArray' => []
        ];

        foreach ($this->oProducts as $oProduct) 
        {
            //oProducts和products 作对比
            //products 从数据库中查询出来
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'],$oProduct['count'], $this->product
            );

            if (!$pStatus['haveStock']) 
            {
                $status['pass'] = false;
            }

            $status['orderPrice'] += $pStatus['totalPrice'];
            array_push($status['pStatusArray'], $pStatus);
        }
        return $status;
    }

    private function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex = -1;
        
        //单个商品的库存量状态
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'name' => '',
            'totalPrice' => 0
        ];

        for($i=0; $i<count($products); $i++) 
        {
            //在服务器端找到客户端提交过来的商品id在真实商品数组中商品的索引序号
            if($oPID == $products[$i]['id'])
            {
                $pIndex = $i;
            }           
        }

        if($pIndex == -1)
        {
            //客户端传递过来的product_id有可能根本不存在
            throw new OrderException([
                'msg' => 'id为'.$oPID.'商品不存在, 创建订单失败'
            ]);
        }
        else 
        {
            //用索引序号得到真实商品数组中的单个商品的信息
            $product = $products[$pIndex];

            //获得单个真实商品的是否有库存的状态
            $pStatus['id'] = $product['id'];   
            $pStatus['name'] = $product['name'];   
            $pStatus['count'] = $oCount;   
            $pStatus['totalPrice'] = $product['price'] * $oCount;
                                    
            if($product['stock'] - $oCount >= 0)
            {
                $pStatus['haveStock'] = true;
            }    
        }

        return $pStatus;

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