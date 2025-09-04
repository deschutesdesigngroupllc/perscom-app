<div>
  @if ($show)
    <div class="flex items-center justify-between dark:bg-gray-800 bg-gray-900 py-2.5 px-4 md:px-6 lg:px-8">
      <div class="flex-1"></div>
      <div class="text-sm leading-6 text-white text-center">
        <a href="{{ route('spark.portal') }}">
          <div class="font-semibold inline-flex">Subscription</div>
          <svg viewBox="0 0 2 2" class="mx-2 inline h-0.5 w-0.5 fill-current" aria-hidden="true">
            <circle cx="1" cy="1" r="1" />
          </svg>{{ $message }}
        </a>
      </div>
      <div class="flex flex-1 justify-end">
        <a href="{{ route('spark.portal') }}" class="-m-3 p-3 focus-visible:-outline-offset-4">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="h-4 stroke-white">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
          </svg>
        </a>
      </div>
    </div>
  @endif
</div>
