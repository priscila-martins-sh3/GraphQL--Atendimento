<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Service;
use App\Models\Support;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class ServiceTest extends TestCase
{
    use RefreshDatabase;
    //use WithFaker;

    public function test_query_service()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $service = Service::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('service', ['id' => $service->id], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'service' => [
                        'id' => $service->id,
                    ],
                ],
            ]);
    }
    public function test_query_service_if_object_not_found()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('service', ['id' => 0], ['id'])
            ->assertJsonFragment([
                'message' => 'validation',
            ]);
    }

    public function test_query_services()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $services = Service::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('services', ['id'])
            ->assertJsonFragment([
                'data' => [
                    'services' => [
                        ['id' => $services->id]
                    ],
                ],
            ]);
    }

    public function test_query_not_finished()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $service = Service::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('notFinished', ['data' => '2024-03-26'], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'notFinished' => [
                        ['id' => $service->id,]
                    ],
                ],
            ]);
    }
      
    public function test_query_type()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $service = Service::factory()->create(['tipo_servico' => 'tirar_duvida']);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('type', ['data' => '2024-03-26', 'tipo_servico' => 'tirar_duvida'], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'type' => [
                        ['id' => $service->id,]
                    ],
                ],
            ]);
    }  

    public function test_query_support()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $support = Support::factory()->create();
        $service = Service::factory()->create(['support_id' => $support->id]);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('type', ['data' => '2024-03-26', 'support_id' => $support->id], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'type' => [
                        ['id' => $service->id,]
                    ],
                ],
            ]);
    }  

    public function test_query_client()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $contact = Contact::factory()->create();
        $service = Service::factory()->create(['contact_id' => $contact->id]);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('client', ['data' => '2024-03-26', 'contact_id' => $contact->id], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'client' => [
                        ['id' => $service->id,]
                    ],
                ],
            ]);
    }  

    public function test_query_area()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $contact = Contact::factory()->create(['area_atendimento' => 'compras']);
        $service = Service::factory()->create(['contact_id' => $contact->id]);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query('area', ['data' => '2024-03-26', 'area_atendimento' => 'compras'], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'area' => [
                        ['id' => $service->id,]
                    ],
                ],
            ]);
    }  

    public function test_create()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $contact = Contact::factory()->create();
        $service = [
            'tipo_servico' => 'tirar_duvida',
            //'encerrado' => false,
            'informacoes' => null,
            'support_id' => null,
            'contact_id' => $contact->id,
        ];
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('createService', [
                'tipo_servico' => $service['tipo_servico'],
                //'encerrado' => $service['encerrado'],
                'informacoes' => $service['informacoes'],
                'support_id' => $service['support_id'],
                'contact_id' => $service['contact_id'],
            ], ['tipo_servico', 'encerrado', 'informacoes', 'support_id', 'contact_id'])
            ->assertJsonFragment([
                'data' => [
                    'createService' => [
                        'tipo_servico' => $service['tipo_servico'],
                        'encerrado' => null,
                        'informacoes' => $service['informacoes'],
                        'support_id' => $service['support_id'],
                        'contact_id' => $service['contact_id'],
                    ]
                ]
            ]);
    }

    public function test_create_with_failed_validation()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $contact = Contact::factory()->create();
        $service = [
            'tipo_servico' => '',
            //'encerrado' => false,
            'informacoes' => null,
            'support_id' => null,
            'contact_id' => $contact->id,
        ];
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('createService', [
                'tipo_servico' => $service['tipo_servico'],
                //'encerrado' => $service['encerrado'],
                'informacoes' => $service['informacoes'],
                'support_id' => $service['support_id'],
                'contact_id' => $service['contact_id'],
            ], ['tipo_servico', 'encerrado', 'informacoes', 'support_id', 'contact_id'])
            ->assertJsonFragment([
                'message' => 'validation',
            ]);
    }

    public function test_update()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $service = Service::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('updateService', [
                'id' => $service->id, 'contact_id' => $service->contact_id, 'tipo_servico' => $service ->tipo_servico,
            ], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'updateService' => [
                        'id' => $service->id,                       
                    ],
                ],
            ]);
    }

    public function test_update_with_failed_validation()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $service = Service::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('updateService', [
                'id' => 0, 'contact_id' => $service->contact_id, 'tipo_servico' => $service ->tipo_servico,
            ], ['id'])
            ->assertJsonFragment([
                'message' => 'validation'
            ]);
    }

    public function test_update_if_object_not_found()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $service = Service::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('updateService', [
                'id' => 0, 'contact_id' => $service->contact_id, 'tipo_servico' => $service ->tipo_servico,
        ], ['id'])
            ->assertJsonFragment([
                'message' => 'validation'
            ]);
    }

    public function test_destroy()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $service = Service::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('deleteService', ['id' => $service->id], [])
            ->assertJsonFragment([
                'deleteService' => true
            ]);
    }

    public function test_destroy_if_object_not_found()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('deleteService', ['id' => 0], [])
            ->assertJsonFragment([
                'message' => 'validation'
            ]);
    }

    public function test_restore()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $service = Service::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('restoreService', ['id' => $service->id], [])
            ->assertJsonFragment([
                'restoreService' => true
            ]);
    }

    public function test_restore_if_object_not_found()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('restoreService', ['id' => 0], [])
            ->assertJsonFragment([
                'message' => 'validation'
            ]);
    }
    public function test_associate_with_support()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $support = Support::factory()->create(['area_atuacao' => 'contabilidade']);
        $contact = Contact::factory()->create(['area_atendimento' => 'contabilidade']);
        $service = Service::factory()->create(['contact_id' => $contact->id]);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('associateService', ['service_id' => $service->id], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'associateService' => [
                        'id' => $service->id,                       
                    ],
                ],
            ]);
    }

    public function test_associate_without_support_livre()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $service = Service::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('associateService', ['service_id' => $service->id], ['id'])
            ->assertJsonFragment([
                'debugMessage' => 'Não existe suporte livre para a área de atendimento.'
            ]);
    }

    public function test_finished_with_support()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'suporte']);
        $token = auth()->login($user);
        $support = Support::factory()->create(['area_atuacao' => 'contabilidade', 'user_id' => $user->id]);
        $contact = Contact::factory()->create(['area_atendimento' => 'contabilidade']);
        $service = Service::factory()->create(['contact_id' => $contact->id, 'support_id' =>$support->id]);
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('finishedService', ['service_id' => $service->id], ['id'])
            ->assertJsonFragment([
                'data' => [
                    'finishedService' => [
                        'id' => $service->id,                       
                    ],
                ],
            ]);
    }

    public function test_finished_without_support()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'suporte']);
        $token = auth()->login($user);
        $service = Service::factory()->create();
        $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->mutation('finishedService', ['service_id' => $service->id], ['id'])
            ->assertJsonFragment([
                'debugMessage' => 'Não é possível finalizar o serviço.'
            ]);
    }
    
}
