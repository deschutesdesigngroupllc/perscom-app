@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ \Illuminate\Support\Facades\Vite::asset('resources/svg/logo.svg') }}" alt="PERSCOM Personnel Management System" style="height: 50px">
@endif
</a>
</td>
</tr>
