<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testBasicTest2()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function testBasicTest3()
    {
        $response = $this->get('/password_init');
        $response->assertStatus(200);
    }

    public function testBasicTestError400()
    {
        $response = $this->get('/error/400');
        $response->assertStatus(400);
    }

    public function testBasicTestError401()
    {
        $response = $this->get('/error/401');
        $response->assertStatus(401);
    }

    public function testBasicTestError404()
    {
        $response = $this->get('/error/404');
        $response->assertStatus(404);
    }

    public function testBasicTestError419()
    {
        $response = $this->get('/error/419');
        $response->assertStatus(419);
    }

    public function testBasicTestError500()
    {
        $response = $this->get('/error/500');
        $response->assertStatus(500);
    }

    public function testBasicTestError503()
    {
        $response = $this->get('/error/503');
        $response->assertStatus(503);
    }
}
