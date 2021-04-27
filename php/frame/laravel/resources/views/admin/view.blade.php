1. 普通渲染变量：@{{ $name }}：{{ $name }}<br />
2. 渲染全视图应用变量（需要在boot函数中定义）：@{{ $key }}:{{ $key }}<br />
3. 简单的php函数应用 @{{ date('Y-m-d H:i:s') }} ： {{ date('Y-m-d H:i:s') }} <br />
4. json的两种表示方法：
@{{ json_encode([
    'aa' => 'bb'
]) }}
@@json([
    'aa' => 'bb'
],JSON_FORCE_OBJECT)<br />

5.@加双大括号来表示原样显示:
    Hello, @{{ name }}.
    @@json() @json([]) <br />


6. 大段落的不解析使用verbatim关键字<br />
@verbatim
    @verbatim<br />
        hello, {{ $name  }}
    @@endverbatim<br />
@endverbatim<br />
7. if条件语句：<br />
@@if (count($records) === 1)<br />
    // one record <br />
@@elseif (count($records) > 1)<br />
    // multiple records!<br />
@@else<br />
    // any records<br />
@@endif<br />
<br />
@if (count($records) === 1)
echo one record
@elseif (count($records) > 1)
echo  multiple records!
@else<br />
echo  any records
@endif<br />
<h5>8. if的另一种方式unless</h5><br />
@@unless(false)<br />
    You are not signed in<br />
@@endunless<br />
@unless(false)
    You are not signed in
@endunless

<h5>9. 判空的两个函数isset和empty的用法</h5><br />
@@isset($records)<br />
    // records is defined and is not null...<br />
@@endisset<br />
@isset($records)
    // records is defined and is not null...
@endisset
-----------------------<br />
@@empty([])<br />
    // [] is empty<br />
@@endempty<br />
@empty([])
    // [] is empty
@endempty

<h5>9. 验证判断是否是当前登录用户还是游客</h5>
@@auth<br />
    //The user is auth;<br />
@@endauth<br />
@@guest<br />
    //The user is guest;<br />
@@endguest<br />
@auth
    //The user is auth;
@endauth
@guest
    //The user is guest;
