<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * API Register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) : JsonResponse
    {
        $this->validate($request, [
            'name'     => 'unique:users|required',
            'email'    => 'email:rfc,dns|unique:users|required',
            'password' => 'required',
        ]);

        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('email'));
        $user->type = UserType::MEMBER->value;
        $user->save();

        $token = $user->createToken('');

        return new JsonResponse(['token' => $token->plainTextToken], Response::HTTP_CREATED);
    }
}
