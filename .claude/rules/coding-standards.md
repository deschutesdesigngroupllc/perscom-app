# Coding Standards

How PHP is written in this repository. Area conventions that apply to one folder (actions, models,
policies, resources, tests) live in `.ai/rules/` and are listed in `.ai/rules/index.md`; this file
holds the standards that cut across all of them. When the two disagree, `.ai/rules/` describes the
code as it is and wins, and this file has a bug.

Pint (`pint.json`), Rector (`rector.php`), and PHPStan (`phpstan.neon`) own formatting, imports,
strict types, and the rewrites they make. Nothing here restates what those tools already enforce.

## Directory Structure

Follow Laravel defaults: `php artisan make:*` decides where a file goes. Beyond the skeleton, these
folders carry a pattern the app has committed to:

| Directory           | Holds                                                               |
|---------------------|---------------------------------------------------------------------|
| `app/Actions/`      | Single-purpose classes with one `handle()` (`.ai/rules/actions.md`) |
| `app/Ai/`           | AI assistant tools and agents                                       |
| `app/Contracts/`    | Interfaces                                                          |
| `app/Data/`         | DTOs extending spatie/laravel-data `Data`                           |
| `app/Filament/`     | Panels: `Admin` (central), `App` (tenant), shared `Actions`         |
| `app/Forms/`        | Reusable Filament form components built from a static `make()`      |
| `app/Mcp/`          | MCP server tools                                                    |
| `app/Models/Enums/` | Backed enums                                                        |
| `app/Services/`     | Stateless services exposing static methods                          |
| `app/Settings/`     | spatie/laravel-settings classes                                     |
| `app/Support/`      | Small shared helpers (`Brand`, `Label`, and the like)               |
| `app/Traits/`       | Model concern traits (`Has*`, `CanBe*`, `Clears*`)                  |
| `operations/`       | One-time data operations, see `one-time-operations.md`              |

There is no repository layer and no domain or module folder. Query Eloquent directly.

## Naming Conventions

| Thing     | Convention                   | Example                     |
|-----------|------------------------------|-----------------------------|
| Actions   | Verb + Noun                  | `CreateSpecialty`           |
| Commands  | Noun or Verb + Command       | `CalculateSchedulesCommand` |
| Events    | Past tense                   | `RecordCreated`             |
| Jobs      | Verb + Noun                  | `PruneApiLogs`              |
| Models    | Singular PascalCase          | `CombatRecord`              |
| Observers | Model + Observer             | `UserObserver`              |
| Policies  | Model + Policy               | `EventPolicy`               |
| Services  | Noun + Service               | `CalendarFeedService`       |
| Tests     | Class under test + Test      | `CalendarFeedServiceTest`   |
| Enums     | Singular PascalCase, no Enum | `EventRegistrationStatus`   |

Enum cases are `UPPER_SNAKE_CASE` (see `.ai/rules/enums.md`).

## Authorization Goes Through Policies

Check access with `can()` against a policy ability, never against a permission string. Permission
strings belong inside the policy, which maps each ability to its Spatie permission.

```php
// BAD — skips the policy
$user->can('view_event');
$user->hasPermissionTo('view_any_event');

// GOOD — a record
$user->can('view', $event);

// GOOD — no record yet ("can they see events at all")
$user->can('view', Event::class);
```

A class-level check on an ability that normally takes a record works because the policy method
declares the record optional (`?Event $event = null`). `viewAny` is the admin list page: a member
can view a record without it, so never gate a member-facing surface on `viewAny`.

## Form State Access

Read Filament form state with `Get`'s typed readers rather than casting `$get('key')`:
`string()`, `integer()`, `float()`, `boolean()`, `array()`, `date()`, `enum()`, plus the
`filled()` and `blank()` presence checks. Keep the raw `$get('key')` only to test for `=== null`
or to pass a value through unchanged.

```php
// BAD
(bool) $get('enabled')

// GOOD
$get->boolean('enabled')
```

### Enum-Bound Fields Use `$get->enum()`

A field declared with `->options(SomeEnum::class)` holds the enum case in live state, not its
`->value`. Reading it as a string fatals the moment the user picks an option and Filament
re-evaluates a `visible()` or `required()` closure.

