<?php
namespace app\api\controller\v1;

use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use think\Exception;

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

        try
        {
            $banner = BannerModel::getBannerByID($id);
        }
        catch(Exception $ex)
        {
            $err = [
                'error_code' => 10001,
                'msg' => $ex->getMessage() 
            ];

            return json($err,400);
        }
        
        return $banner;

    }

        
    
}