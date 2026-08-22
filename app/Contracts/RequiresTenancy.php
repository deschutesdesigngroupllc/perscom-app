<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * A job or task that only makes sense in the multi-tenant SaaS deployment
 * (e.g. per-tenant database backups, tenant lifecycle emails). It is never
 * dispatched when the app is self-hosted with tenancy disabled.
 */
interface RequiresTenancy {}
