<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Authentication extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserRecievesErrorsForMissingSignupFormFields()
    {
        $response = $this->post('api/signup');

        $response
            ->assertJsonFragment([
                "username" => ["The username field is required."],
                "email" => ["The email field is required."]
            ])
            ->assertStatus(422);
    }

    /**
     * Test a user can signup filling the right signup fields
     *
     * @return void
     */
    public function testUserCanSignupFillingTheRightFields()
    {

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'regular']);

        $response = $this->json('POST', 'api/signup', [
                'confirmPassword' => 'solomon1.',
                'username' => 'mazinoukah',
                'email' => 'ewomaukah@yahoo.com',
                'password' => 'solomon1.'
            ]
        );

        $response
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in'])
            ->assertSuccessful();
    }

    /**
     * Tests that a user can login using the right credewntials
     * @author ewomaukah <ewomaukah@yahoo.com>
     */
    public function testAUserCanLoginWithTheRightCredentials()
    {
        $password = 'secret';
        $user = factory(User::class)->create();
        $response = $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);


        $response
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in'])
            ->assertSuccessful();
    }


    /**
     * Tests that a user cannot login using the wrong credewntials
     * @author ewomaukah <ewomaukah@yahoo.com>
     */
    public function testAUserCannotLoginWithTheWrongCredentials()
    {

        $response = $this->json('POST', '/api/login', [
            'email' => 'email',
            'password' => 'password'
        ]);


        $response
            ->assertJsonFragment([
                "error"    => "Unauthorized",
                "message"  => "Wrong username or password"
            ])
            ->assertStatus(401);
    }
}
