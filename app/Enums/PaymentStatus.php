<?php

declare(strict_types = 1);

namespace App\Enums;

/**
 * PaymentStatus enum represents different user types which a user can have.
 */
enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
}
