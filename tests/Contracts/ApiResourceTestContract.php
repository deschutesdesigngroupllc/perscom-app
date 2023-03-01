<?php

namespace Tests\Contracts;

interface ApiResourceTestContract
{
    /**
     * @return string
     */
    public function endpoint();

    /**
     * @return string
     */
    public function model();

    /**
     * @return \Database\Factories\UserFactory
     */
    public function factory();

    /**
     * @return array
     */
    public function scopes();

    /**
     * @return array
     */
    public function storeData();

    /**
     * @return array
     */
    public function updateData();
}
