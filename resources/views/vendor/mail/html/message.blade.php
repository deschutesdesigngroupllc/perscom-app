<x-mail::layout>
  {{-- Header --}}
  @php
    $organization = tenancy()->initialized ? tenant()?->name : null;
    $organizationUrl = tenancy()->initialized ? tenant()?->url : null;
  @endphp
  <x-slot:header>
    <x-mail::header :url="$organizationUrl ?? config('app.url')">
      {{ $organization ?? config('app.name') }}
    </x-mail::header>
  </x-slot:header>

  {{-- Body --}}
  {!! $slot !!}

  {{-- Subcopy --}}
  @isset($subcopy)
    <x-slot:subcopy>
      <x-mail::subcopy>
        {!! $subcopy !!}
      </x-mail::subcopy>
    </x-slot:subcopy>
  @endisset

  {{-- Footer --}}
  <x-slot:footer>
    <x-mail::footer>
      @if (filled($organization))
        You're receiving this because you're a member of {{ $organization }} on PERSCOM.<br>
      @endif
      © {{ date('Y') }} PERSCOM. All rights reserved.
    </x-mail::footer>
  </x-slot:footer>
</x-mail::layout>
