<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method Builder|static hidden()
 * @method Builder|static visible()
 */
interface Hideable {}
