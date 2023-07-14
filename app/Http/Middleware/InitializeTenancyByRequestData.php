<?php

namespace App\Http\Middleware;

use App\Exceptions\TenantCouldNotBeIdentified;
use Stancl\Tenancy\Resolvers\RequestDataTenantResolver;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyByRequestData extends \Stancl\Tenancy\Middleware\InitializeTenancyByRequestData
{
    /** @var string|null */
    public static $header = 'X-Perscom-Id';

    /** @var string|null */
    public static $queryParameter = 'perscom_id';

    public function __construct(Tenancy $tenancy, RequestDataTenantResolver $resolver)
    {
        self::$onFail = static function () {
            throw new TenantCouldNotBeIdentified(401, 'We could not identify the organization attempting the request. Please make sure to include the X-Perscom-Id header with your valid PERSCOM ID.');
        };

        parent::__construct($tenancy, $resolver);
    }
}
