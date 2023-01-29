@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img srcset="{{ asset('images/logo-50.png') }}, {{ asset('images/logo-100.png') }} 2x"
     src="{{ asset('images/logo-100.png') }}"
     alt="PERSCOM Personnel Management System">
</a>
</td>
</tr>
