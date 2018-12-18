<?php

namespace app\api\validate;

use app\api\validate\BaseValidate;
use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{
    /* 
        要验证的数据格式: 模拟数据 其中products字段是我们要验证的复杂数据字段
        $data = [
                    'name'  => 'thinkphp',
                    'age'   => 10,
                    'email' => 'thinkphp@qq.com',
                    products = [
                                    [
                                        'product_id' => 1,
                                        'count' => 3
                                    ],
                                    [
                                        'product_id' => 2,
                                        'count' => 3
                                    ],
                                    [
                                        'product_id' => 3,
                                        'count' => 3
                                    ],
                                ]
                ];
        
    */

    //多个商品的整体验证规则thinkphp5.0框架系统验证规则定义专有属性$rule
    //TP框架会自动加载调用这个$rule属性所定义的验证规则
    protected $rule = [
        'products' =>'checkProducts'
    ];

    //单个商品的验证 我们自定义的属性$singleRule
    //需要我们自己手动加载调用$singleRule 所定义的验证规则
    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger',
    ];

    //订单接口自己独有的验证规则
    protected function checkProducts($values)
    {
        //要验证的products字段的值(客户端提交过来的数据)不是数组的验证错误提示
        if(!is_array($values))
        {
            throw new ParameterException([
                'msg' => '商品参数不正确'
            ]);
        }
        
        //要验证的products字段的值(客户端提交过来的数据)为空的验证错误提示
        if(empty($values))
        {
            throw new ParameterException([
                'msg' => '商品列表不能为空'
            ]);
        }

        /* 
            循环遍历验证多个商品中的每一个商品是否验证通过 
            $values为products字段的数据值
            $value为单个商品数据
        */
        foreach ($values as $value) 
        {
            //验证单个商品 $value为 要验证的单个商品数据 (含字段名和数据值)
            $this->checkProduct($value);
                                  
        }
        return true;
    }


    /*     
        单个商品的验证 $value为 要验证的单个商品数据 (含字段名和数据值)
        例如: [
                'product_id' => 1,
                'count' => 3
              ]
    */
    protected function checkProduct($value)
    {
        //用TP5框架的独立验证的方法来手动加载调用我们自己定义的$singRule属性所定义的验证规则
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if(!$result)
        {
            throw new ParameterException([
                'msg' => '商品列表参数错误'
            ]);
        }

        
    }
}