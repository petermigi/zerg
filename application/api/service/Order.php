<?php

namespace app\api\service;

use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\api\model\UserAddress;
use app\api\model\OrderProduct;
use app\api\model\Product;
use think\Db;


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
        $status =$this->getOrderStatus();

        if(!$status['pass'])
        {
            $status['order_id'] = -1;
            return $status;
        }

        //开始创建订单
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }

    private function createOrder($snap)
    {   
        Db::startTrans();
        try
        {
            $orderNo = $this->makeOrderNo();
            $order = new \app\api\model\Order();

            //var_dump($snap);die;
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus'],JSON_UNESCAPED_UNICODE);

            $order->save();            

            $orderID = $order->id;
            $create_time = $order->create_time;

            foreach ($this->oProducts as &$p) 
            {
                $p['order_id'] = $orderID;
            }

            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);

            Db::commit();

            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];

        }
        catch (Exception $ex){
            Db::rollback();
            throw $ex;
        }    
    }

    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    //生成订单快照
    private function snapOrder($status)
    {
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => '',
            'snapName' => '',
            'snapImg' => ''
        ];

        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress(),JSON_UNESCAPED_UNICODE);
        $snap['snapName'] = $this->products[0]['name'];        
        $snap['snapImg'] = $this->products[0]['main_img_url'];

        if(count($this->products) > 1)
        {
            $snap['snapName'] .= '等';
        }

        for ($i = 0; $i < count($this->products); $i++) {
            $product = $this->products[$i];
            $oProduct = $this->oProducts[$i];

            $pStatus = $this->snapProduct($product, $oProduct['count']);
            //$snap['orderPrice'] += $pStatus['totalPrice'];
            //$snap['totalCount'] += $pStatus['count'];
            array_push($snap['pStatus'], $pStatus);
        }
        return $snap;
        
    }

    // 单个商品库存检测
    private function snapProduct($product, $oCount)
    {
        $pStatus = [
            'id' => null,
            'name' => null,
            'main_img_url'=>null,
            'count' => $oCount,
            'totalPrice' => 0,
            'price' => 0
        ];

        $pStatus['counts'] = $oCount;
        // 以服务器价格为准，生成订单
        $pStatus['totalPrice'] = $oCount * $product['price'];
        $pStatus['name'] = $product['name'];
        $pStatus['id'] = $product['id'];
        $pStatus['main_img_url'] =$product['main_img_url'];
        $pStatus['price'] = $product['price'];
        return $pStatus;
    }

    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id','=',$this->uid)
        ->find();

        if(!$userAddress)
        {
            throw new UserException([
                'msg' => '用户收货地址不存在, 下单失败',
                'erroCode' => 60001,
            ]);            
        }

        return $userAddress->toArray();
    }

    public function checkOrderStock($orderID)
    {
        //        if (!$orderNo)
        //        {
        //            throw new Exception('没有找到订单号');
        //        }

        // 一定要从订单商品表中直接查询
        // 不能从商品表中查询订单商品
        // 这将导致被删除的商品无法查询出订单商品来
        $oProducts = OrderProduct::where('order_id', '=', $orderID)
            ->select();        
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        return $status;
    }

    private function getOrderStatus()
    {
        //订单商品的整体状态
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatusArray' => []
        ];

        foreach ($this->oProducts as $oProduct) 
        {
            //oProducts和products 作对比
            //products 从数据库中查询出来
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'],$oProduct['count'], $this->products
            );

            if (!$pStatus['haveStock']) 
            {
                $status['pass'] = false;
            }

            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['counts'];
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
            'counts' => 0,
            'price' => 0,
            'name' => '',
            'totalPrice' => 0,
            'main_img_url' => null
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
            $pStatus['counts'] = $oCount;   
            $pStatus['price'] = $product['price'];   
            $pStatus['main_img_url'] = $product['main_img_url'];   
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
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();

        return $products;
        
    }


}