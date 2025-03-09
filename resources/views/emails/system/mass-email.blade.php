@component('mail::message')
    {!! tiptap_converter()->asHTML($content) !!}
@endcomponent