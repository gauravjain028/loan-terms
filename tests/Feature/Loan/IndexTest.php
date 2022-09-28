<?php

namespace Tests\Feature\Loan;

use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex_WhenInputValid_ReturnsLoans() : void
    {
        $user = User::factory()->create([
            'email'    => 'test@user.com',
            'password' => Hash::make('123456789'),
        ]);

        $loan1 = Loan::factory()->create(['user_id' => $user->id]);
        $loan2 = Loan::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson(route('loan.index'), [
            'Authorization' => 'Bearer '.$user->createToken('')->plainTextToken
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals(2, count($response->json()));
        $this->assertEquals($loan1->id, $response->json('0.id'));
        $this->assertEquals($loan2->id, $response->json('1.id'));
    }

    public function testIndex_WhenInvalidUser_ReturnsAuthenticationError() : void
    {
        $response = $this->getJson(route('loan.index'),[
            'Authorization' => 'Bearer foo|bar',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testIndex_WhenDifferentUser_ReturnsEmptyLoans() : void
    {
        $user = User::factory()->create([
            'email'    => 'test@user.com',
            'password' => Hash::make('123456789'),
        ]);

        $user2 = User::factory()->create([
            'email'    => 'test1@user.com',
            'password' => Hash::make('123456789'),
        ]);

        $loan1 = Loan::factory()->create(['user_id' => $user->id]);
        $loan2 = Loan::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson(route('loan.index'), [
            'Authorization' => 'Bearer '.$user2->createToken('')->plainTextToken
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals(0, count($response->json()));
    }
}
