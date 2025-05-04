<?php

declare(strict_types=1);

namespace App\Models;

use Lwwcas\LaravelCountries\Models\Country as BaseCountry;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Country extends BaseCountry
{
    use CentralConnection;
}
