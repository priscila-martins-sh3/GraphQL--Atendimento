<?php

namespace Tests\Feature;

use App\Models\Support;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_query_user()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('user', ['id' => $user->id], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                    ],
                ],
            ]);
    }
    public function test_query_user_if_object_not_found()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('user', ['id' => 0], ['id'])
            ->assertJsonFragment([
                'message' => 'validation',
            ]);
    }

    public function test_query_users()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('users', ['id'])
            ->assertJsonFragment([
                'data' => [
                    'users' => [
                        ['id' => $user->id]
                    ],
                ],
            ]);
    }

    public function test_query_supports()
    {
        //$user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $support = Support::factory()->create();
        $user = $support->user;
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('supports', ['id'])
            ->assertJsonFragment([
                'data' => [
                    'supports' => [
                        ['id' => $user->id]
                    ],
                ],
            ]);
    }

    public function test_register()
    {
        $user = [
            'name' => 'João',
            'email' => 'joao@gmail.com',
            'password' => 'teste123',
            'tipo_funcionario' => 'admin',
            'area_atuacao' => '',
        ];
        $this->mutation('register', [
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => $user['password'],
            'tipo_funcionario' => $user['tipo_funcionario'],
            'area_atuacao' => $user['area_atuacao']
        ], ['name', 'email', 'tipo_funcionario'])
            ->assertJsonFragment([
                'data' => [
                    'register' => [
                        'name' => $user['name'],
                        'email' => $user['email'],                        
                        'tipo_funcionario' => $user['tipo_funcionario'],                        
                    ]
                ]
            ]);
    }

    public function test_register_invalid()
    {
        $user = [
            'name' => 'João',
            'email' => 'joao@gmail.com',
            'password' => 'teste123',
            'tipo_funcionario' => 'desenvolvedor',  
            'area_atuacao' => '',         
        ];
        $this->mutation('register', [
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => $user['password'],
            'tipo_funcionario' => $user['tipo_funcionario'],
            'area_atuacao' => $user['area_atuacao']
        ], ['name', 'email', 'tipo_funcionario'])
            ->assertJsonFragment([
                'data' => [
                    'register' => null,
                ]
            ]);
    }

    public function test_register_support()
    {
        $user = [
            'name' => 'João',
            'email' => 'joao@gmail.com',
            'password' => 'teste123',
            'tipo_funcionario' => 'suporte',
            'area_atuacao' => 'compras',
        ];
        $this->mutation('register', [
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => $user['password'],
            'tipo_funcionario' => $user['tipo_funcionario'],
            'area_atuacao' => $user['area_atuacao']
        ], ['name', 'email', 'tipo_funcionario'])
            ->assertJsonFragment([
                'data' => [
                    'register' => [
                        'name' => $user['name'],
                        'email' => $user['email'],                        
                        'tipo_funcionario' => $user['tipo_funcionario'],                        
                    ]
                ]
            ]);
    }

    public function test_authenticate()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin', 'password' => 'teste123']);
        $this->mutation('login', [
            'email' => $user['email'],
            'password' => 'teste123',
        ], [])
            ->assertJson([
                'data' => [
                    "login" => true,
                ],
            ]);
    }

    public function test_authenticate_invalid()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $this->mutation('login', [
            'email' => 'emailinvalid',
            'password' => $user['password'],
        ], [])
            ->assertJsonFragment([
                'data' => [
                    'login' => null,
                ],
            ]);
    }

    public function test_logout()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('logout', [], [])
            ->assertJsonFragment([
                'logout' => true
            ]);
    }
}
