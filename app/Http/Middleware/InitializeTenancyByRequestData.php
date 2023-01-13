<?php

namespace App\Http\Middleware;

class InitializeTenancyByRequestData extends \Stancl\Tenancy\Middleware\InitializeTenancyByRequestData
{
    /** @var string|null */
    public static $header = 'X-Perscom-Id';

    /** @var string|null */
    public static $queryParameter = 'perscom_id';
}
