<?php

declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Loan;

/**
 * Class LoanRepository
 *
 * @package App\Repositories
 */
class LoanRepository extends BaseRepository implements LoanRepositoryInterface
{
    /**
     * LoanRepository constructor.
     *
     * @param \App\Models\Loan $model
     */
    public function __construct(Loan $model)
    {
        parent::__construct($model);
    }
}