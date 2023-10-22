<?php

namespace App\Contracts;

interface RepositoryContract
{
    public function getAll(): mixed;

    public function findByKey($key, $value): mixed;

    public function findById($id): mixed;
}
