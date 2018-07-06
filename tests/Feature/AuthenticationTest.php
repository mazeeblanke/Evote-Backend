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
    public function testUsersCanSignup()
    {
        $response = $this->post('/api/signup');

        // dd($response);

        $response->assertStatus(200);
    }
}
