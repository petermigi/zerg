<?php
namespace app\api\controller\v1;

use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use think\Exception;
use app\lib\exception\BannerMissException;

class Banner
{
    /**
        * 功能说明: 获取指定id的banner信息
        * 参数说明:
        * @url  /banner/:id
        * @http  GET
        * @id  banner的id号
        *         
    **/
    public function getBanner($id)
    {
        (new IDMustBePositiveInt())->goCheck();

        
        $banner = BannerModel::getBannerByID($id);  
        
        if(!$banner)
        {
            //throw new BannerMissException();
            throw new Exception('我是内部错误');
        }
        
        return $banner;

    }

        
    
}