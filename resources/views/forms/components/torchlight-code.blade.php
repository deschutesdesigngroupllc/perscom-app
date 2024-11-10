<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
  <pre>
    <x-torchlight-code
        language="{{ $field->getLanguage() ?? '' }}"
        theme="{{ $field->getTheme() ?? '' }}"
        {{ $attributes }}
    >{!! $getState() !!}</x-torchlight-code>
</pre>
</x-dynamic-component>
