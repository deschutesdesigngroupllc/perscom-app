@props(['tree', 'mode', 'hiddenFields' => []])

@php
  $isManual = $mode === \App\Models\Enums\RosterMode::MANUAL;

  /** @var array<int, array{path: array<int, string>, node: array<string, mixed>, depth: int}> $units */
  $units = [];
  $flatten = function (array $nodes, array $path) use (&$flatten, &$units): void {
      foreach ($nodes as $node) {
          $unitPath = [...$path, $node['unit']->name];
          $units[] = ['path' => $unitPath, 'node' => $node, 'depth' => count($path)];
          $flatten($node['children'] ?? [], $unitPath);
      }
  };
  $flatten(collect($tree)->all(), []);

  $columns = array_filter([
      'slot' => $isManual ? 'Slot' : null,
      'rank' => !in_array('rank_id', $hiddenFields) ? 'Rank' : null,
      'name' => !in_array('name', $hiddenFields) ? 'Name' : null,
      'position' => !in_array('position_id', $hiddenFields) ? 'Position' : null,
      'specialty' => !in_array('specialty_id', $hiddenFields) ? 'Specialty' : null,
      'status' => !in_array('status_id', $hiddenFields) ? 'Status' : null,
  ]);

  $cell = fn($user, string $column, ?string $slot = null): string => match ($column) {
      'slot' => $slot ?? '',
      'rank' => (string) ($user->rank->abbreviation ?? ($user->rank->name ?? '')),
      'name' => (string) $user->display_name,
      'position' => (string) data_get($user->getAttribute('position'), 'name', ''),
      'specialty' => trim(
          data_get($user->getAttribute('specialty'), 'abbreviation', '') . ' ' . data_get($user->getAttribute('specialty'), 'name', ''),
      ),
      'status' => (string) ($user->status->name ?? ''),
  };
@endphp

<table class="roster-print-table">
  <thead>
    <tr>
      @foreach ($columns as $key => $label)
        <th scope="col" class="roster-print-col-{{ $key }}">{{ $label }}</th>
      @endforeach
    </tr>
  </thead>

  @foreach ($units as ['node' => $node, 'depth' => $depth])
    @php
      $rows = $isManual
          ? collect($node['slots'])->flatMap(
              fn(array $slotNode) => $slotNode['users']->isEmpty()
                  ? [[null, $slotNode['slot']->name]]
                  : $slotNode['users']->map(fn($user) => [$user, $slotNode['slot']->name]),
          )
          : $node['users']->map(fn($user) => [$user, null]);
      $memberCount = $rows->filter(fn(array $row) => $row[0] !== null)->count();
    @endphp
    <tbody>
      <tr class="roster-print-unit">
        <th scope="rowgroup" colspan="{{ count($columns) }}" style="padding-left: {{ 2 + $depth * 5 }}mm">
          {{ $node['unit']->name }}
          <span>{{ $memberCount }} {{ Str::plural('member', $memberCount) }}</span>
        </th>
      </tr>
      @forelse ($rows as [$user, $slotName])
        <tr>
          @foreach (array_keys($columns) as $column)
            @if ($user === null)
              <td @class(['roster-print-empty' => $column !== 'slot'])>{{ $column === 'slot' ? $slotName : ($loop->index === ($isManual ? 1 : 0) ? 'Vacant' : '') }}
              </td>
            @else
              <td>{{ $cell($user, $column, $slotName) }}</td>
            @endif
          @endforeach
        </tr>
      @empty
        <tr>
          <td colspan="{{ count($columns) }}" class="roster-print-empty">No members assigned</td>
        </tr>
      @endforelse
    </tbody>
  @endforeach
</table>
