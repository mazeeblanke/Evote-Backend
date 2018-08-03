<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Authentication extends TestCase
{
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
}
