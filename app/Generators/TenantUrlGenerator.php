<?php

declare(strict_types=1);

namespace App\Generators;

use Illuminate\Routing\UrlGenerator;

class TenantUrlGenerator extends UrlGenerator
{
    public function route($name, $parameters = [], $absolute = true): string
    {
        if (in_array('tenant', $this->request->route()?->parameterNames()) && ! array_key_exists('tenant', $parameters) && tenancy()->initialized) {
            $parameters['tenant'] = tenant()->slug;
        }

        return parent::route($name, $parameters, $absolute);
    }
}
