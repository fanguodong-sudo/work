<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\TestRouteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//根目录
Route::get('/', function () {
    return view('welcome');
})->name('/');

//hello demo
Route::get('hello', function () {
    return 'Hello Laravel!';
});

//login
Route::get('/login', function () {
    return 'login!';
})->name("/login");

//post
Route::post('profile/login', function () {
    return 'login!';
});

//get 传餐数|路由控制器
Route::get('test/{user}', [TestController::class, 'test']);


//中间件
Route::get('profile/{user}', [TestController::class, 'show'])->middleware('checkAge',function(){

});

//中间件
Route::post('profile2/{user}', [TestController::class, 'show'])->middleware('auth');

//----正则约束 start
Route::get('user/{name}', function ($name) {
    // $name 必须是字母且不能为空
})->where('name', '[A-Za-z]+');

Route::get('user/{id}', function ($id) {
    // $id 必须是数字
})->where('id', '[0-9]+');

Route::get('user/{id}/{name}', function ($id, $name) {
    // 同时指定 id 和 name 的数据格式
})->where(['id' => '[0-9]+', 'name' => '[a-z]+']);
//----正则约束 end

//可选参数
Route::get('user/{name?}', function ($name = null) {
    return $name;
});
Route::get('user/{name?}', function ($name = 'John') {
    return $name;
});

//子域名路由 todo（未验证）
Route::domain('{account}.blog.test')->group(function () {
    Route::get('user/{id}', function ($account, $id) {
        return 'This is ' . $account . ' page of User ' . $id;
    });
});

//路由前缀
Route::prefix('admin')->group(function () {
    Route::get('users', function () {
        // Matches The "/admin/users" URL

        return "admin/users";
    });
});

//路由名称前缀
Route::name('admin.')->group(function () {
    Route::get('users', function () {
        // 新的路由名称为 "admin.users"...
    })->name('users');
});

//路由传递model（感觉用途不大，未验证）todo 先留着，之后验证
Route::get('users/{user}', function (\App\Models\User $user) {
    return $user;
});

//兜底路由
Route::fallback(function () {
    return 'fallback 兜底路由';
});

//访问频率限制
//配置例子在 app/Providers/RouteServiceProviders.php的 configureRateLimiting 中
//可以在例子中使用自定义函数来判断是否使用现在回去

//应用频率限制器到路由
Route::middleware(['throttle:example'])->group(function (){
    Route::get('/audio',function (){
        return 'audioaudioaudioaudio';
    });
    Route::get('/video',function (){
       return "videovideovideovideovideovideo";
    });
});

//表单 (小试一下，后面有更详细的)
Route::get('/csrf',[TestRouteController::class,'csrf'])->name('csrf');

//访问当前路由
Route::get('currentRoute1',function (){
    $route = Route::current();
    echo Route::currentRouteName();
    echo Route::currentRouteAction();
    echo $route->getActionMethod();
    //类Route静态方法和普通方法保存这路由的相关信息，有需要可以查看
    //控制器中不可使用
});

//跨域资源共享（CORS）
//跨域调取接口，获取js，css，image文件 都需要设置CORS，修改config/cors.php 中，（其实就是
//配置响应头部）

//
Route::permanentRedirect('/aa', '/');

Route::delete('/delete', function (){
    echo 'delete';
});
Route::options('/options', function (){
    echo 'delete';
});

//全局约束
//全局约束 需要app/Provider/RouteServiceProvider.php 的boot函数中
//function boot(){
//    Route::pattern('id', '[0-9]+');
//}

Route::get('test', function (){
    return view('testRoute',['user'=>'Tom']);
})->name("test");

//match
Route::match(['get','post'],'/match',function (){
    return "get and post";
});

//any
Route::any('/any', [TestController::class, 'show'])->name("/any");;

//redirect
Route::get('redirect', function() {
    // 通过路由名称进行重定向
    //return redirect()->route('/login');

    $url = route('/');
    echo $url;
    // 生成重定向
    return redirect()->route('/');
});

//redirect 控制器
Route::get('redirectC', [\App\Http\Controllers\TestRouteController::class,'index']);

//自定义中间件 app/Middleware/CheckAge.php

//文件需要放在app/Middleware中，固定函数 handle 执行代码之前执行，terminate 在执行代码之后执行

//注册中间件

//将做好的中间件添加到app/Http/Kernel.php的$middleware数组中

//注册好的中间件不能直接应用到页面上,需要在页面上指定
Route::get('/middle',function(){
    echo "指定中间件";
})->middleware('checkAge');

Route::get('/middle2',function(){
    echo "指定中间件";
})->middleware(\App\Http\Middleware\CheckAge::class);

//分配多个中间件
Route::get('/middleM',function(){
    echo "指定中间件";
})->middleware('checkAge','auth');

