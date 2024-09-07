<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\TenantCouldNotBeIdentified;
use App\Models\Tenant;
use App\Support\JwtAuth\Providers\CustomJwtProvider;
use Illuminate\Http\Request;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain;
use Stancl\Tenancy\Resolvers\RequestDataTenantResolver;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyByRequestData extends \Stancl\Tenancy\Middleware\InitializeTenancyByRequestData
{
    public static $header = 'X-Perscom-Id';

    public static $queryParameter = 'perscom_id';

    public function __construct(Tenancy $tenancy, RequestDataTenantResolver $resolver)
    {
        self::$onFail = static function () {
            throw new TenantCouldNotBeIdentified(401, 'We could not identify the organization attempting the request. Please make sure to include the X-Perscom-Id header with your valid PERSCOM ID.');
        };

        parent::__construct($tenancy, $resolver);
    }

    protected function getPayload(Request $request): ?string
    {
        $tenant = null;
        if (static::$header && $request->hasHeader(static::$header)) {
            $tenant = $request->header(static::$header);
        } elseif (static::$queryParameter && $request->has(static::$queryParameter)) {
            $tenant = $request->get(static::$queryParameter);
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

        $signed = false;
        if ($tenant = Tenant::find($token->claims()->get('tenant'))?->first()) {
            $tenant->run(function () use ($token, &$signed) {
                /** @var CustomJwtProvider $provider */
                $provider = app(CustomJwtProvider::class);

                if (rescue(fn () => $provider->getConfig()->validator()->validate($token, ...$provider->getConfig()->validationConstraints()), report: false)) {
                    $signed = true;
                }
            });
        }

        if (! $signed || ! $tenant) {
            return null;
        }

        return (string) $tenant->getKey();
    }
}
