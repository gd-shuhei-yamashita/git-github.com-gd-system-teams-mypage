<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Tests\TestCase;

/**
 * ログインテスト
 */
class UserLoginTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * CustomerCodeでログイン
     */
    public function testUserLoginByCustomerCode()
    {
        // ログインをリクエスト
        $response = $this->from('/login')->post('/login', [
            'customer_code' => 'ADMN001003',
            'password'      => 'Aaaa123456',
        ]);

        // ホーム画面にリダイレクト遷移される設定か
        $response->assertStatus(302);
        $response->assertRedirect('/home');

        // ホーム画面へ
        // $response2 = $this->get('/home'); // email/first
        // $response2->assertSee("ホーム");
    }

    /**
     * emailでログイン
     */
    public function testUserLoginByEmail()
    {
        // ログインをリクエスト
        $response = $this->from('/login')->post('/login', [
            'customer_code' => 'test03@example.com',
            'password'      => 'Aaaa123456',
        ]);

        // ホーム画面にリダイレクト遷移される設定か
        $response->assertStatus(302);
        $response->assertRedirect('/home');

    }

}