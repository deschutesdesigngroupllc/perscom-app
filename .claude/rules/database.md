# Database

Schema conventions for migrations and the models over them. Where migrations live, foreign keys
via `foreignId()->constrained()`, reversible `down()` methods, and enums stored as strings are set
in `.ai/rules/migrations.md`. Data changes for existing tenants are one-time operations, see
`one-time-operations.md`.

## Keys

Tables use auto-incrementing big-integer keys (`$table->id()`) and reference each other with
`foreignId()->constrained()`. A relationship names no foreign key column when it follows the
convention: `belongsTo(Unit::class)` resolves `unit_id`.

## Column Order

MySQL can place a later column with `->after()`, but a new table is written in this order:

1. `id`
2. Parent and owner references (`*_id`), most fundamental first, then each polymorphic pair
   (`x_type`, `x_id`) together
3. Descriptive attributes grouped by topic, identifying columns first. Keep pairs together: a
   start and end, an amount and its unit, a flag and its detail. A sort column (`order`) is the
   last attribute
4. Status or state columns, after the sort column
5. Nullable `*_at` lifecycle columns in chronological order, each directly after its actor column
   (`approved_by` before `approved_at`)
6. `created_at` and `updated_at`

Indexes and unique constraints follow the last column. A pivot table holds its keys, then its
payload, then its timestamps. A new table, for example:

```php
Schema::create('event_check_ins', function (Blueprint $table): void {
    $table->id();
    $table->foreignId('event_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('status');
    $table->timestamp('checked_in_at')->nullable();
    $table->timestamps();

    $table->unique(['event_id', 'user_id']);
});
```

## Ordering Records

A model the user sorts by hand implements `Spatie\EloquentSortable\Sortable` and uses the
`App\Traits\CanBeOrdered` trait, over an integer `order` column
(`$table->integer('order')->default(0)`). Never add a bespoke `position` or `sort_order` column
with hand-written reordering.

```php
class Rank extends Model implements HasLabel, Sortable
{
    use CanBeOrdered;
}
```

When records are ordered within a parent rather than globally, override `buildSortQuery()` to scope
it. Query with the `ordered()` scope and reorder with `Model::setNewOrder($ids)`.

## Hard Deletes

Models hard-delete. Do not add `SoftDeletes` or a `deleted_at` column to a new table by default;
it takes a real need, such as an archive and restore workflow or a record that must outlive its
source, to earn one. A status or boolean column usually says what is meant more directly.
