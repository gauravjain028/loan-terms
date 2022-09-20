<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\Models\User;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param \App\Models\User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}