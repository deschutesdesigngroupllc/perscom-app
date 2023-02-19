<?php

namespace App\Contracts;

interface RepositoryContract
{
    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function findByKey($key, $value);

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id);
}