```php
// BAD — "Object of class EventRegistrationStatus could not be converted to string"
->visible(fn (Get $get): bool => $get('status') === EventRegistrationStatus::GOING->value)

// GOOD
->visible(fn (Get $get): bool => $get->enum('status', EventRegistrationStatus::class, isNullable: true)
    === EventRegistrationStatus::GOING)
```

Never write a shim that normalizes "string or enum" yourself. The component's enum state cast does
that conversion in both directions; when a value arrives in the wrong shape, fix the seam that
produced it (a missing `->options(Enum::class)`, a `->value` written into `fillForm`).

## Emptiness Checks

`.ai/rules/app.md` makes `blank()` and `filled()` the emptiness check. Two cases they do not cover:

- A collection uses its own predicates: `isEmpty()` and `isNotEmpty()`, never `count() === 0`.
- Zero is not blank. `blank(0)`, `blank('0')`, and `blank(false)` are all `false`, so a check for
  `=== 0` or `=== false` stays as it is.

## Plain Array Searches

Collections are preferred (`.ai/rules/app.md`). For a small plain array where wrapping it in a
collection would be unnatural, use PHP's array search functions instead of a `foreach` whose body
is a single `if` that returns:

| The loop returns   | Use                |
|--------------------|--------------------|
| `true` on a match  | `array_any()`      |
| `false` on a match | `array_all()`      |
| The matching value | `array_find()`     |
| The matching key   | `array_find_key()` |

## Line Length

Lines a change adds or edits never pass 120 columns, and a docblock title stays within 80. Lines
the change does not touch are left as they are.

- A signature or call that runs over goes one parameter or argument per line, with a trailing
  comma.
- A long data-provider row breaks across lines, and a payload repeated in every row is hoisted
  into a local variable.

### Checking It

Neither Pint nor PHPStan measures width. This prints every added PHP line over 120 columns and
every new docblock title over 80, as `file:line`:

```bash
git diff -U0 "$(git merge-base origin/master HEAD)" -- '*.php' ':!*.blade.php' | perl -CSD -ne '
    if (m{^\+\+\+ b/(.*)}) { $f = $1; next }
    if (/^@@ -\S+ \+(\d+)/) { $n = $1; $p = ""; next }
    next unless s/^\+//;
    chomp; $w = length;
    print "$f:$n: $w columns, over 120\n" if $w > 120;
    print "$f:$n: docblock title $w columns, over 80\n"
        if $w > 80 && $p =~ m{^\s*/\*\*\s*$} && m{^\s*\* [^@]};
    $p = $_; $n++;
'
```

It diffs the working tree against the fork point, so uncommitted edits count. A file never added
to git is not in the diff: `git add -N <file>` brings it in. No output means no violations.

## Closures

Closures have typed parameters and a return type. Prefer an arrow function for a single short
expression, and a full closure for several statements or a condition too long to read on one line.

```php
// GOOD
$users->map(fn (User $user): string => $user->name);

// GOOD — several statements
$records->each(function (AwardRecord $record): void {
    $record->user->notify(new NewAwardRecord($record));
    $record->touch();
});
```

## Ternaries

Never nest a ternary. Use sequential assignments, an early return, or a `match (true)`. Combining
`??` with `?:` on one line is fine.

```php
// BAD
$label = $user ? ($user->rank ? $user->rank->name : 'Unranked') : 'Unknown';

// GOOD
$label = match (true) {
    ! $user instanceof User => 'Unknown',
    $user->rank === null => 'Unranked',
    default => $user->rank->name,
};
```

## Exceptions

### `@throws`

List every exception a method can let escape, so a caller can decide whether to handle it: the
ones it throws, the implicit ones (`UnhandledMatchError` from a non-exhaustive `match`,
`JsonException` from `JSON_THROW_ON_ERROR`), and the ones a call it makes declares and it does not
catch. Describe each as a `when …` clause with no trailing period.

```php
/**
 * Resolve the handler for the given trigger.
 *
 * @throws UnhandledMatchError  when the trigger has no handler
 */
```

A caller of a method that declares `@throws` catches it, converts it to an exception it lists, or
lists the same `@throws`, until a boundary handles it: a controller, middleware, a Livewire or
Filament action, a job, a command, a listener, a notification, an MCP tool, or an operation.

Catch the most specific type at the point that can act on it. Below a boundary, catch `Throwable`
or `Exception` only to clean up and rethrow, or to keep one failed item from stopping a batch, and
then `report()` it. Never swallow it.

### JSON

