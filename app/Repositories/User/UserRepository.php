<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\Repository;

/**
 * Class User Repository
 */
final class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * Set Repository Model
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = app(User::class);
    }
}
