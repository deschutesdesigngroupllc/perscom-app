# List Search

Applies to every App panel list page: a resource's table, and any relation manager table.

## The Rule

A user who can read a value in a list column can find the row by typing it.

1. **Every column whose value a user could type is searchable**: names, titles, codes,
   identifiers, descriptions, member names, and labels.
2. **Toggle state does not matter.** Filament searches a column the user has toggled off. Never
   `->hidden()` a column to keep it out of search; a hidden column is skipped entirely.
3. **A relation column searches through its relation** with dot notation
   (`TextColumn::make('user.name')->searchable()`). A computed or accessor-backed column needs a
   `searchable(query: …)` closure against the stored columns it renders from.
4. **An enum or badge column matches the label on screen**, never the stored value. A bare
   `->searchable()` on an enum-cast column matches `going` and misses "Going", so give it a query
   that maps the term to the cases whose label contains it:

   ```php
   ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereIn(
       'status',
       collect(EventRegistrationStatus::cases())
           ->filter(fn (EventRegistrationStatus $status): bool => Str::contains(
               $status->getLabel(),
               $search,
               ignoreCase: true,
           ))
           ->map(fn (EventRegistrationStatus $status): string => $status->value),
   ))
   ```

5. **Exempt by nature**: dates, numbers, counts, booleans, colors, and computed summaries with no
   single stored value.
6. **Member names are searched on `users.name`**, the stored name every display-name format
   includes.

## Global Search

A resource searchable from the global search bar lists its attributes in
`getGloballySearchableAttributes()`, using the same columns its table searches.

## Testing

Cover a list page's search with a `searchTable()` assertion for each kind of searchable column it
has: a plain column, a relation column, and an enum column searched by its label.

```php
Livewire::test(ListEvents::class)
    ->searchTable('Range Day')
    ->assertCanSeeTableRecords([$rangeDay])
    ->assertCanNotSeeTableRecords([$patrol]);
```
