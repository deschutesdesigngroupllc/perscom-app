<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method Builder|static disabled()
 * @method Builder|static enabled()
 */
interface Enableable {}
