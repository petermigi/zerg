<?php

namespace app\lib\exception;
use app\lib\exception\BaseException;

class ThemeException extends BaseException
{
    public $code  = 404;
    public $msg = '指定的主题不存在,请检查主题ID';
    public $errorCode = 30000;

}