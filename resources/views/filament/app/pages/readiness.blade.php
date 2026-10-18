<x-filament-panels::page>
  <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
    @foreach ($this->getSummaries() as $summary)
      <x-filament::section :heading="$summary->label" :description="$summary->members . ' ' . str('member')->plural($summary->members)">
        <dl class="grid grid-cols-3 gap-4 text-center">
          <div>
            <dt class="text-xs text-gray-500 dark:text-gray-400">Current</dt>
            <dd @class([
                'text-2xl font-semibold',
                'text-success-600 dark:text-success-400' =>
                    ($summary->readyPercentage ?? 0) >= 90,
                'text-warning-600 dark:text-warning-400' =>
                    ($summary->readyPercentage ?? 0) >= 70 &&
                    ($summary->readyPercentage ?? 0) < 90,
                'text-danger-600 dark:text-danger-400' =>
                    $summary->readyPercentage !== null && $summary->readyPercentage < 70,
            ])>
              {{ $summary->readyPercentage === null ? '—' : $summary->readyPercentage . '%' }}
            </dd>
          </div>
          <div>
            <dt class="text-xs text-gray-500 dark:text-gray-400">Attendance (30d)</dt>
            <dd class="text-2xl font-semibold">
              {{ $summary->attendanceRate === null ? '—' : $summary->attendanceRate . '%' }}
            </dd>
          </div>
          <div>
            <dt class="text-xs text-gray-500 dark:text-gray-400">Expiring (30d)</dt>
            <dd @class([
                'text-2xl font-semibold',
                'text-warning-600 dark:text-warning-400' => $summary->expiring > 0,
            ])>
              {{ $summary->expiring }}
            </dd>
          </div>
        </dl>
      </x-filament::section>
    @endforeach
  </div>

  {{ $this->table }}
</x-filament-panels::page>
