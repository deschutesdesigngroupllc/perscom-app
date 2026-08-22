---
paths:
  - 'tests/Feature/**'
---

# Feature

## Extend the tenant/central base TestCase
Feature tests extend TenantTestCase (tenant-scoped) or CentralTestCase (central). Rely on the base classes' tenant DB creation, migration, seeding, and tenancy initialization rather than setting up tenancy in the test.
