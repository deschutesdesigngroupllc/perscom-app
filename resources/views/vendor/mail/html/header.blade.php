@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img srcset="{{ asset('images/logo-50.png') }}, {{ asset('images/logo-100.png') }} 2x"
     src="{{ asset('images/logo-100.png') }}"
     alt="PERSCOM Personnel Management System">
@endif
</a>
</td>
</tr>
