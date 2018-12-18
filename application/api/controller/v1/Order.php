<?php

namespace app\api\controller\v1;

use think\Controller;
use app\api\service\Token as TokenService;
use app\lib\exception\TokenException;
use app\lib\exception\ForbiddenException;
use app\lib\enum\ScopeEnum;
use app\api\controller\BaseController;
use app\api\validate\OrderPlace;

class Order extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only'=>'placeOrder']
    ];  

    public function placeOrder()
    {
        (new OrderPlace())->goCheck();
    }
}
