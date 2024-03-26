<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class ContactTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    
    public function test_query_contact()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        
        $token = auth()->login($user);
        $contact = Contact::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('contact', ['id' => $contact->id], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'contact' => [
                        'id' => $contact->id,
                    ],
                ],
            ]);
    }

    public function test_query_contact_if_object_not_found()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('contact', ['id' => 0], ['id'])
            ->assertJsonFragment([
                'message' => 'validation',
            ]);
    }

    public function test_query_contacts()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $contact = Contact::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('contacts', ['id'])
            ->assertJsonFragment([
                'data' => [
                    'contacts' => [
                        ['id' => $contact->id]
                    ],
                ],
            ]);
    }


    public function test_create()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $contact = [
            'nome_pessoa' => 'Maria',
            'nome_cliente' => 'Empresa',
            'area_atendimento' => 'contabilidade'
        ];
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('createContact', [
                'nome_pessoa' => $contact['nome_pessoa'],
                'nome_cliente' => $contact['nome_cliente'],
                'area_atendimento' => $contact['area_atendimento'],
            ], ['nome_pessoa', 'nome_cliente', 'area_atendimento'])
            ->assertJsonFragment([
                'data' => [
                    'createContact' => [
                        'nome_pessoa' => $contact['nome_pessoa'],
                        'nome_cliente' => $contact['nome_cliente'],
                        'area_atendimento' => $contact['area_atendimento'],
                    ]
                ]
            ]);
    }

    public function test_create_with_failed_validation()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $contact = [
            'nome_pessoa' => '',
            'nome_cliente' => 'Empresa',
            'area_atendimento' => 'contabilidade'
        ];
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('createContact', [
                'nome_pessoa' => $contact['nome_pessoa'],
                'nome_cliente' => $contact['nome_cliente'],
                'area_atendimento' => $contact['area_atendimento'],
            ], ['nome_pessoa', 'nome_cliente', 'area_atendimento'])
            ->assertJsonFragment([
                'message' => 'validation',
            ]);
    }

    public function test_update()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $contact = Contact::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('updateContact', ['id' => $contact->id], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'updateContact' => [
                        'id' => $contact->id,
                    ],
                ],
            ]);
    }

    public function test_update_with_failed_validation()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('updateContact', ['id' => 0], ['id'])
            ->assertJsonFragment([
                'message' => 'validation'
            ]);
    }

    public function test_update_if_object_not_found()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('updateContact', ['id' => 0], ['id'])
            ->assertJsonFragment([
                'message' => 'validation'
            ]);
    }

    public function test_destroy()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $contact = Contact::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('deleteContact', ['id' => $contact->id], [])
            ->assertJsonFragment([
                'deleteContact' => true
            ]);
    }

    public function test_destroy_if_object_not_found()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('deleteContact', ['id' => 0], [])
            ->assertJsonFragment([
                'message' => 'validation'
            ]);
    }

    public function test_restore()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $contact = Contact::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('restoreContact', ['id' => $contact->id], [])
            ->assertJsonFragment([
                'restoreContact' => true
            ]);
    }

    public function test_restore_if_object_not_found()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('restoreContact', ['id' => 0], [])
            ->assertJsonFragment([
                'message' => 'validation'
            ]);
    }
    
}
