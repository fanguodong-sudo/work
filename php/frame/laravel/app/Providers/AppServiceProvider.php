<?php

namespace App\Providers;

use App\View\Components\Alert;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //宏示例
        Response::macro('caps',function ($value){
            return Response::make(strtoupper($value));
        });

        //全view共享
        View::share('key','value');

        //自定义组件
        Blade::component('package-alert',Alert::class);
    }
}
