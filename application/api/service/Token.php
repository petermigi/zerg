<?php

namespace app\api\service;

use think\Request;
use think\Cache;
use app\lib\exception\TokenException;
use think\Exception;


class Token
{
    public static function generateToken()
    {
        //32个字符组成一组随机字符串
        $randChars = getRandChar(32);
        //用三组字符串, 进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //salt 盐
        $salt = config('secure.token_salt');

        return md5($randChars.$timestamp.$salt);

    }

    //根据键名获取令牌缓存区里的值,如: uid,openid,scope
    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);

        if(!$vars)
        {
            throw new TokenException();
        }
        else 
        {
            if(!is_array($vars))
            {
                $vars = json_decode($vars, true);
            }

            if(array_key_exists($key, $vars))
            {
                return $vars[$key];
            }
            else 
            {
                throw new Exception('尝试获取的Token变量并不存在');
            }
            
        }
    }

    //获取令牌缓存区里的uid
    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }
}