//指定url跳过指定中间件
//跳过的中间件不能拥有handle函数
Route::middleware([\App\Http\Middleware\CheckAge::class])->group(function (){
    Route::get('/without/example',function(){
        echo '/without/example';
    });
    Route::get('/without/example2',function(){
        echo '/without/example2';
    })->withoutMiddleware([\App\Http\Middleware\CheckAge::class]);
});

//web、api、全局有不同的配置数组
//全局 Kernel.php $middleware(全局数组),$middlewareGroups(web、api)

//中间件传餐数, checkAge接餐有示例
Route::get('post/{id}', function ($id) {
    echo 'post/id';
})->middleware('checkAge:Tomcat');

//csrf防御 中间件VerifyCsrfToken自动验证token
Route::get('/csrf/example1',[TestController::class,'csrfExample']);
//app/middleware/VerifyCsrfToken.php $except 里可以排除url，排除正则表达式

//X-CSRF-Token 完整例子
Route::get('/xCsrfToken',[TestController::class,'xCsrfToken']);

//x-XSRF-Token laravel 会把XSRF-Token保存到Cookie里，不需要手动设置
//一些JavaScript框架，比如Angular和Axios。


//资源路由 todo 未成功，不知道干什么用的
//资源路由 单个
Route::resource('photos',TestController::class);

//资源路由 多个（数组）
Route::resources([
    'photos' => TestController::class,
    'video'  => TestController::class
]);

//部分资源路由
Route::resource('photos',TestController::class,['only'=>['video','photos']]);
Route::resource('video',TestController::class,['except'=>['show']]);

//api资源路由
//Route::apiResource('video',\App\Http\Controllers\TestController::class,['only'=>['video','photos']]);

//请求
Route::prefix('request')->get('update/{id}',[\App\Http\Controllers\requestController::class,'update']);

//闭包访问请求
Route::prefix('request')->get('clause',function (\Illuminate\Http\Request $request){

    echo "当前path：".$request->path()."<br />";

    //url 正则匹配
    if($request->is('request/*')){
        echo "匹配通过！".PHP_EOL;
    }

    //获取头部信息
    //print_r($request->headers);

    //判断提交方式
    if($request->isMethod('get')){
        echo 'isMethod is get !'.PHP_EOL;
    }

    //获取请求输入
    echo '<br />';
    $input = $request->all();
    print_r($input);
    echo '<br />';

    //获取指定参数
    $name = $request->input('name');
    echo $name.PHP_EOL;

    //获取指定参数,没有显示默认值
    $name = $request->input('name','Tom');
    echo $name.PHP_EOL;
});

//闭包访问请求
Route::prefix('request')->post('clause1',function (\Illuminate\Http\Request $request){


//    //获取全部请求参数
//    $input = $request->all();
//    print_r($input);
//    echo '<br />';
//
//    //获取全部请求参数的灵一中方法
//    $input = $request->input();
//    print_r($input);
//    echo '<br />';
//
//
//    $name = $request->name;
//    echo $name.'<br />';



    //获取当前url两个方法
    echo $request->url().'<br />';
    echo $request->fullUrl().'<br />';

    //自定义参数拼接URL
    echo $request->fullUrlWithQuery(['name'=>'Tom']).'<br />';

    //获取头部信息，没有使用默认值
    echo $request->header('X-Header-Name').'<br />';
    echo "bb:".$request->header('bb').'<br />';
    echo $request->header('X-Header-Name','default').'<br />';

    //获取bearerToken todo (未验证)
    echo $request->bearerToken().'<br />';

    //获取ip
    echo $request->ip().'<br />';

    //经由accept header 获取content type
    $contentType = $request->getAcceptableContentTypes();
    print_r($contentType);


    //判断接收类型，安全使用
    if($request->accepts(['text/html','application/json'])){
        echo "接收类型为：text/html,application/json".'<br />';
    }else{
        echo "接收类型非text/html,application/json".'<br />';
    }

    //首选接收类型，如果是类型为首选，返回true，非首选选择false
    if($request->prefers(['text/html','application/json'])){
        echo "接收类型为首选 text/html:application/json<br />";
    }else {
        echo "接收类型为非首选 text/html,application/json<br />";
    }

    //是否期待响应json
    if($request->expectsJson()){
        echo "expects is true<br />";
    }else{
        echo "expects is false<br />";
    }

    $all = $request->input();
    print_r($all);


});

