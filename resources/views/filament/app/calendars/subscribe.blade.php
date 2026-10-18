<div class="flex flex-col gap-5">
  <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
    <x-filament::button tag="a" :href="$googleUrl" target="_blank" rel="noopener" color="gray"
      icon="heroicon-m-arrow-top-right-on-square" icon-position="after">
      Google
    </x-filament::button>
    <x-filament::button tag="a" :href="$webcalUrl" color="gray" icon="heroicon-m-device-phone-mobile">
      Apple
    </x-filament::button>
    <x-filament::button tag="a" :href="$outlookUrl" target="_blank" rel="noopener" color="gray"
      icon="heroicon-m-arrow-top-right-on-square" icon-position="after">
      Outlook
    </x-filament::button>
  </div>

  <div class="flex flex-col gap-2">
    <p class="text-sm font-medium text-gray-950 dark:text-white">Subscription link</p>
    <div x-data="{ copied: false }" class="flex items-stretch gap-2">
      <x-filament::input.wrapper class="min-w-0 flex-1">
        <x-filament::input type="text" readonly :value="$url" onclick="this.select()" aria-label="Subscription link" />
      </x-filament::input.wrapper>
      <x-filament::button color="gray" icon="heroicon-m-clipboard-document"
        x-on:click="window.navigator.clipboard.writeText({{ Js::from($url) }}); copied = true; setTimeout(() => copied = false, 2000)">
        <span x-show="! copied">Copy</span>
        <span x-show="copied" x-cloak>Copied</span>
      </x-filament::button>
    </div>
    <p class="text-xs text-gray-500 dark:text-gray-400">
      Paste this link into any calendar app that supports subscribing by URL. Keep it private: anyone with the link can see these events.
    </p>
  </div>

  <x-filament::section compact secondary>
    <div class="flex items-center gap-4">
      <img src="{{ $qrCode }}" alt="Calendar subscription QR code" class="size-28 shrink-0 rounded-md bg-white p-1.5" />
      <div class="min-w-0 text-sm">
        <p class="font-medium text-gray-950 dark:text-white">On your phone?</p>
        <p class="mt-1 text-gray-500 dark:text-gray-400">Scan this code with your camera to subscribe to {{ $name }} on your device.
        </p>
      </div>
    </div>
  </x-filament::section>
</div>
