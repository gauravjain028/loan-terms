<?php

namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\Response;
use Tests\TestCase;

class GetTokenTest extends TestCase
{
    use RefreshDatabase;

    public function testGetToken_WhenInputValid_ReturnsToken() : void
    {
        User::factory()->create(['email' => 'foo@bar.com']);

        $response = $this->postJson(route('token.get'), [
            'email'    => 'foo@bar.com',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertNotEmpty($response->json('token'));
    }

    public function testGetToken_WhenInputPasswordInvalid_ReturnsErrors() : void
    {
        User::factory()->create(['email' => 'foo@bar.com']);

        $response = $this->postJson(route('token.get'), [
            'email'    => 'foo@bar.com',
            'password' => 'password1',
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testGetToken_WhenInputEmailInvalid_ReturnsErrors() : void
    {
        User::factory()->create(['email' => 'foo1@bar.com']);

        $response = $this->postJson(route('token.get'), [
            'email'    => 'foo@bar.com',
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
