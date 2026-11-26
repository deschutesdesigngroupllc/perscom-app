@php
  $posts = $this->posts();
  $featured = $posts[0] ?? null;
  $rest = array_slice($posts, 1);
  $allPostsUrl = $this->allPostsUrl();
@endphp

<x-filament-widgets::widget>
  <x-filament::section :icon="$this->icon()" icon-color="primary">
    <x-slot name="heading">{{ $this->heading() }}</x-slot>
    <x-slot name="description">{{ $this->description() }}</x-slot>

    @if ($featured)
      <button type="button" wire:click="mountAction('view', @js(['record' => $featured['key']]))" wire:key="post-{{ $featured['key'] }}"
        class="group relative block w-full overflow-hidden rounded-xl bg-gray-50 py-5 pe-5 ps-7 text-left ring-1 ring-gray-950/5 transition hover:bg-gray-100 focus-visible:outline-2 focus-visible:outline-primary-600 dark:bg-white/5 dark:ring-white/10 dark:hover:bg-white/10">
        <span class="absolute inset-y-0 start-0 w-1.5" style="background-color: {{ $featured['color'] }}" aria-hidden="true"></span>

        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-gray-500 dark:text-gray-400">
          <x-filament::badge color="brass" size="sm">Latest</x-filament::badge>
          @if ($featured['date'])
            <time datetime="{{ $featured['date']->toIso8601String() }}" title="{{ $featured['date']->toDayDateTimeString() }}">
              {{ $featured['date']->diffForHumans() }}
            </time>
          @endif
          @if ($featured['note'])
            <span aria-hidden="true">&middot;</span>
            <span>{{ $featured['note'] }}</span>
          @endif
        </div>

        <h3
          class="mt-2 font-[family-name:var(--font-display)] text-xl font-bold tracking-tight text-gray-950 group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
          {{ $featured['title'] }}
        </h3>

        @if (filled($featured['excerpt']))
          <p class="mt-1.5 line-clamp-3 max-w-4xl text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $featured['excerpt'] }}</p>
        @endif

        <div class="mt-4 flex items-center justify-between gap-3">
          @if ($featured['author'])
            <span class="flex min-w-0 items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
              <x-filament::avatar :src="$featured['author']->profile_photo_url" alt="" size="size-6" />
              <span class="truncate">{{ $featured['author']->name }}</span>
            </span>
          @else
            <span></span>
          @endif
          <span class="shrink-0 text-sm font-medium text-primary-600 dark:text-primary-400">Read more &rarr;</span>
        </div>
      </button>

      @if ($rest !== [])
        <ul class="mt-3 divide-y divide-gray-950/5 dark:divide-white/10">
          @foreach ($rest as $post)
            <li wire:key="post-{{ $post['key'] }}">
              <button type="button" wire:click="mountAction('view', @js(['record' => $post['key']]))"
                class="group flex w-full items-start gap-3 py-3 text-left focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                <span class="mt-1.5 size-2 shrink-0 rounded-full" style="background-color: {{ $post['color'] }}" aria-hidden="true"></span>
                <span class="min-w-0 flex-1">
                  <span
                    class="block truncate text-sm font-semibold text-gray-950 group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
                    {{ $post['title'] }}
                  </span>
                  @if (filled($post['excerpt']))
                    <span class="mt-0.5 block truncate text-sm text-gray-500 dark:text-gray-400">{{ $post['excerpt'] }}</span>
                  @endif
                </span>
                <span class="shrink-0 text-end text-xs text-gray-500 dark:text-gray-400">
                  @if ($post['date'])
                    <time datetime="{{ $post['date']->toIso8601String() }}" title="{{ $post['date']->toDayDateTimeString() }}">
                      {{ $post['date']->diffForHumans(short: true) }}
                    </time>
                  @endif
                  @if ($post['note'])
                    <span class="block">{{ $post['note'] }}</span>
                  @endif
                </span>
              </button>
            </li>
          @endforeach
        </ul>
      @endif
    @else
      <x-filament::empty-state :icon="$this->icon()" icon-color="gray" heading-tag="h3" :contained="false">
        <x-slot name="heading">{{ $this->emptyHeading() }}</x-slot>
        <x-slot name="description">{{ $this->emptyDescription() }}</x-slot>
      </x-filament::empty-state>
    @endif

    @if ($allPostsUrl && $posts !== [])
      <x-slot name="footer">
        <x-filament::link :href="$allPostsUrl" color="gray" icon="heroicon-m-arrow-right" icon-position="after" size="sm">
          {{ $this->allPostsLabel() }}
        </x-filament::link>
      </x-slot>
    @endif
  </x-filament::section>

  <x-filament-actions::modals />
</x-filament-widgets::widget>
