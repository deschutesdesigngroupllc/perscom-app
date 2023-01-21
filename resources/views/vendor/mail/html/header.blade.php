@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ \Illuminate\Support\Facades\Vite::asset('resources/svg/mail-logo.svg') }}" alt="PERSCOM Personnel Management System">
@endif
</a>
</td>
</tr>
