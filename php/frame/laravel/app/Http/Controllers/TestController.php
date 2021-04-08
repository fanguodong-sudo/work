<?php

namespace App\Http\Controllers;


use http\Env\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class TestController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(){

//        //中间件可以加在这里
//        $this->middleware('checkAge:Mysql');
//
//        //中间件只应该指定的方法上
//        $this->middleware('checkAge:go')->show();
//
//        //排除指定方法
//        $this->middleware('checkAge:elastic')->except('xCsrfToken');

        //注册中间件
        $this->middleware(function ($request,$next){
            $next($request);
            exit(0);
        });

        //

    }

    //资源路由相关控制器
    public function photos(){
        echo 'photosphotosphotos';
    }
    //资源路由相关控制器
    public function video(){
        echo 'videovideovideovideo';
    }


    //--------》 配合路由例子 start
    public function test($user){
        echo $user;
        return view('test', ['user' =>$user]);
    }

    //控制器函数中可以传 $request
    public function show(\Illuminate\Http\Request $request){
        print_r($request->headers);
        return view('test', ['user' =>'show']);
    }
    public function csrfExample(){
        return view('csrf');
    }
    public function xCsrfToken(){
        return view('xCsrfToken');
    }
    //--------》 配合路由例子 end

}
