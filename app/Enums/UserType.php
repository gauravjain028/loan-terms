<?php

declare(strict_types = 1);

namespace App\Enums;

/**
 * UserType enum represents different user types which a user can have.
 */
enum UserType: int
{
    case MEMBER = 1;
    case ADMIN = 2;

    /**
     * Determine whether the current user type is owner or not.
     *
     * @return bool
     */
    public function isAdmin() : bool
    {
        return $this === self::ADMIN;
    }
}
