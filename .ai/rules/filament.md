---
paths:
  - 'app/Filament/**'
---

# Filament

## Heroicon enum for icons; literal navigation groups
Use the Filament\Support\Icons\Heroicon enum for all icons ($navigationIcon, ->icon(), emptyStateIcon), not 'heroicon-o-*' strings. Set $navigationGroup as a plain string literal (e.g. 'Organization', 'Records') and always set $navigationSort.
