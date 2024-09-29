<div>
  @foreach ($announcements as $announcement)
    <div class="flex items-center justify-center text-center gap-x-6 py-2.5 px-4 md:px-6 lg:px-8"
      style="background-color: {{ data_get($announcement, 'color') }}">
      <div class="text-sm leading-6 text-white">
        <div class="font-semibold inline-flex">{{ data_get($announcement, 'title') }}</div>
        <svg viewBox="0 0 2 2" class="mx-2 inline h-0.5 w-0.5 fill-current" aria-hidden="true">
          <circle cx="1" cy="1" r="1" />
        </svg>
        <div class="inline-flex">
          {!! data_get($announcement, 'content') !!}
        </div>
      </div>
    </div>
  @endforeach
</div>
