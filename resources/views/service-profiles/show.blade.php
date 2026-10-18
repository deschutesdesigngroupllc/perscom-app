@php
  /** @var \App\Data\ServiceProfiles\ServiceProfileData $profile */
  $user = $profile->user;
  $hidden = \App\Services\ServiceProfileService::hiddenFields();
  $title = trim(($profile->showsRank ? $user->rank->abbreviation . ' ' : '') . $user->name);
  $description = collect([
      $profile->showsRank ? $user->rank->name : null,
      $profile->showsAssignment ? $user->position?->name : null,
      $profile->showsAssignment ? $user->unit?->name : null,
      $profile->organization,
  ])
      ->filter()
      ->implode(' · ');
  $stats = collect([
      ['label' => 'Time in Service', 'value' => $profile->timeInService],
      ['label' => 'Time in Grade', 'value' => $profile->showsRank ? $profile->timeInGrade : null],
      ['label' => 'Awards', 'value' => in_array('award_records', $hidden, true) ? null : (string) $profile->awards->count()],
      [
          'label' => 'Qualifications',
          'value' => in_array('qualification_records', $hidden, true) ? null : (string) $profile->qualifications->count(),
      ],
  ])->filter(fn(array $stat): bool => filled($stat['value']));
@endphp

<!DOCTYPE html>
<html lang="en" class="dark h-full scroll-smooth antialiased">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $title }} · {{ $profile->organization }}</title>
  <meta name="description" content="{{ $description }}" />
  <meta name="theme-color" content="#0f172a" />
  <link rel="canonical" href="{{ $profile->url }}" />

  <meta property="og:type" content="profile" />
  <meta property="og:site_name" content="{{ $profile->organization }}" />
  <meta property="og:title" content="{{ $title }}" />
  <meta property="og:description" content="{{ $description }}" />
  <meta property="og:url" content="{{ $profile->url }}" />
  <meta property="og:image" content="{{ $profile->imageUrl }}" />
  <meta property="og:image:width" content="{{ \App\Services\ServiceProfileImageService::WIDTH }}" />
  <meta property="og:image:height" content="{{ \App\Services\ServiceProfileImageService::HEIGHT }}" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="{{ $title }}" />
  <meta name="twitter:description" content="{{ $description }}" />
  <meta name="twitter:image" content="{{ $profile->imageUrl }}" />

  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  @googlefonts
  @filamentStyles
  @vite(['resources/css/filament/app/theme.css'])
</head>

