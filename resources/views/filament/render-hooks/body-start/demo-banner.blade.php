@if (App::isDemo())
  <div class="flex items-center justify-center gap-x-6 bg-gray-900 dark:bg-gray-800 px-4 py-2.5 sm:px-3.5">
    <div class="text-sm leading-6 text-white">
      <strong class="font-semibold">Demo Mode</strong><svg viewBox="0 0 2 2" class="mx-2 inline h-0.5 w-0.5 fill-current" aria-hidden="true">
        <circle cx="1" cy="1" r="1" />
      </svg>You are currently in demo mode. Not all features will be available.
    </div>
  </div>
@endif
