# Queued Jobs and Tenant Fan-Out

This directory holds the queued jobs. `Tenant/` jobs work on one organization's data, `Central/`
jobs work on the central database or send from it, `System/` jobs handle tenant lifecycle and
platform housekeeping, and `Concerns/` holds the traits that make a job tenancy-aware.

## Two Deployment Modes

Every job here has to behave under both values of `config('tenancy.enabled')`:

- **Tenancy enabled (SaaS).** Each organization is a `Tenant` with its own database. Per-tenant
  work runs once per tenant, inside that tenant's context.
- **Tenancy disabled (self-hosted).** There are no tenants. The central database holds the single
  organization's data, so per-tenant work runs once, directly against it.

Two marker contracts in `app/Contracts/` sort jobs by mode:

| Contract          | Meaning                                             | Self-hosted behavior                   |
|-------------------|-----------------------------------------------------|----------------------------------------|
| `RunsPerTenant`   | Work scoped to one organization's data, in `work()` | Runs once against the central database |
| `RequiresTenancy` | Only meaningful in the SaaS deployment              | Skipped by `DispatchTenantJob`         |

A job can carry both: `Tenant/LinkDiscordServer` is per-tenant work, but `discord_guild_id` lives
on the central `tenants` table, so it has nothing to do when self-hosted.

`RequiresTenancy` is only enforced by `DispatchTenantJob::for()`. Nothing stops a plain
`dispatch()` of a marked job, so other SaaS-only entry points guard on the config or only exist
under tenancy: the scheduled `DeleteUnverifiedRegistrations` and `RemoveInactiveAccounts` in
`bootstrap/app.php` use `->when(config('tenancy.enabled'))`, `BackupDatabasesCommand` wraps the
unmarked `Central\BackupDatabase` in an `if`, and `SetupTenantAccount` and `RemoveTenantAccount`
run from the tenant event pipelines in `app/Providers/TenancyServiceProvider.php` (plus a
`SetupTenantAccount` action in the admin `TenantResource`).

## Fan-Out Flow

Scheduled per-tenant work starts at an Artisan command in `app/Console/Commands/` (scheduled in
`bootstrap/app.php`) that calls `App\Actions\Tenancy\DispatchTenantJob::for($job, $name, $queue,
$arguments)`:

1. **Tenancy disabled.** A `RequiresTenancy` job returns `null` without dispatching. Anything else
   is dispatched once as `new $job(null, ...$arguments)`.
2. **Tenancy enabled.** It builds `new $job($tenant->getKey(), ...$arguments)` for every
   `Tenant::all()` and dispatches them as one named `Bus::batch()` on the `central` connection
   with `allowFailures()`, so one tenant's failure does not cancel the rest.

On the worker, `Concerns\RunsForTenant::handle()` returns early when the batch has been
cancelled, calls `work()` directly when tenancy is off, and otherwise runs
`Tenant::findOrFail($this->tenantKey)->run(fn () => $this->work())`. Exceptions from `work()`
propagate, so the job's tries, backoff, and failed-job handling apply per tenant.

The `central` queue connection in `config/queue.php` is flagged `'central' => true`, so the
`QueueTenancyBootstrapper` does not restore tenant context from the payload. That is why these
jobs carry `$tenantKey` and enter the tenant themselves rather than relying on being dispatched
from inside one.

## Queues and Connections

`Concerns\ConfiguresTenantQueue::configureForTenancy(?string $queue, ?string $connection =
'central')` applies a queue and connection only when tenancy is enabled; self-hosted, the job stays
on the default queue and connection. Pass `null` for either to leave it alone, as
`Tenant/PurgeApiCache` does with `connection: null` so it keeps the tenant-aware connection.
`RunsForTenant` already uses this trait.

The batch's queue wins over the job's own. `Bus::batch()->onQueue()` pushes every job onto the
batch's queue, and `DispatchTenantJob::for()` defaults `$queue` to `'default'`. A job that
configures `queue: 'backup'` or `queue: 'clean'` lands there under fan-out only because its
command passes the same `queue:` argument. Keep the two in step; the Horizon supervisors in
`config/horizon.php` serve `backup` and `clean` on the `central` connection.

## Writing a Per-Tenant Job

- Implement `RunsPerTenant` and `ShouldQueue`, and use `Batchable`, `InteractsWithQueue`,
  `Queueable`, and `RunsForTenant`. `Batchable` is required: `handle()` calls `$this->batch()`.
- Make the constructor's first parameter `public ?int $tenantKey = null`. `DispatchTenantJob`
  passes it positionally, then the `arguments` array, so extra parameters follow it (see
  `PruneApiLogs`'s `$days`).
- Call `configureForTenancy()` in the constructor, with a `queue:` when the job belongs on a
  dedicated queue.
- Put the logic in `work()`, never `handle()`. Inside it, `tenant()` returns the current `Tenant`
  under tenancy and `null` when self-hosted; guard on it when touching central tenant columns.
- Add `RequiresTenancy` when the work only makes sense with tenants.
- Dispatch it from a command through `DispatchTenantJob::for()` with a batch `name:`, the matching
  `queue:`, and any `arguments:`, then schedule the command in `bootstrap/app.php`.
- Test it in `tests/Feature/Tenant/Jobs/Tenant/<Job>Test.php` on `TenantTestCase`: call `->work()`
  directly for behavior, and assert the queue, connection, and `tenantKey` with `Queue::fake()`
  under `config(['tenancy.enabled' => true])`, as `CleanBackupsTest` does. Fan-out and self-hosted
  skipping are covered once in `tests/Feature/Tenant/Actions/Tenancy/DispatchTenantJobTest.php`.

## Linking Discord Servers

`Tenant/LinkDiscordServer`, run by `perscom:discord-link-servers`, copies each organization's
saved `IntegrationSettings::discord_settings['discord_server']` onto `tenants.discord_guild_id`,
which `DiscordInteractionService` uses to route slash commands to an organization. It skips a
tenant that has no saved server or is already linked, and leaves a server already claimed by
another tenant alone, matching the one-server-one-organization rule that
`App\Actions\Integrations\ConnectDiscordServer` enforces for new connections.

## Mass Emails

`App\Actions\Tenancy\DispatchTenantMassEmails::handle(Mail $mail)` sends an operator `Mail` to
tenants. It returns `null` when tenancy is off, when `sent_at` is set, or when the mail is neither
`send_now` nor scheduled with `send_at`. Otherwise it batches one `Central\SendMassEmail` per
recipient on the `central` connection with `allowFailures()`: the tenants listed in `recipients`,
or every tenant when that is empty. `SendMassEmail` delays itself until `send_at` for scheduled
mail and sets `sent_at` after sending.

It is called from `App\Observers\MailObserver` on both `created` and `updated`, and from the
retry action in `app/Filament/Admin/Resources/MailResource.php`. Consequences:

- The `sent_at` guard is what stops the `sent_at` update in `SendMassEmail` from dispatching again.
- Saving an unsent mail that is `send_now` or has a `send_at` dispatches a new batch each time, so
  editing a scheduled mail before it goes out queues a second set of jobs.
- `recipients` is mapped through `Tenant::find()`, so a deleted tenant's ID fails the whole
  dispatch with a type error rather than being skipped.
