<h1>php artisan 命令</h1>

php artisan make:component Alert

<x-alert type="error" :message="$message">
    <x-slot name="title">
        dddd
    </x-slot>
</x-alert>

<x-message type="warning" class="myMessage" :message="$message" :live="@env('MEMCACHED_HOST')" >
    test message test message slot
    <x-slot name="title">
        Server Error
        <br />{{ $component->formatAlert('hello world!') }}<br />
    </x-slot>
</x-message>

<h4>深层组件</h4>
<x-input.button :message="$message"  />

<h4>动态组件</h4>
根据程序运行结果决定显示哪个组件时使用<br />
&lt x-dynamic-component :component="$componentName" class="mt-4" / &gt;

<h4>布局组件</h4>
<x-layout>
    <x-slot name="title">
        Custom Title : 使用方法：@{{ $title }}
    </x-slot>
     {{ $message }}
</x-layout>

<h4>Form表单 csrf method</h4>
<form action="/profile">
    @csrf
    @method('PUT')
</form>

<h4>Form表单 Validation @@error (未验证)</h4>
<label for="title">Post Title</label>

<h4> Stacks 栈 使用</h4>
@push('scripts')
script src="aa.js"></script
@endpush

@prepend('scripts')
This will be first...<br />
@endprepend('scripts')
-------------------
@stack('scripts')

<h4> Service Injection 服务注入</h4>



