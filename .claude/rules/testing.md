# Testing

How tests are shaped. Where they live, their base classes, and their style are set in
`.ai/rules/tests.md` and `.ai/rules/feature.md`: PHPUnit classes with `test_snake_case` methods,
extending `TenantTestCase` or `CentralTestCase`, under `tests/Feature/{Tenant|Central}/`.

## Shape

- Arrange, act, assert in every test, without comments labeling the three parts.
- One behavior per test. A method name says the behavior, not the method under test:
  `test_members_who_cannot_view_events_are_refused`.
- Test behavior, not implementation. Assert what a user, a response, or the database sees.

## Data Providers

Prefer a data provider with named `yield` cases over one test hard-coding many cases.

```php
public static function labels(): Generator
{
    yield 'sentence case' => ['First name', 'First Name'];
    yield 'acronyms' => ['api key', 'API Key'];
}

#[DataProvider('labels')]
public function test_title_case(string $label, string $expected): void
{
    $this->assertSame($expected, Label::titleCase($label));
}
```

## Users and Permissions

`UserFactory` gives every user the default panel role, which grants what a member can do. A test
of a member who lacks access starts from no role and adds only what the page itself needs:

```php
$member = User::factory()->createQuietly()->syncRoles([])->givePermissionTo('view_calendar');
```

A page that embeds a widget also needs the widget's own permission (`view_calendar_widget`), or the
page answers 403 before the assertion under test runs.

## Time

Move the clock with `$this->travelTo()` or `$this->travel()`, never `Date::setTestNow()` or
`Carbon::setTestNow()`.

## Shared Builders

Setup that builds the same records in more than one test class lives in one trait, not in a
private builder copied into each class. When a trait almost fits, extend it with a parameter that
has a default rather than writing a near copy beside it.

## Prove the Fix

A bug fix comes with a test that fails without it. Run that test against the code before the fix
when it is cheap to do, so a test that passes either way is caught.

## Running

- `composer test-filter <pattern>` runs the tests matching a pattern.
- `php artisan test --compact <path>` runs one file or directory; it takes exactly one path.
- `composer test` runs the full suite in parallel. Two runs at once share the test databases and
  fail each other in ways that look like a broken branch, so check nothing else is running first.
