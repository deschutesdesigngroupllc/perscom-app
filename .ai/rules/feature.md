---
paths:
  - 'tests/Feature/**'
---

# Feature

## Extend the tenant/central base TestCase
Feature tests extend TenantTestCase (tenant-scoped) or CentralTestCase (central). Rely on the base classes' tenant DB creation, migration, seeding, and tenancy initialization rather than setting up tenancy in the test.

## Use Livewire::test(), not Pest's livewire() helper
In class-based PHPUnit tests, Pest's `livewire()` helper calls `test()`, which returns a deferred Pest TestCall. Chained assertions never run, so the test passes without checking anything. Use `Livewire\Livewire::test(...)` instead. After a component redirects its instance is gone, so use Livewire's `assertHasNoErrors()` rather than Filament's `assertHasNoFormErrors()` after a redirecting call.

## Inherit DB reset from the base test case
Do not add RefreshDatabase, DatabaseTruncation, or DatabaseMigrations to tests. Extend TenantTestCase or CentralTestCase, which already reset the central DB and handle the tenant database, tenancy initialization, and rollback transaction.

## Mirror app namespaces under Tenant/Central
Place a test at tests/Feature/{Tenant|Central}/ plus the class's app namespace path (e.g. app/Jobs/Tenant/Foo.php → tests/Feature/Tenant/Jobs/Tenant/FooTest.php). Choose Tenant vs Central by the base test case it needs, not the app namespace. Write Feature tests, not Unit tests.

## Fake facades per test; HTTP is locked
Call Queue/Notification/Mail/Bus/Event fakes inside the test method that needs them, not in setUp(). Stray HTTP is blocked globally by the base TestCase, so fake any outbound request with Http::fake().
