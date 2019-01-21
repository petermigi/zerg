<?php

namespace app\api\service;

use think\Request;
use think\Cache;
use app\lib\exception\TokenException;
use think\Exception;
use app\lib\exception\ForbiddenException;
use app\lib\enum\ScopeEnum;



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

    /**
        *
        *
        * 功能说明:需要用户和CMS管理员都可以访问的权限(基础权限)判断
        * 参数说明:
        * @param  [type] $arg [description]
        * @return [type]      [description]
     */
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');

        if($scope)
        {
            if($scope >= ScopeEnum::User)
            {
                return true;
            }
            else 
            {
                throw new ForbiddenException();
            }
        }
        else 
        {
            throw new TokenException();
        }

    }

    /**
        *
        *
        * 功能说明:只有用户才能访问的接口权限判断
        * 参数说明:
        * @param  [type] $arg [description]
        * @return [type]      [description]
     */
    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');

        if($scope)
        {
            if($scope == ScopeEnum::User)
            {
                return true;
            }
            else 
            {
                throw new ForbiddenException();
            }
        }
        else 
        {
            throw new TokenException();
        }        
    }

     /**
     * 检查操作UID是否合法
     * @param $checkedUID
     * @return bool
     * @throws Exception
     * @throws ParameterException
     */
    public static function isValidOperate($checkedUID)
    {
        if(!$checkedUID){
            throw new Exception('检查UID时必须传入一个被检查的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if($currentOperateUID == $checkedUID){
            return true;
        }
        return false;
    }

    public static function verifyToken($token)
    {
        $exist = Cache::get($token);
        if($exist){
            return true;
        }
        else {
            return false;
        }
    }

}