<?php

namespace app\lib\exception;

use think\Exception;
use think\exception\Handle;
use think\Request;
use think\Log;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;    

    public function render(\Exception $e)
    {            
          
        if($e instanceof BaseException)
        {
            //客户端异常错误响应处理 给客户端返回一个json结构体

            //如果是自定义的异常,不需要异常错误记录日志
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        }else 
        {   //服务端异常错误响应处理 给客户端返回一个json结构体

            //app_debug 异常错误显示调试模式开关 给客户端返回一个html页面
            if(config('app_debug'))
            {   //具体的错误显示页面tp5框架的render方法()
                return parent::render($e);
            }
            else 
            {   //简易错误显示页面json结构体

                //服务器内部错误,需要记录异常错误日志            
                $this->code = 500;
                $this->msg = '服务器内部错误,不想告诉你';
                $this->errorCode = 999;

                //调用自己封装的异常错误记录日志处理方法recordErrorLog            
                $this->recordErrorLog($e);
            }
            
        }

        $request = Request::instance();

        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => $request->url(),

        ];

        return json($result, $this->code);
    }

    private function recordErrorLog(\Exception $e)
    {
        //手动初始化日志配置(因为关闭了自动记录日志)
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error'],
        ]);
        Log::record($e->getMessage(),'error');
    }
}