<body class="min-h-full bg-gray-950 font-sans text-gray-300">
  <div class="relative isolate">
    @if (!in_array('cover_photo', $hidden, true) && $user->cover_photo_url)
      <img src="{{ $user->cover_photo_url }}" alt="" class="absolute inset-x-0 top-0 -z-10 h-72 w-full object-cover opacity-40">
      <div class="absolute inset-x-0 top-0 -z-10 h-72 bg-gradient-to-b from-gray-950/20 to-gray-950"></div>
    @else
      <div class="absolute inset-x-0 top-0 -z-10 h-72 bg-gradient-to-b from-primary-600/25 to-gray-950"></div>
    @endif

    <main class="mx-auto max-w-4xl px-4 pt-24 pb-16 sm:px-6 sm:pt-32">
      <section class="rounded-2xl bg-gray-900/80 p-6 shadow-xl ring-1 ring-white/10 backdrop-blur sm:p-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
          <div class="flex shrink-0 items-center gap-4">
            @unless (in_array('profile_photo', $hidden, true))
              <x-filament::avatar :src="$user->profile_photo_url" :alt="$user->name" :circular="false" size="h-24 w-24"
                class="rounded-xl object-cover ring-1 ring-white/10" />
            @endunless
            @if ($profile->showsRank && $user->rank->image?->image_url)
              <div class="flex h-24 w-20 items-center justify-center">
                <img src="{{ $user->rank->image->image_url }}" alt="{{ $user->rank->name }}" class="max-h-20 max-w-full object-contain">
              </div>
            @endif
          </div>

          <div class="min-w-0 flex-1">
            @if ($profile->showsRank)
              <p class="text-sm font-semibold tracking-wide text-primary-400 uppercase">{{ $user->rank->name }}</p>
            @endif
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ $user->name }}</h1>
            @if ($profile->showsAssignment && ($user->position || $user->unit))
              <p class="mt-2 text-base text-gray-400">
                {{ collect([$user->position?->name, $user->unit?->name])->filter()->implode(' · ') }}
              </p>
            @endif
            <div class="mt-3 flex flex-wrap items-center gap-2">
              @if (!in_array('specialty_id', $hidden, true) && $user->specialty)
                <x-filament::badge color="gray">
                  {{ trim($user->specialty->abbreviation . ' ' . $user->specialty->name) }}
                </x-filament::badge>
              @endif
              @if (!in_array('status_id', $hidden, true) && $user->status)
                <x-filament::badge :color="\Filament\Support\Colors\Color::generateV3Palette($user->status->color ?? '#2563eb')">
                  {{ $user->status->name }}
                </x-filament::badge>
              @endif
            </div>
          </div>

          <x-filament::button icon="heroicon-m-share" data-share-url="{{ $profile->url }}" class="shrink-0 self-start sm:self-center">
            <span data-share-label>Share</span>
          </x-filament::button>
        </div>

        @if ($stats->isNotEmpty())
          <dl class="mt-8 grid grid-cols-2 gap-px overflow-hidden rounded-xl bg-white/10 ring-1 ring-white/10 sm:grid-cols-4">
            @foreach ($stats as $stat)
              <div class="bg-gray-900 px-4 py-4">
                <dt class="text-xs font-medium text-gray-500">{{ $stat['label'] }}</dt>
                <dd class="mt-1 text-lg font-semibold text-white">{{ $stat['value'] }}</dd>
              </div>
            @endforeach
          </dl>
        @endif
      </section>

      @if ($profile->awards->isNotEmpty())
        <x-filament::section heading="Ribbon Rack" class="mt-8">
          <div class="flex flex-wrap gap-1.5">
            @foreach ($profile->awards as $record)
              @if ($record->award->image?->image_url)
                <img src="{{ $record->award->image->image_url }}" alt="{{ $record->award->name }}" title="{{ $record->award->name }}"
                  class="h-7 w-auto rounded-xs ring-1 ring-white/10" loading="lazy">
              @else
                <x-filament::badge color="gray">{{ $record->award->name }}</x-filament::badge>
              @endif
            @endforeach
          </div>

          <ul class="mt-6 divide-y divide-white/5">
            @foreach ($profile->awards as $record)
              <li class="flex gap-4 py-4">
                <div class="flex h-10 w-14 shrink-0 items-center justify-center">
                  @if ($record->award->image?->image_url)
                    <img src="{{ $record->award->image->image_url }}" alt="" class="max-h-10 max-w-full object-contain"
                      loading="lazy">
                  @else
                    <x-filament::icon icon="heroicon-o-trophy" :size="\Filament\Support\Enums\IconSize::Large" class="text-amber-400" />
                  @endif
                </div>
                <div class="min-w-0 flex-1">
                  <div class="flex flex-wrap items-baseline justify-between gap-x-3">
                    <p class="font-medium text-white">{{ $record->award->name }}</p>
                    <time class="text-xs text-gray-500"
                      datetime="{{ $record->created_at?->toDateString() }}">{{ $record->created_at?->toFormattedDateString() }}</time>
                  </div>
                  @if (filled($record->text))
                    <p class="mt-1 text-sm leading-relaxed text-gray-400">{{ Str::limit(strip_tags((string) $record->text), 400) }}</p>
                  @endif
                </div>
              </li>
            @endforeach
          </ul>
        </x-filament::section>
      @endif

      <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
        @if ($profile->promotions->isNotEmpty())
          <x-filament::section heading="Promotions">
            <ol class="space-y-4 border-s border-white/10 ps-5">
              @foreach ($profile->promotions as $record)
                <li class="relative">
                  <span class="absolute -start-[1.65rem] top-1.5 h-2.5 w-2.5 rounded-full bg-primary-500 ring-4 ring-gray-900"></span>
                  <div class="flex items-center gap-3">
                    @if ($record->rank->image?->image_url)
                      <img src="{{ $record->rank->image->image_url }}" alt="" class="h-7 w-8 object-contain" loading="lazy">
                    @endif
                    <div>
                      <p class="font-medium text-white">{{ $record->rank->name }}</p>
                      <time class="text-xs text-gray-500"
                        datetime="{{ $record->created_at?->toDateString() }}">{{ $record->created_at?->toFormattedDateString() }}</time>
                    </div>
                  </div>
                </li>
              @endforeach
            </ol>
          </x-filament::section>
        @endif

        @if ($profile->qualifications->isNotEmpty())
          <x-filament::section heading="Qualifications">
            <ul class="flex flex-wrap gap-2">
              @foreach ($profile->qualifications as $record)
                <li>
                  <x-filament::badge color="gray" size="lg" :icon="$record->qualification->image?->image_url ?? 'heroicon-m-check-badge'">
                    {{ $record->qualification->name }}
                  </x-filament::badge>
                </li>
              @endforeach
            </ul>
          </x-filament::section>
        @endif
      </div>

      <footer class="mt-16 flex flex-col items-center gap-3 text-center">
        <p class="text-sm text-gray-500">Service profile for {{ $profile->organization }}</p>
        <x-filament::button tag="a" href="https://perscom.io/?utm_source=service-profile&utm_medium=referral" target="_blank"
          rel="noopener" color="gray" icon="heroicon-m-arrow-right" icon-position="after" class="rounded-full">
          Powered by <span class="font-semibold text-white">PERSCOM</span>
          <span class="text-gray-500">·</span>
          <span class="text-primary-400">Start your unit</span>
        </x-filament::button>
      </footer>
    </main>
  </div>

  <script>
    document.querySelectorAll('[data-share-url]').forEach((button) => {
      button.addEventListener('click', async () => {
        const url = button.dataset.shareUrl;
        const label = button.querySelector('[data-share-label]');

        if (navigator.share) {
          try {
            await navigator.share({
              title: document.title,
              url
            });
          } catch {}

          return;
        }

        await navigator.clipboard?.writeText(url);
        label.textContent = 'Link copied';
        setTimeout(() => (label.textContent = 'Share'), 2000);
      });
    });
  </script>
</body>

</html>
