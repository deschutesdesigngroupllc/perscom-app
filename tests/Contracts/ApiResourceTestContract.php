<?php

namespace Tests\Contracts;

use Illuminate\Database\Eloquent\Factories\Factory;

interface ApiResourceTestContract
{
    public function endpoint(): string;

    public function model(): string;

    public function controller(): string;

    public function factory(): Factory;

    public function scopes(): array;

    public function storeData(): array;

    public function updateData(): array;
}
