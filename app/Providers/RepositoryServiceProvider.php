<?php

declare(strict_types = 1);

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\LoanRepository;
use App\Repositories\LoanRepositoryInterface;
use App\Repositories\RepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->app->bind(RepositoryInterface::class, BaseRepository::class);
        $this->app->bind(LoanRepositoryInterface::class, LoanRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
