<?php

namespace App\Contracts;

interface RepositoryContract
{
    public function getAll(): mixed;

    public function findByKey(string $key, mixed $value): mixed;

    public function findById(string|int $id): mixed;
}
