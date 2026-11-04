@props(['name','label','kind'=>'text','value'=>'','required'=>false,'options'=>[],'help'=>null,'id'=>null,'showErrors'=>true])
@php
    $fieldId=$id ?? 'ti-'.trim(str_replace(['.','[',']'],['-','-',''],$name),'-');
    $errorKey=str_replace(['[',']'],['.',''],$name);
    $hasError=$showErrors && $errors->has($errorKey);
    $descriptions=[$fieldId.'-help'];
    if($hasError) $descriptions[]=$fieldId.'-error';
    if($kind==='select') $descriptions[]=$fieldId.'-selection';
@endphp
<div class="tc-field" data-field-name="{{ $errorKey }}">
<label for="{{ $fieldId }}">{{ $label }} @if($required)<span aria-hidden="true">*</span>@endif</label>
@if($kind==='textarea')
<textarea id="{{ $fieldId }}" name="{{ $name }}" @required($required) maxlength="10000" rows="4" aria-invalid="{{ $hasError?'true':'false' }}" aria-describedby="{{ implode(' ',$descriptions) }}">{{ $value }}</textarea>
@elseif($kind==='select')
<select id="{{ $fieldId }}" name="{{ $name }}" @required($required) data-tc-long-option aria-invalid="{{ $hasError?'true':'false' }}" aria-describedby="{{ implode(' ',$descriptions) }}">
<option value="">{{ $errorKey==='data.effectiveness_level'?'Tidak dinilai / tidak ada tindakan':'Pilih '.mb_strtolower($label) }}</option>
@foreach($options as $option)
<option value="{{ $option }}" @selected((string)$value===(string)$option)>{{ $option }}</option>
@endforeach
</select>
<small id="{{ $fieldId }}-selection" class="tc-selected-value">{{ $value }}</small>
@else
<input id="{{ $fieldId }}" type="{{ $kind }}" name="{{ $name }}" value="{{ $value }}" @required($required) @if($kind==='number') min="0" max="2147483647" step="1" inputmode="numeric" @elseif($kind!=='date') maxlength="{{ $kind==='url'?2000:255 }}" @endif aria-invalid="{{ $hasError?'true':'false' }}" aria-describedby="{{ implode(' ',$descriptions) }}">
@endif
<small id="{{ $fieldId }}-help">{{ $help }}</small>
@if($hasError)
<p id="{{ $fieldId }}-error" class="tc-error">{{ $errors->first($errorKey) }}</p>
@endif
</div>
