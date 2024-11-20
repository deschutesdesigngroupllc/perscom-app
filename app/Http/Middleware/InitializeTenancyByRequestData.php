<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData as BaseInitializeTenancyByRequestData;
use Stancl\Tenancy\Resolvers\RequestDataTenantResolver;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyByRequestData extends BaseInitializeTenancyByRequestData
{
    public static $header = 'X-Perscom-Id';

    public static $queryParameter = 'perscom_id';

    public function __construct(Tenancy $tenancy, RequestDataTenantResolver $resolver)
    {
        self::$onFail = static function () {
            abort(401);
        };

        parent::__construct($tenancy, $resolver);
    }

    protected function getPayload(Request $request): ?string
    {
        $tenant = null;

        if (static::$queryParameter && $request->has(static::$queryParameter)) {
            $tenant = $request->get(static::$queryParameter);
        } elseif (static::$header && $request->hasHeader(static::$header)) {
            $tenant = $request->header(static::$header);
        } elseif ($request->bearerToken()) {
            $tenant = $this->getTenantFromToken($request);
        }

        return $tenant;
    }

    protected function getTenantFromToken(Request $request): ?string
    {
        if (! $request->bearerToken()) {
            return null;
        }

        $parser = new Parser(new JoseEncoder);

        /** @var Plain|null $token */
        $token = rescue(fn () => $parser->parse($request->bearerToken()), report: false);

        if (is_null($token)) {
            return null;
        }

        if (! $tenantId = $token->claims()->get('tenant')) {
            return null;
        }

        return (string) $tenantId;
    }
}
