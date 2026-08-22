---
paths:
  - 'app/Jobs/**'
---

# Jobs

## Tenancy-aware jobs
Per-organization work is a job implementing App\Contracts\RunsPerTenant + using the RunsForTenant trait: put the logic in work() (not handle()), make tenantKey nullable. Dispatch fan-out via App\Actions\Tenancy\DispatchTenantJob::for(): it batches per-tenant when tenancy is enabled and runs once against central when self-hosted. Mark SaaS-operator-only jobs with App\Contracts\RequiresTenancy so they are skipped when self-hosted. Use ConfiguresTenantQueue::configureForTenancy() so specific queues/connections apply only under tenancy (default queue otherwise).
