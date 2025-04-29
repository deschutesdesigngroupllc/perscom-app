<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain;
use Stancl\Tenancy\Features\UniversalRoutes;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData as BaseInitializeTenancyByRequestData;
use Stancl\Tenancy\Resolvers\RequestDataTenantResolver;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyByRequestData extends BaseInitializeTenancyByRequestData
{
    public static $header = 'X-Perscom-Id';

    public static $queryParameter = 'apikey';

    public function __construct(Tenancy $tenancy, RequestDataTenantResolver $resolver)
    {
        self::$onFail = static function (): void {
            if (! UniversalRoutes::routeHasMiddleware(request()->route(), UniversalRoutes::$middlewareGroup)) {
                abort(401);
            }
        };

        parent::__construct($tenancy, $resolver);
    }

    protected function getPayload(Request $request): ?string
    {
        $tenant = null;

        if (static::$queryParameter && $request->has(static::$queryParameter)) {
            $tenant = $this->getTenantFromToken($request->query('apikey'));
        } elseif (static::$header && $request->hasHeader(static::$header)) {
            $tenant = $request->header(static::$header);
        } elseif ($jwt = $request->bearerToken()) {
            $tenant = $this->getTenantFromToken($jwt);
        } elseif ($request->hasCookie('perscom_api_key')) {
            $tenant = $this->getTenantFromToken($request->cookie(Passport::$cookie));
        }

        return $tenant;
    }

    protected function getTenantFromToken(string $jwt): ?string
    {
        $parser = new Parser(new JoseEncoder);

        /** @var Plain|null $token */
        $token = rescue(fn (): Token => $parser->parse($jwt), report: false);

        if (is_null($token)) {
            return null;
        }

        if (! $tenantId = $token->claims()->get('tenant')) {
            return null;
        }

        return (string) $tenantId;
    }
}