@endguest <br />
-----判断是否是指定用户?(未成功，需要定义admin auth guard 不知道如何定义)<br />
{{--@auth('admin')--}}
{{--    //The user is admin--}}
{{--@endauth--}}
{{--@guest('admin')--}}
{{--    //The guest is admin--}}
{{--@endguest--}}
<h5>10. 获取环境变量，是否是正式环境(//todo 未完成)</h5>
@@production<br />
    //正式环境<br />
@@endproduction<br />
@production
    //正式环境
@endproduction
@env('APP_KEY')
    echo 'staging';
@endenv

<h5>11. 定义section</h5>
@@section('test')<br />
    test section<br />
@@endsection<br />
@section('test')
    test section
@endsection
-------------判断test section是否存在<br />
@@hasSection('test')<br />
    has test<br />
@@endif<br />
@hasSection('test')
    has test
@endif
<h5>12. sectionMissing 不包含section时触发</h5>
@@sectionMissing('test1')<br />
//no test11<br />
@@endif<br />
@sectionMissing('test1')
//no test1
@endif

<h5>13. switch 用法</h5>
@@switch($i)<br />
    @@case(1)<br />
        first case <br />
    @@break<br />
    @@case(2)<br />
        second case<br />
    @@break<br />
    @@default<br />
        default case<br />
    @@break<br />
@@endswitch<br />
@switch($i)
    @case(1)
        first case
        @break
    @case(2)
        Second case
        @break
    @default
        default case
        @break
@endswitch

<h5>14. loops 用法</h5>
@@for($i=1;$i<10;$i++)
    {{ $i }} ----
@@endfor
@for($i=1; $i<10;$i++)
    {{ $i }} ----
@endfor<br />

@verbatim
@foreach($records as $k => $r)<br />
    K:{{ $k }} V:{{ $r['name'] }}<br />
@endforeach<br />
@endverbatim

@foreach($records as $k=>$r)
    K:{{ $k }} V:{{ $r['name'] }}
@endforeach<br />

-----------解析对象<br />
@@foreach($users as $user)<br />
    @{{ $user->name }}<br />
@@endforeach<br />
@foreach($users as $user)
    {{ $user->name }}
@endforeach

<h5>16. forelse 用法 与for不同，forelse有 empty分支，如果数组为空执行判空分支</h5>
@@forelse($users as $user)<br />
    {{ $user->name }}<br />
@@empty<br />
    No Users<br />
@@endforelse<br />
@forelse($users as $user)
    {{ $user->name }}
@empty
    No Users
@endforelse
----------------@@while用法(未使用，用其他的吧)<br />
@@while($i==1)
    code...
@@endwhile
<h5>17. foreach 中使用if </h5>
@@foreach($users as $user)<br />
    @@if ($user->age == 18)<br />
        continue;<br />
        @@continue;<br />
    @@endif<br />
    @@if ($user->age == 20)<br />
        break;<br />
        @@break;<br />
    @@endif<br />
@@endforeach<br />
@foreach($users as $user)
    @if ($user->age == 18)
        continue-----
        @continue
    @endif
    @if ($user->age == 20 )
        break
        @break
    @endif
@endforeach
<br />-------------------------另一种continue，break写法<br />
@@foreach($users as $user)<br />
    @@continue($user->age == 18)<br />
    @{{ $user->name }}<br />
    @@break($user->age == 20)<br />
@@endforeach<br />
@foreach($users as $user)
    @continue($user->age==18)
    {{ $user->name }}
    @break($user->age == 20)
@endforeach
<h5>18. foreach 中使用 @loop </h5>
@@foreach($users as $user)<br />
    @@if($loop->first)<br />
        This is the first iteration<br />
    @@endif<br />
    @@if($loop->last)<br />
        This is the last iteration<br />
    @@endif<br />
    This is user @{{ $user->name }}<br />
@@endforeach<br />
@foreach($users as $user)
    @if($loop->first)
        This is the first iteration
    @endif
    @if ($loop->last)
        This is the last iteration
    @endif
    <p> This is user {{ $user->name }}</p>
@endforeach

<h5>19. foreach 中使用 @loop更多用法 </h5>
@foreach($users as $user)
    获取索引值（从0开始）:$loop->index:{{ $loop->index }} <br />
    获取索引值（从1开始）:$loop->iteration:{{ $loop->iteration }} <br />
    所剩循环体数量：$loop->remaining:{{ $loop->remaining }} <br />
    总循环数：$loop->count:{{$loop->count}} <br />
    当第一次循环为true：$loop->first:{{ (int)$loop->first }} <br />
    当最后一次循环为true：$loop->last:{{ (int)$loop->last }} <br />
    不是第一次或最后一次时为true：$loop->even:{{ (int)$loop->even }} <br />
    是第一次或最后一次时为true:$loop->even:{{ (int)$loop->odd }} <br />
    循环深度（嵌套几层）：$loop->depth:{{ $loop->depth }} <br />
    父循环体：$loop->parent：{{ $loop->parent }}
    <br />
@endforeach

<h5>20. 注释 </h5>
@{{-- This comment will not be present in the rendered HTML --}}

<h5>21. 包含view </h5>
@@include('admin.includeTest')<br />
@include('admin.includeTest')<br />
--------------传递变量<br />
@@include('admin.includeTest',['name' => 'cataaaaa'])<br />
@include('admin.includeTest',['name' => 'cataaaaa'])

<h5>22. 存在就包含 </h5>
@@includeIf('admin.includeTest',['name'=>'if name'])<br />
@@includeIf('admin.test')<br />

@includeIf('admin.includeTest',['name' => 'if name'])
@includeIf('admin.test')

<h5>23. 根据变量确认包含 </h5>
@@includeWhen($boolean,'admin.includeTest',['name' => 'if name'])<br />
@@includeWhen(false,'admin.includeTest',['name' => 'if name'])<br />
@@includeUnless($boolean,'admin.includeTest',['name' => 'if name'])<br />
@@includeUnless(false,'admin.includeTest',['name' => 'if name'])<br />

@includeWhen($boolean,'admin.includeTest',['name' => 'if name'])
@includeWhen(false,'admin.includeTest',['name' => 'if name'])
@includeUnless($boolean,'admin.includeTest',['name' => 'if name'])
@includeUnless(false,'admin.includeTest',['name' => 'if name'])

<h5>23. includeFirst 判断第一个，没有选择第二个，依次类推 </h5>
@@includeFirst(['admin.aa','admin.includeTest'],['name'=> 'blade'])<br />
@includeFirst(['admin.aa','admin.includeTest'],['name' => 'blade'])

<h5>24. each用法 </h5>
@each('admin.includeTest',$jobs,'job')




























<div style="height: 1000px;"></div>
