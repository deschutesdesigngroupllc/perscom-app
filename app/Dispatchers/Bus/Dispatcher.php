<?php

declare(strict_types=1);

namespace App\Dispatchers\Bus;

use Illuminate\Bus\Dispatcher as BaseDispatcher;
use Illuminate\Contracts\Container\Container;

class Dispatcher extends BaseDispatcher
{
    public function __construct(Container $app, BaseDispatcher $dispatcher)
    {
        parent::__construct($app, $dispatcher->queueResolver);
    }

    public function dispatch($command)
    {
        if (request()->header('x-perscom-notifications') === 'false') {
            return null;
        }

        return parent::dispatch($command);
    }
}
