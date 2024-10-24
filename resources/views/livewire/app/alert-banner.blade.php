<div @class([
    'pt-8 flex flex-col space-y-4' => filled($alerts),
])>
  @foreach ($alerts as $alert)
    <div
      class="flex items-center justify-between gap-x-6 px-4 py-2.5 sm:px-3.5 dark:bg-blue-600 bg-blue-50 rounded-lg ring-1 ring-blue-100 dark:ring-blue-500 shadow">
      <div class="text-sm leading-6 text-blue-600 dark:text-blue-50">
        <strong class="font-bold">{{ data_get($alert, 'title') }}</strong>
        {!! data_get($alert, 'message') !!}
      </div>
      @if ($url = data_get($alert, 'url'))
        <div class="text-sm leading-6 font-semibold text-blue-600 dark:text-blue-50">
          <a href="{{ $url }}" target="_blank">{{ data_get($alert, 'link') }}</a>
        </div>
      @endif
    </div>
  @endforeach
</div>
