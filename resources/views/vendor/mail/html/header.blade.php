@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img srcset="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/logo-50.png') }}, {{ \Illuminate\Support\Facades\Vite::asset('resources/images/logo-100.png') }} 2x"
     src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/logo-100.png') }}"
     alt="PERSCOM Personnel Management System">
@endif
</a>
</td>
</tr>
