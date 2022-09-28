<?php

namespace Tests\Feature\Loan;

use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GetTest extends TestCase
{
    use RefreshDatabase;

    public function testGet_WhenInputValid_ReturnsLoan() : void
    {
        $user = User::factory()->create([
            'email'    => 'test@user.com',
            'password' => Hash::make('123456789'),
        ]);

        $loan = Loan::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson(route('loan.get', $loan->id), [
            'Authorization' => 'Bearer '.$user->createToken('')->plainTextToken
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($loan->id, $response->json('id'));
    }

    public function testIndex_WhenInvalidUser_ReturnsAuthenticationError() : void
    {
        $user = User::factory()->create([
            'email'    => 'test@user.com',
            'password' => Hash::make('123456789'),
        ]);

        $loan = Loan::factory()->create(['user_id' => $user->id]);

        $user1 = User::factory()->create([
            'email'    => 'test1@user.com',
            'password' => Hash::make('123456789'),
        ]);

        $response = $this->getJson(route('loan.get', $loan->id), [
            'Authorization' => 'Bearer '.$user1->createToken('')->plainTextToken,
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testIndex_WhenInvalidLoan_ReturnsAuthenticationError() : void
    {
        $user = User::factory()->create([
            'email'    => 'test@user.com',
            'password' => Hash::make('123456789'),
        ]);

        $response = $this->getJson(route('loan.get', rand(1, 100)), [
            'Authorization' => 'Bearer '.$user->createToken('')->plainTextToken,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
