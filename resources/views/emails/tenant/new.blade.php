@component('mail::message')
# {{ $organization }} Is Ready

Your organization is set up. Choose a password to sign in and start building your roster.

**Your PERSCOM URL**: [{{ $url }}]({{ $url }})<br>
**Email**: {{ $email }}

@component('mail::button', ['url' => $setPasswordUrl])
  Set Your Password
@endcomponent

This link works once and expires in 7 days. After that, use **Forgot password** on the sign-in page at {{ $url }}.

— The PERSCOM Team
@endcomponent
