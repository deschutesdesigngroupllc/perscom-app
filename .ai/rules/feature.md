---
paths:
  - 'tests/Feature/**'
---

# Feature

## Extend the tenant/central base TestCase
Feature tests extend TenantTestCase (tenant-scoped) or CentralTestCase (central). Rely on the base classes' tenant DB creation, migration, seeding, and tenancy initialization rather than setting up tenancy in the test.

## Use Livewire::test(), not Pest's livewire() helper
In class-based PHPUnit tests, Pest's `livewire()` helper calls `test()`, which returns a deferred Pest TestCall. Chained assertions never run, so the test passes without checking anything. Use `Livewire\Livewire::test(...)` instead. After a component redirects its instance is gone, so use Livewire's `assertHasNoErrors()` rather than Filament's `assertHasNoFormErrors()` after a redirecting call.
