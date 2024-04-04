<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ReportSupportType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ReportSupport',
        'description' => 'Tipo para o relatório de suporte'
    ];

    public function fields(): array
    {
        return [
            'nome_suporte' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Nome do suporte',
            ],
            'area_atuacao' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Área de atuação do suporte',
            ],
            'services_encerrados' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Número de serviços encerrados pelo suporte',
            ],
            'service_atual' => [
                'type' => GraphQL::type('Service'),
                'description' => 'Serviço atualmente atribuído ao suporte',
            ],
            'services_tipo' => [
                'type' => Type::listOf(GraphQL::type('ServiceTypeCount')),
                'description' => 'Lista do tipos de serviços e sua quantidade',
                'resolve' => function ($root, $args) {
                    // Aqui, você pode acessar os dados de $root que contém os dados do relatório de suporte
                    // Vamos supor que $root contém uma chave 'services_tipo' que contém os dados de $servicesPorTipo
                    // então, você pode fazer algo como:
                    $servicesPorTipo = $root['services_tipo'];
            
                    // Agora, você precisa transformar os dados de $servicesPorTipo em um formato compatível com o tipo ServiceTypeCountType
                    // Aqui, vou assumir que $servicesPorTipo é uma coleção de objetos como discutido anteriormente
            
                    // Mapeie os dados para o formato esperado pelo tipo ServiceTypeCountType
                    $serviceTypeCounts = [];
                    foreach ($servicesPorTipo as $service) {
                        $serviceTypeCounts[] = [
                            'serviceType' => $service->tipo_servico,
                            'quantity' => $service->total,
                        ];
                    }
            
                    return $serviceTypeCounts;
                },
            ],
            'cliente_mais_atendido' => [
                'type' => Type::string(),
                'description' => 'Nome do cliente mais atendido pelo suporte',
            ],
            'qtidade_cliente' => [
                'type' => Type::int(),
                'description' => 'Quantidade de atendimentos ao cliente mais atendido pelo suporte',
            ],
        ];
    }
}
