<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Service;
use App\Models\Support;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;



class ReportTest extends TestCase
{
    //use RefreshDatabase;

    public function test_query_supportReport()
    {
        $user = User::factory()->create(['tipo_funcionario' => 'admin']);
        $token = auth()->login($user);
        $support1 = Support::factory()->create(['area_atuacao' => 'contabilidade']);
        $support2 = Support::factory()->create(['area_atuacao' => 'compras']);
        $contact1 = Contact::factory()->create(['area_atendimento' => 'contabilidade']);
        $contact2 = Contact::factory()->create(['area_atendimento' => 'compras']);
        $service = Service::factory()->create(['tipo_servico' => 'tirar_duvida', 'encerrado' => true, 'support_id' => $support1->id, 'contact_id' => $contact1->id]);
        $service = Service::factory()->create(['tipo_servico' => 'tirar_duvida', 'encerrado' => true, 'support_id' => $support1->id, 'contact_id' => $contact1->id]);
        $service = Service::factory()->create(['tipo_servico' => 'informar_problema', 'encerrado' => true, 'support_id' => $support1->id, 'contact_id' => $contact1->id]);
        $service = Service::factory()->create(['tipo_servico' => 'informar_problema', 'encerrado' => true, 'support_id' => $support2->id, 'contact_id' => $contact2->id]);


        $response = $this->withHeaders(["Authorization" => "Bearer {$token}"])
            ->query(
                'supportReport',
                [],
                [
                    'nome_suporte',
                    'area_atuacao',
                    'services_encerrados',
                    'service_atual',
                    'services_tipo' => [
                        'serviceType',
                        'quantity'
                    ],
                    'cliente_mais_atendido',
                    'qtidade_cliente'
                ]
            );
        $response->assertJsonStructure([
            'data' => [
                'supportReport' => [
                    '*' => [
                        'nome_suporte',
                        'area_atuacao',
                        'services_encerrados',
                        'service_atual',
                        'services_tipo',
                        'cliente_mais_atendido',
                        'qtidade_cliente'
                    ]
                ]
            ]
        ]);
    }
}
