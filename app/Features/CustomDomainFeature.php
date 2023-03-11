<?php

namespace App\Features;

class CustomDomainFeature
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(mixed $scope): mixed
    {
        return true;
    }
}
