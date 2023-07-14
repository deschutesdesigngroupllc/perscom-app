<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryContract
{
    public function getAll(): Collection;

    public function findByKey(string $key, mixed $value): Builder|Model;

    public function findById(mixed $id): Builder|Collection|Model;
}
