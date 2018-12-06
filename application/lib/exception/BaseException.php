<?php

namespace app\lib\exception;

use think\Exception;


class BaseException extends Exception
{
    //异常对象属性
    public $code = 400; //HTTP 状态码 404,200
    public $msg = '参数错误';  //错误具体信息
    public $errorCode = 10000; //自定义的错误码

    public function __construct($params = [])
    {
        if(!is_array($params))
        {
            return ; //不改写成员的赋值
            //throw new Exception('new一个类的对象初始化参数必须是数组 ');
        }

        if(array_key_exists('code',$params))
        {
            $this->code = $params['code'];
        }

        if(array_key_exists('msg',$params))
        {
            $this->msg = $params['msg'];
        }
        if(array_key_exists('errorCode',$params))
        {
            $this->errorCode = $params['errorCode'];
        }
    }






}
