@component('mail::message')
  # Hello,

  {{ __('We wanted to let you know that the password associated with your PERSCOM.io account has been changed.') }}

  {{ __('If this was not intended, or if you did not perform this action, please contact support@deschutesdesigngroup.com, or submit a support ticket.') }}

  {{ __('Thanks,') }}<br>
  {{ config('app.name') }}
@endcomponent
