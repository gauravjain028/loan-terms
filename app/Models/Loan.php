<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'amount',
        'terms',
        'user_id',
    ];

    /**
     * Get repayments of the Loan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class);
    }
}
