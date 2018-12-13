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

//获取获取商品信息接口访问url路由
Route::get('api/:version/product/recent','api/:version.Product/getRecent');
//获取获取分类商品信息接口访问url路由
Route::get('api/:version/product/by_category','api/:version.Product/getAllInCategory');

//获取获取分类信息接口访问url路由
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');

//获取获取令牌token接口访问url路由
Route::post('api/:version/token/user','api/:version.Token/getToken');
