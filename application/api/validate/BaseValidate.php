<?php
namespace app\api\validate;
use think\Validate;
use think\Request;
use think\Exception;
use app\lib\exception\ParameterException;

class BaseValidate extends Validate
{

    public function goCheck()
    {
        $request = Request::instance();

        $params = $request->param();

        $result = $this->batch() ->check($params);

        if(!$result)
        {
            //验证层参数错误 需要一个json结构体,并有具体的异常错误信息
            //要抛出一个自定义的异常错误类对象继承于BaseException类
            $e = new ParameterException([
                'msg' => $this->error                
            ]);            
            throw $e;                             
        }
        else 
        {
            return true;
        }
    }

    //验证参数是否是正整数
    protected function isPositiveInteger($value, $rule ='', $data = '', $field ='')
    {
        if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0)
        {
            return true;            
        }
        else 
        {
            return false;
            //return $field.'必须是正整数';
        }
    }
}
