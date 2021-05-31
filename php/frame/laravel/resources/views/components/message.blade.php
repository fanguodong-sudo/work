加载全部属性：
<div {{ $attributes }}>
    {{ $message }}
</div>
<br />------<br />
<div {{ $attributes->merge(['class' => 'alert alert-'>$type ]) }}>
    {{ $type }}
</div>

<div>
        type:{{ $type }}｜｜message:{{ $message }}
</div>

class----
<div {{ $attributes->class(['p-4','bg_red'])->merge(['type' => $type]) }}>
{{ $type }}--{{ $message }}
</div>

---------------------
<div {{ $attributes->merge(['data-controller' => $attributes->prepends('profile-controller')]) }}>
    {{ $message }}---{{ $attributes->prepends('profile-controller') }}
</div>
----------------------
{{ $attributes->filter(function ($value,$key){
    echo '['.$key.']';
    echo '['.$value.']';
    return true;
}) }}
------------------------<br />
<button>
   aaaa {{ $slot }} aaaa
</button>

<br />------------------------<br />
<h4>whereStartsWith与whereDoesntStartWith</h4>
{{ $attributes->whereStartsWith('wire:cla') }}
{{ $attributes->whereDoesntStartWith('wire:cla') }}

<h4>$attributes->has('class')</h4>
@@if ($attributes->has('class'))<br />
    <div>Class attribute is present</div>
@@endif<br />
@if ($attributes->has('class'))
    <div>Class attribute is present</div>
@endif

<h4>$attributes->get('class')</h4>
@{{ $attributes->get('class') }}<br />
{{ $attributes->get('class') }}

<h4> $slot </h4>
{{ $slot }}

<h4> $slot $title</h4>
<span class="alert-title">{{ $title }}</span>
{{ $slot }}

<h4> 手动添加组件时需要在app/Providers/AppServiceProvider.php的boot函数中手动注册，
或者使用组件空间的方式注册整个目录里的组件

    否则程序加载不到。</h4>
boot 函数中添加代码：
Blade::componentNamespace('Nightshade\\Views\\Components', 'nightshade');
意思时将 Nightshade\Views\Components 目录下的全部组件注册，并命名为 nightshade；
如果nightshade中有两个组件（calendar，color-picker），则 使用方法为：
{{--<@x-nightshade::calendar />--}}
{{--<@x-nightshade::color-picker />--}}