`json_decode()` and `json_encode()` pass `JSON_THROW_ON_ERROR`, the method lists
`@throws JsonException`, and nothing reads `json_last_error()`. The one exception is an encode for
display or logging that must never fail, which passes `JSON_PARTIAL_OUTPUT_ON_ERROR` instead.

### Custom Exceptions

Throw a custom exception from `app/Exceptions/`, not `Exception` or `RuntimeException`, when a
caller can act on the difference. A built-in `InvalidArgumentException` for a bad argument stays,
and so does a `LogicException` for a state only a bug can reach.

- One class names one kind of failure, not one throw site. Reuse it through a named constructor
  before adding a sibling.
- New code throws with `if (…) { throw new …; }`, not `throw_if()` or `throw_unless()`, so the IDE
  and PHPStan see the exception.

## Docblocks

The PHP guidelines in `CLAUDE.md` prefer a docblock over an inline comment. This is the shape one
takes; the `docblocks` skill is the fuller judgment guide.

- **A method docblock opens with a one-line title.** Imperative, verb-first, ending in a period,
  within 80 columns: `Resolve the member's private feed token.` If it wraps, tighten it or extract
  the code until a short title says it. A constructor, a framework lifecycle method, or an override
  of a framework method may carry tags alone.
- **At most one fact follows the title**, and only when a caller would trip without it: a side
  effect, an edge case, or a precondition. Never an account of what the method does line by line,
  or why it was built that way. A method that needs more has code to extract.
- **Then the tags**, after a blank line: `@template`, `@param`, `@return`, a blank line, `@throws`.
  Tag descriptions start lowercase and take no trailing period. A tag that only repeats the
  signature is dropped.
- **No docblock when the signature says it all.** A docblock that only echoes typed parameters
  and returns is deleted, not titled. A method overriding a documented parent or contract method
  gets none; tools inherit the parent's.
- **A class docblock carries tags only** (`@mixin`, `@template`, `@property`, `@method`,
  `@phpstan-type`). A new class gets no prose summary, and an existing one is trimmed when a change
  touches the class.
- **Properties and methods use the multi-line form.** The inline `/** @var … */` form is only for
  a variable inside a method or closure.
- **Short class names, not FQCNs**, in every tag. Pint imports the class.
- **Avoid inline comments.** A comment explaining a block is a method waiting to be extracted.
  Keep one only for a constraint the code cannot show, or where there is nothing to extract into
  (a config file, a migration, a route file). A comment never carries an issue or PR number, the
  history of a change, or an open question.
- **Never edit a model's `@property` block by hand.** It is generated by `composer ide`; add a
  PHPStan ignore when the generated block is wrong.

```php
/**
 * Get the members registered for the event.
 *
 * @return BelongsToMany<User, $this>
 */
public function registrations(): BelongsToMany
```

## Type Hints

Type every parameter, return, and closure signature. Annotate array and iterable types for
PHPStan, using its narrowed types where they apply: `non-empty-string`, `positive-int`,
`class-string<T>`, `list<T>`. When an array is complex enough to need an `array{key: type}` shape
in more than one place, make it a `Data` class instead.

## Laravel Helpers Over Raw PHP

Use `Str::*` and `Arr::*` over raw string and array functions, and a collection over
`array_map()` or `array_filter()`. Rector converts some of these; write the rest that way.

```php
// BAD
$slug = strtolower(trim($name));
if (str_contains($slug, ' ')) { ... }

// GOOD
$slug = Str::lower(Str::trim($name));
if (Str::contains($slug, ' ')) { ... }
```

- Format numbers with `Number::format()`, not `number_format()`, unless the call sets its own
  separators.
- Use the `File` facade for the local filesystem (`File::exists()`, `File::get()`,
  `File::put()`), and `Storage::` for anything on a disk.

## Dedicated Negations

When Laravel offers a dedicated negation, use it instead of negating the positive method:
`doesntExist()`, `Str::doesntContain()`, `Str::doesntStartWith()`, `Str::doesntEndWith()`,
`isNotEmpty()`, and `File::missing()`.

```php
// BAD
if (! $query->exists()) { ... }

// GOOD
if ($query->doesntExist()) { ... }
```

## Repeated Queries Become Scopes

A query condition repeated in three or more places becomes a local scope, written as a
`scopeXxx()` method (see `.ai/rules/models.md`).
