<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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

    public function token(Request $request) : JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
     
        $user = $this->userRepository->findOneBy([
            ['email', $request->get('email')]
        ], false);

     
        if (! $user || ! Hash::check($request->get('password'), $user->password)) {
            throw (new AuthorizationException('Unauthenticate.'));
        }
     
        $token = $user->createToken('');

        return new JsonResponse(['token' => $token->plainTextToken], Response::HTTP_OK);
    }
}

