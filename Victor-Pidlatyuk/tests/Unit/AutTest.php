<?php

namespace Tests\Unit;

use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * For some tests you should have user with login user1, password 12345678 and email victor.pidlatyuk09604@gmail.com
 */
class AutTest extends TestCase
{
    /**
     * Test registration with correct data
     *
     * @return void
     */
    public function testCorrectRegistration()
    {
        $response = $this->post('/api/auth/register', [
            'login'                 =>  Str::random(20),
            'password'              =>   '12345678',
            'password_confirmation' => '12345678',
            'email'                 => Str::random(10) . '@gmail.com',
            'full_name'             => 'Cool person'
        ]);
        $response->assertOk();
    }

    /**
     * Test registration with exists email
     *
     * @return void
     */
    public function testExistUserEmailRegistration()
    {
        $response = $this->post('/api/auth/register', [
            'login'                 =>  'user1',
            'password'              =>   '12345678',
            'password_confirmation' => '12345678',
            'email'                 => 'victor.pidlatyuk09604@gmail.com',
            'full_name'             => 'Cool person'
        ]);
        $response->assertSessionHasErrors(['login', 'email']);
    }

    /**
     * Test registration validation
     *
     * @return void
     */
    public function testValidationRegistration()
    {
        $response = $this->post('/api/auth/register', [
            'login'     =>  '',
            'password'  =>   '12345678',
            'email'     => Str::random(10) . '@gmail.com'
        ]);
        $response->assertSessionHasErrors(['login', 'password', 'full_name']);
    }

    /**
     * Test login with username
     *
     * @return void
     */
    public function testCorrectLoginWithUsername()
    {
        $response = $this->post('/api/auth/login', [
            'login'    =>  'user1',
            'password' =>   '12345678'
        ]);
        $response->assertOk();
    }

    /**
     * Test login with email
     *
     * @return void
     */
    public function testCorrectLoginWithEmail()
    {
        $response = $this->post('/api/auth/login', [
            'login'    =>  'victor.pidlatyuk09604@gmail.com',
            'password' =>   '12345678'
        ]);
        $response->assertOk();
    }

    /**
     * Test login validation
     *
     * @return void
     */
    public function testValidationLogin()
    {
        $response = $this->post('/api/auth/login', [
            'login'     =>  '',
            'password'  =>   '1234'
        ]);
        $response->assertSessionHasErrors(['login', 'password']);
    }

    /**
     * Test middleware with authenticated user
     *
     * @return void
     */
    public function testAuthenticated()
    {
        $token = $this->post('/api/auth/login', [
            'login'     =>  'user1',
            'password'  =>   '12345678'
        ])->getOriginalContent()["token"];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->get('/api/orders');
        $response->assertOk();
    }

    /**
     * Test middleware with unauthenticated user
     *
     * @return void
     */
    public function testUnauthenticated()
    {
        $response = $this->get('/api/orders');
        $response->assertRedirect();
    }

    /**
     * Test password reset correct
     *
     * @return void
     */
    public function testCorrectPasswordReset()
    {
        $response = $this->post('/api/auth/reset-password', ['email' => 'victor.pidlatyuk09604@gmail.com']);
        $response->assertStatus(500);
    }

    /**
     * Test password reset validation
     *
     * @return void
     */
    public function testValidationPasswordReset()
    {
        $response = $this->post('/api/auth/reset-password');
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test password reset wrong email
     *
     * @return void
     */
    public function testWrongEmailPasswordReset()
    {
        $response = $this->post('/api/auth/reset-password', ['email' => '_']);
        $response->assertNotFound();
    }


}
