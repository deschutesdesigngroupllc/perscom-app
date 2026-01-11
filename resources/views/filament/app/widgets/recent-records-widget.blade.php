<x-filament-widgets::widget>
  <div class="space-y-4">
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <a href="{{ $this->getUserRecordsUrl('4') }}"
        class="group relative overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 transition-all hover:shadow-md hover:ring-amber-500/20 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-amber-500/30">
        <div
          class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-orange-500/5 opacity-0 transition-opacity group-hover:opacity-100">
        </div>
        <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-amber-500/10 blur-2xl transition-all group-hover:bg-amber-500/20">
        </div>

        <div class="relative flex items-start gap-x-3">
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 shadow-md shadow-amber-500/30">
            @if ($this->getLatestRankRecord()?->rank?->image?->image_url)
              <img src="{{ $this->getLatestRankRecord()->rank->image->image_url }}" alt="" class="h-6 w-6 object-contain" />
            @else
              <x-heroicon-s-chevron-double-up class="h-5 w-5 text-white" />
            @endif
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Rank</span>
              <x-heroicon-m-arrow-up-right class="h-4 w-4 text-gray-300 transition-colors group-hover:text-amber-500 dark:text-gray-600" />
            </div>
            @if ($this->getLatestRankRecord())
              <p class="mt-1 truncate text-sm font-semibold text-gray-900 dark:text-white">
                {{ $this->getLatestRankRecord()->rank?->name ?? 'Rank Change' }}
              </p>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                {{ $this->getLatestRankRecord()->created_at->diffForHumans() }}
              </p>
            @else
              <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">No records yet</p>
            @endif
          </div>
        </div>
      </a>

      <a href="{{ $this->getUserRecordsUrl('0') }}"
        class="group relative overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 transition-all hover:shadow-md hover:ring-blue-500/20 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-blue-500/30">
        <div
          class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-500/5 opacity-0 transition-opacity group-hover:opacity-100">
        </div>
        <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-blue-500/10 blur-2xl transition-all group-hover:bg-blue-500/20">
        </div>

        <div class="relative flex items-start gap-x-3">
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 shadow-md shadow-blue-500/30">
            @if ($this->getLatestAssignmentRecord()?->unit?->image?->image_url)
              <img src="{{ $this->getLatestAssignmentRecord()->unit->image->image_url }}" alt="" class="h-6 w-6 object-contain" />
            @else
              <x-heroicon-s-rectangle-stack class="h-5 w-5 text-white" />
            @endif
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Assignment</span>
              <x-heroicon-m-arrow-up-right class="h-4 w-4 text-gray-300 transition-colors group-hover:text-blue-500 dark:text-gray-600" />
            </div>
            @if ($this->getLatestAssignmentRecord())
              <p class="mt-1 truncate text-sm font-semibold text-gray-900 dark:text-white">
                {{ $this->getLatestAssignmentRecord()->position?->name ?? ($this->getLatestAssignmentRecord()->unit?->name ?? 'Assignment Change') }}
              </p>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                {{ $this->getLatestAssignmentRecord()->created_at->diffForHumans() }}
              </p>
            @else
              <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">No records yet</p>
            @endif
          </div>
        </div>
      </a>

      <a href="{{ $this->getUserRecordsUrl('1') }}"
        class="group relative overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 transition-all hover:shadow-md hover:ring-amber-500/20 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-amber-500/30">
        <div
          class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-orange-500/5 opacity-0 transition-opacity group-hover:opacity-100">
        </div>
        <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-amber-500/10 blur-2xl transition-all group-hover:bg-amber-500/20">
        </div>

        <div class="relative flex items-start gap-x-3">
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 shadow-md shadow-amber-500/30">
            @if ($this->getLatestAwardRecord()?->award?->image?->image_url)
              <img src="{{ $this->getLatestAwardRecord()->award->image->image_url }}" alt="" class="h-6 w-6 object-contain" />
            @else
              <x-heroicon-s-trophy class="h-5 w-5 text-white" />
            @endif
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Award</span>
              <x-heroicon-m-arrow-up-right class="h-4 w-4 text-gray-300 transition-colors group-hover:text-amber-500 dark:text-gray-600" />
            </div>
            @if ($this->getLatestAwardRecord())
              <p class="mt-1 truncate text-sm font-semibold text-gray-900 dark:text-white">
                {{ $this->getLatestAwardRecord()->award?->name ?? 'Award Received' }}
              </p>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                {{ $this->getLatestAwardRecord()->created_at->diffForHumans() }}
              </p>
            @else
              <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">No records yet</p>
            @endif
          </div>
        </div>
      </a>

      <a href="{{ $this->getUserRecordsUrl('3') }}"
        class="group relative overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 transition-all hover:shadow-md hover:ring-emerald-500/20 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-emerald-500/30">
        <div
          class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-teal-500/5 opacity-0 transition-opacity group-hover:opacity-100">
        </div>
        <div
          class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-emerald-500/10 blur-2xl transition-all group-hover:bg-emerald-500/20">
        </div>

        <div class="relative flex items-start gap-x-3">
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 shadow-md shadow-emerald-500/30">
            @if ($this->getLatestQualificationRecord()?->qualification?->image?->image_url)
              <img src="{{ $this->getLatestQualificationRecord()->qualification->image->image_url }}" alt=""
                class="h-6 w-6 object-contain" />
            @else
              <x-heroicon-s-star class="h-5 w-5 text-white" />
            @endif
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Qualification</span>
              <x-heroicon-m-arrow-up-right
                class="h-4 w-4 text-gray-300 transition-colors group-hover:text-emerald-500 dark:text-gray-600" />
            </div>
            @if ($this->getLatestQualificationRecord())
              <p class="mt-1 truncate text-sm font-semibold text-gray-900 dark:text-white">
                {{ $this->getLatestQualificationRecord()->qualification?->name ?? 'Qualification Earned' }}
              </p>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                {{ $this->getLatestQualificationRecord()->created_at->diffForHumans() }}
              </p>
            @else
              <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">No records yet</p>
            @endif
          </div>
        </div>
      </a>

      <a href="{{ $this->getUserRecordsUrl('2') }}"
        class="group relative overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 transition-all hover:shadow-md hover:ring-red-500/20 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-red-500/30">
        <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-orange-500/5 opacity-0 transition-opacity group-hover:opacity-100">
        </div>
        <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-red-500/10 blur-2xl transition-all group-hover:bg-red-500/20"></div>

        <div class="relative flex items-start gap-x-3">
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-red-400 to-red-600 shadow-md shadow-red-500/30">
            <x-heroicon-s-fire class="h-5 w-5 text-white" />
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400">Combat</span>
              <x-heroicon-m-arrow-up-right class="h-4 w-4 text-gray-300 transition-colors group-hover:text-red-500 dark:text-gray-600" />
            </div>
            @if ($this->getLatestCombatRecord())
              <p class="mt-1 truncate text-sm font-semibold text-gray-900 dark:text-white">
                {{ Str::limit($this->getLatestCombatRecord()->text, 30) }}
              </p>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                {{ $this->getLatestCombatRecord()->created_at->diffForHumans() }}
              </p>
            @else
              <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">No records yet</p>
            @endif
          </div>
        </div>
      </a>

      <a href="{{ $this->getUserRecordsUrl('5') }}"
        class="group relative overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 transition-all hover:shadow-md hover:ring-purple-500/20 dark:bg-gray-900 dark:ring-white/10 dark:hover:ring-purple-500/30">
        <div
          class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-indigo-500/5 opacity-0 transition-opacity group-hover:opacity-100">
        </div>
        <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-purple-500/10 blur-2xl transition-all group-hover:bg-purple-500/20">
        </div>

        <div class="relative flex items-start gap-x-3">
          <div
            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 shadow-md shadow-purple-500/30">
            <x-heroicon-s-clipboard-document-list class="h-5 w-5 text-white" />
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between">
              <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">Service</span>
              <x-heroicon-m-arrow-up-right class="h-4 w-4 text-gray-300 transition-colors group-hover:text-purple-500 dark:text-gray-600" />
            </div>
            @if ($this->getLatestServiceRecord())
              <p class="mt-1 truncate text-sm font-semibold text-gray-900 dark:text-white">
                {{ Str::limit($this->getLatestServiceRecord()->text, 30) }}
              </p>
              <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                {{ $this->getLatestServiceRecord()->created_at->diffForHumans() }}
              </p>
            @else
              <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">No records yet</p>
            @endif
          </div>
        </div>
      </a>
    </div>
  </div>
</x-filament-widgets::widget>
