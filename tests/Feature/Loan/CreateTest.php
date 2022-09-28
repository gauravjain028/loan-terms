<?php

namespace Tests\Feature\Loan;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    public function testCreate_WhenInputValid_ReturnsLoan() : void
    {
        $user = User::factory()->create([
            'email'    => 'test@user.com',
            'password' => Hash::make('123456789'),
        ]);

        $response = $this->postJson(route('loan.create'), [
            'amount' => '10',
            'terms'  => '3',
        ], [
            'Authorization' => 'Bearer '.$user->createToken('')->plainTextToken
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertEquals(10, $response->json('amount'));
        $this->assertEquals(3, count($response->json('repayments')));
        $this->assertEquals(3.33, $response->json('repayments.0.amount'));
        $this->assertEquals(3.33, $response->json('repayments.1.amount'));
        $this->assertEquals(3.34, $response->json('repayments.2.amount'));

        $repaymentDate = Carbon::now();
        $repaymentDate = $repaymentDate->copy()->addDays(env('TERM_DURATION', 7));
        $this->assertEquals($repaymentDate->format('Y-m-d'), $response->json('repayments.0.repayment_date'));
        $repaymentDate = $repaymentDate->copy()->addDays(env('TERM_DURATION', 7));
        $this->assertEquals($repaymentDate->format('Y-m-d'), $response->json('repayments.1.repayment_date'));
        $repaymentDate = $repaymentDate->copy()->addDays(env('TERM_DURATION', 7));
        $this->assertEquals($repaymentDate->format('Y-m-d'), $response->json('repayments.2.repayment_date'));
    }

    public function testCreate_WhenInvalidUser_ReturnsAuthenticationError() : void
    {

        $response = $this->postJson(route('loan.create'), [
            'amount' => '10',
            'terms'  => '3',
        ], [
            'Authorization' => 'Bearer foo|bar',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreate_WhenInvalidData_ReturnsValidationErrors() : void
    {
        $user = User::factory()->create([
            'email'    => 'test@user.com',
            'password' => Hash::make('123456789'),
        ]);

        $response = $this->postJson(route('loan.create'), [
            'amount' => '0',
            'terms'  => '0',
        ], [
            'Authorization' => 'Bearer '.$user->createToken('')->plainTextToken
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertEquals('The amount must be greater than 0. (and 1 more error)', $response->json('message'));
        $this->assertEquals('The amount must be greater than 0.', $response->json('errors.amount.0'));
        $this->assertEquals('The terms must be greater than 0.', $response->json('errors.terms.0'));
    }
}
