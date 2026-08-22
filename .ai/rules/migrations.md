---
paths:
  - 'database/migrations/**'
---

# Migrations

## Tenant-scoped schema
Put new tenant tables in database/migrations/tenant/. The central connection carries almost no domain tables.

## Foreign keys via foreignId()->constrained()
Define foreign keys with foreignId()->constrained(), not foreignIdFor() or manual foreign()->references()->on().

## Reversible down() methods
Implement real reverse logic in down() mirroring the up() change.

## Store enums as string columns
Store enums as string() columns cast to an App\Models\Enums backed enum on the model. Do not use DB enum() columns.
