<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * @var \App\Repositories\UserRepositoryInterface
     */
    public UserRepositoryInterface $userRepository;

    /**
     * Contructor
     * 
     * @param \App\Repositories\UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (empty($user = $this->userRepository->findOneBy([
            ['email', 'admin@email.com']
        ], false))) {
            $user = new User();
            $user->name = 'Admin';
            $user->email = 'admin@email.com';
            $user->password = Hash::make('admin');
            $user->type = UserType::ADMIN->value;
            $user->save();
        }
    }
}
