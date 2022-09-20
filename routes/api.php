<?php

use App\Http\Controllers\LoanController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function (Router $router) {
    $router->get('/user', function (Request $request) {
        return $request->user();
    });

    $router->post('/loans', [LoanController::class, 'create'])->name('loan.create');
    $router->get('/loans', [LoanController::class, 'index'])->name('loan.index');
    $router->get('/loans/{loan}', [LoanController::class, 'get'])->name('loan.get');
    $router->post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loan.approve');
});

$router->post('/register', [RegisterController::class, 'register'])->name('register');
$router->post('/token', [UserController::class, 'token'])->name('token.get');

