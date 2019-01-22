<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
//获取banner信息接口访问url路由
Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');

//获取专题列表接口访问url路由
Route::get('api/:version/theme/','api/:version.Theme/getSimpleList');

//获取专题详情页信息接口访问url路由
Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');


//获取分类商品信息接口访问url路由
Route::get('api/:version/product/by_category','api/:version.Product/getAllInCategory');
//获取商品详情信息接口访问url路由
Route::get('api/:version/product/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
//获取商品信息接口访问url路由
Route::get('api/:version/product/recent','api/:version.Product/getRecent');

/* Route::group('api/:version/product', function(){
    //获取分类商品信息接口访问url路由
    Route::get('/by_category','api/:version.Product/getAllInCategory');
    //获取商品详情信息接口访问url路由
    Route::get('/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
    //获取商品信息接口访问url路由
    Route::get('/recent','api/:version.Product/getRecent');
}); */

//获取分类信息接口访问url路由
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');

//获取令牌token接口访问url路由
Route::post('api/:version/token/user','api/:version.Token/getToken');
//令牌token有效性验证接口访问url路由
Route::post('api/:version/token/verify','api/:version.Token/verifyToken');

//用户收货地址添加修改接口访问url路由
Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');
//获取用户收货地址接口访问url路由
Route::get('api/:version/address','api/:version.Address/getUserAddress');

//订单接口访问url路由
Route::post('api/:version/order','api/:version.Order/placeOrder');
//历史订单接口访问url路由
Route::get('api/:version/order/by_user','api/:version.Order/getSummaryByUser');
//订单详情接口访问url路由
Route::get('api/:version/order/:id','api/:version.Order/getDetail',[],['id'=>'\d+']);

//支付接口访问路由
Route::post('api/:version/pay/pre_order','api/:version.Pay/getOrder');
//微信支付回调接口访问路由
Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotify');
//微信支付回调转发接口访问路由
Route::post('api/:version/pay/re_notify','api/:version.Pay/redirectNotify');
