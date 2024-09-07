<div>
  @if ($show)
    <div class="flex items-center justify-center gap-x-6 dark:bg-gray-800 bg-gray-900 px-6 py-2.5 sm:px-3.5 sm:before:flex-1">
      <div class="text-sm leading-6 text-white">
        <a href="{{ route('spark.portal') }}">
          <strong class="font-semibold">Subscription</strong><svg viewBox="0 0 2 2" class="mx-2 inline h-0.5 w-0.5 fill-current"
            aria-hidden="true">
            <circle cx="1" cy="1" r="1" />
          </svg>{{ $message }}
        </a>
      </div>
      <div class="flex flex-1 justify-end">
        <a href="{{ route('spark.portal') }}" class="-m-3 p-3 focus-visible:outline-offset-[-4px]">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="h-4 stroke-white">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
          </svg>
        </a>
      </div>
    </div>
  @endif
</div>
