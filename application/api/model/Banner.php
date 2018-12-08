<?php
namespace app\api\model;

use think\Exception;
use think\Db;

class Banner
{
    public static function getBannerByID($id)
    {
        $result = Db::table('banner_item')        
        ->where(function($query) use ($id){
            $query->where('banner_id','=',$id);
        })
        ->select();
        
        return $result;


    }

}