@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img srcset="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/logo-50.png') }}, {{ \Illuminate\Support\Facades\Vite::asset('resources/images/logo-100.png') }} 2x"
     src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/logo-50.png') }}"
     alt="PERSCOM Personnel Management System">
</a>
</td>
</tr>
