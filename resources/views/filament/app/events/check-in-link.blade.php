<div class="flex flex-col items-center gap-4">
  <img src="{{ $qrCode }}" alt="{{ __('Event check-in QR code') }}" class="size-56 rounded-lg bg-white p-2" />

  @if ($opens && $closes)
    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
      {{ __('Check-in opens :opens and closes :closes.', ['opens' => $opens->toDayDateTimeString(), 'closes' => $closes->toDayDateTimeString()]) }}
    </p>
  @endif

  <x-filament::input.wrapper class="w-full">
    <x-filament::input type="text" readonly :value="$url" onclick="this.select()" />
  </x-filament::input.wrapper>
</div>
