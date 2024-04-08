<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SupportReportType extends GraphQLType
{
    protected $attributes = [
        'name' => 'SupportReport',
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
                'type' => Type::int(),
                'description' => 'ID do Serviço atualmente atribuído ao suporte',
            ],
            'services_tipo' => [
                'type' => Type::listOf(GraphQL::type('CountService')),
                'description' => 'Lista do tipos de serviços e sua quantidade',
                'resolve' => function ($root, $args) {                    
                    
                    if (isset($root['services_tipo']) && is_array($root['services_tipo'])) {
                        return $root['services_tipo'];
                    }
                    
                    return null;
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
