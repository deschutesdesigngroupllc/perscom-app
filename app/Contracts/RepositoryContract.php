<?php

namespace App\Contracts;

interface RepositoryContract
{
    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @return mixed
     */
    public function findByKey($key, $value);

    /**
     * @return mixed
     */
    public function findById($id);
}
