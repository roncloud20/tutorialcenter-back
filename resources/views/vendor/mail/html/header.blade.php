@props(['url'])
<tr>
<td class="header">
<a href="{{ config('app.frontend_url') }}" style="display: inline-block;">
    <img
        src="{{ asset('images/icon.png') }}"
        class="logo"
        alt="{{ config('app.name') }}"
        style="height: 60px;"
    >
</a>
</td>
</tr>

<!-- <tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr> -->
