<div class="alert alert-{{ $type }}">
    {{ $message }}-----{{ $type }}
</div>



<select>
    @foreach($caseData as $k=>$v)
    <option {{ $isSelected($v) ? 'selected="selected"' : ''}}
        value="{{ $k }}">{{ $v }}</option>
    @endforeach
</select>
