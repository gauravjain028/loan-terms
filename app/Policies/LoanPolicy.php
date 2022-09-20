<?php

namespace App\Policies;

use App\Enums\UserType;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given loan can be viewed by the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Loan  $loan
     * @return bool
     */
    public function view(User $user, Loan $loan)
    {
        return $user->id === $loan->user_id;
    }

    /**
     * Determine if the given loan can be approved by the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Loan  $loan
     * @return bool
     */
    public function approve(User $user, Loan $loan)
    {
        return $user->type === UserType::ADMIN->value;
    }
}
