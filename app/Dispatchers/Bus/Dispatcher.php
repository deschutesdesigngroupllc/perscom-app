<?php

namespace App\Dispatchers\Bus;

use Illuminate\Bus\Dispatcher as BaseDispatcher;

class Dispatcher extends BaseDispatcher
{
    public function __construct($app, $dispatcher)
    {
        parent::__construct($app, $dispatcher->queueResolver);
    }

    public function dispatch($command)
    {
        if (request()->header('x-perscom-notifications') === 'false') {
            return null;
        }

        return parent::dispatchToQueue($command);
    }
}
