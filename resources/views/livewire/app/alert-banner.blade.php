<div @class([
    'pt-8' => filled($alerts),
])>
  @foreach ($alerts as $alert)
    <div class="flex items-center justify-start gap-x-6 px-4 py-2.5 sm:px-3.5 bg-blue-50 rounded-lg ring-1 ring-blue-600 shadow">
      <div class="text-sm leading-6 text-blue-600">
        <strong class="font-bold">{{ data_get($alert, 'title') }}</strong>
        {!! data_get($alert, 'message') !!}
      </div>
    </div>
  @endforeach
</div>
