deeper input button

匿名组件因为没有关联传递class，传递变量<br />

@props(['type' => 'info', 'message'])

{{ $attributes->merge(['class' => 'alert alert-'.$type]) }}
<div {{ $attributes->merge(['class' => 'alert alert-'.$type]) }}>
{{ $message }}
</div>
