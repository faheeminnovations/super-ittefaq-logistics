<?php

namespace App\Repositories\Contracts;

interface RoleRepositoryInterface extends RepositoryInterface
{
    public function findByName(string $name);
}
