<?php

namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\Response;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testRegister_WhenInputValid_ReturnsToken() : void
    {
        $response = $this->postJson(route('register'), [
            'name'     => 'Foo',
            'email'    => 'foo@bar.com',
            'password' => '123456',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseHas((new User())->getTable(), ['email' => 'foo@bar.com', 'type' => UserType::MEMBER->value]);
    }

    public function testRegister_WhenInputInvalid_ReturnsValidationErrors() : void
    {
        $response = $this->postJson(route('register'), [
            'name'     => 'Foo',
            'email'    => 'foo@bar',
            'password' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertEquals('The email must be a valid email address. (and 1 more error)', $response->json('message'));
        $this->assertEquals('The email must be a valid email address.', $response->json('errors.email.0'));
        $this->assertEquals('The password field is required.', $response->json('errors.password.0'));
    }

    public function testRegister_WhenUserAlreadyRegistered_ReturnsValidationError() : void
    {
        User::factory()->create(['email' => 'foo@bar.com']);

        $response = $this->postJson(route('register'), [
            'name'     => 'Foo',
            'email'    => 'foo@bar.com',
            'password' => '123456',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertEquals('The email has already been taken.', $response->json('message'));
    }
}
