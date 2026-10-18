---
paths:
  - 'app/Console/Commands/**'
---

# Commands

## perscom-prefixed commands that return exit codes
Name command classes *Command, put the signature in the perscom: namespace, and return static::SUCCESS / static::FAILURE from handle(): int. For per-tenant work, dispatch a tenant job via DispatchTenantJob::for() instead of looping over tenants inline.