Route::prefix('request')->post('clause2',function (\Illuminate\Http\Request $request){

//    $all = $request->input();
//    print_r($all);
//
//    //查询参数并设置默认值
//    echo $request->query('dd','dddddd');
//
//    //json  将Content-Type设置为application/json
//    echo $request->input('user.name');
//
//    //获取bool
//    $archived = $request->boolean('user.archived');
//    var_dump($archived);

//    //另类获取参数的形式
//    $users = $request->users;
//    print_r($users);
//
//    //仅仅和排除
//    $input = $request->only(['username', 'password']);
//    $input = $request->only('username', 'password');
//    $input = $request->except(['credit_card']);
//    $input = $request->except('credit_card');
//
//    //是否存在
//    if ($request->has('name')) {
//        echo 'you name--';
//    }else{
//        echo 'wu name--';
//    }
//
//    //如果存在
//    $request->whenHas('users',function (){
//        echo 'you users--';
//    });

//    //多个参数存在
//    $request->whenHas(['users','city'],function (){
//        echo 'I have users and city-------';
//    });
//
//    //有任何一个执行
//    if($request->hasAny(['users','email'])){
//        echo 'you  users or email! ';
//    }

    //存在且不为空
    if($request->filled('users')){
        echo 'have users------';
    }else{
        echo 'no users-----';
    }

    //存在且不为空时执行
    $request->whenFilled('users',function ($input){
        echo 'have have users !---';
        print_r($input); //全部参数
    });

    //如果不存在
    if ($request->missing('name')) {
        echo 'name missing-------';
    }else{
        echo 'name get';
    }
});

Route::get('/oldTest',function (\Illuminate\Http\Request $request){

//    //将全部请求参数存入临时session old('name') 调用，仅持续到下一次请求
//    $request->flash();
//
//    //仅username和password刷入临时session
//    $request->flashOnly(['username','password']);
//
//    //除了username和password其他的参数输入临时session
//    $request->flashExcept(['username','password']);
//
//    //PHP中获取临时session的值
//    $username = $request->old('username');

    //获取cookie
    $cookList = $request->cookie();
    print_r($cookList);

    //获取单个cookie
    $laravel_session = $request->cookie('laravel_session');
    print_r($laravel_session);


    return view('oldTest',['username'=>'blade']);
});

//上传图片
Route::get('/upload',function (\Illuminate\Http\Request $request){

    $file = $request->file('photo');
    $file = $request->photo;

    //如果photo存在
    if($request->hasFile('photo')){
        echo 'have photo ';
    }

    //验证是否上次成功
    if($request->file('photo')->isValid()){
        echo 'valid success!';
    }

    //获取文档地址和后缀名
    $path = $request->photo->path();
    $extension = $request->photo->extension();

    //
    $path = $request->photo->store('images');
    $path = $request->photo->store('images', 's3');

    //
    $path = $request->photo->storeAs('images', 'filename.jpg');
    $path = $request->photo->storeAs('images', 'filename.jpg', 's3');

});

//配置信任代理，在中间件TrustProxies中配置
//返回结果
Route::get('/response',function (\Illuminate\Http\Request $request){

    //返回结果设置http code 和header信息
//    return response('Hello world',404)
//        ->header('Content-Type','text/plain');

    //设置头部数组
//    return response('world')
//        ->withHeaders([
//            'Content-Type' => 'text/html',
//            'X-Header-One' => 'Header Value',
//            'X-Header-Two' => 'Header Value',
//        ]);

    //返回json
//    return response()->json([
//        'name' => 'Abigail',
//        'state' => 'CA'
//    ]);

    //返回jsonp
    //return response()->jsonp('callback',['aa'=> 'aa','bb'=>'bb']);
//    return response()->json([
//        'bb'=>'bb',
//        'ee'=>'ee'
//    ])->withCallback($request->input('callback'));

    //下载文件
//    $pathToFile = 'https://www.baidu.com/img/PCtm_d9c8750bed0b3c7d089fa7d55720d6cf.png';
//    $name = 'aa.jpg';
//    $header = [
//        'Content-type'=>'text/html'
//    ];
//    //return response()->download($pathToFile);
//    return response()->download($pathToFile,$name,$header);

    //定义返回信息宏
    //需要将宏写入Providers的 AppServiceProvider.php 中的boot函数中，
    return response()->caps('hello tom');

    //todo 缓存头部信息中间件

});

//viewTest
Route::get('/view',function (){
    return view('viewTest',['name'=>'Tom']);
});

Route::get('/admin/view',function (){
    return view('admin.view',['name'=>'images']);
});

Route::get('/admin/view1',function (){
    $user = new stdClass();
    $user->name = 'tom';
    $user->age = 18;

    $user2 = new stdClass();
    $user2->name = 'marry';
    $user2->age = 20;


    $user3 = new stdClass();
    $user3->name = 'john';
    $user3->age = 40;

    return view('admin.view')->with('name','jerry')
        ->with('records',['aa'=>['name'=>'bb'],'bb'=>['name'=>'cc']])
        ->with('i',1)
        ->with('boolean',true)
        ->with('users',[$user,$user2,$user3])
        ;
});


//所有view共享变量
//在app/Providers/AppServiceProvider.php boot函数









