<?php

namespace App\Features;

class OAuth2AccessFeature
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(mixed $scope): mixed
    {
        return true;
    }
}
