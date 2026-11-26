# One-Time Operations

Data changes that every existing tenant needs once, such as granting the default role a new
permission, a backfill, or a one-off correction, are one-time operations in `operations/`
(timokoerber/laravel-one-time-operations), not migrations and not seeders. Migrations change
schema; seeders build a new tenant; an operation changes the data existing tenants already have.

## Shape

An operation is a bare `return new class extends OneTimeOperation` with no namespace, created with
`php artisan make:operation`. One that touches tenant data loops the tenants itself:

```php
return new class extends OneTimeOperation
{
    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant): void {
            Role::query()->where('name', 'User')->first()?->givePermissionTo('view_event');
        });
    }
};
```

- Make it idempotent, so running it twice changes nothing the second time.
- Pair it with the seeder change for new tenants. A permission the default role gains goes in
  `ShieldSeeder` and in an operation for the tenants that already exist.
- Chunk anything unbounded, since it runs inside a deploy.

## Run, Not Tested

Operations carry no tests, the same as seeders. Run each one a branch adds before merging, with
`php artisan operations:process <name> --test` against a local database.

## The Directory Holds Only Operations

Every file in `operations/` is loaded and must return an operation, so nothing else (a README, a
`.gitkeep`) may sit there. Documentation about operations lives in this file.
