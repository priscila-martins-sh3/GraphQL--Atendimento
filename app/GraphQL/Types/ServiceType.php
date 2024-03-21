<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Service;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ServiceType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Service',
        'description' => 'A service',
        'model' => Service::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'O ID do serviço dentro do banco de dados',
            ],            
            'tipo_servico' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O tipo de serviço ',
            ],           
            'encerrado' => [
                'type' => Type::boolean(),
                'default' => false,
                'description' => 'Se o serviço foi finalizado ou não',
            ],
            'informacoes' => [
                'type' => Type::string(),
                'description' => 'Informações adicionais do serviço',
            ],
            'support_id' => [
                'type' => Type::int(),
                'description' => 'O ID do suporte associado ao serviço',
            ],
            'contact_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O ID do contato associado ao serviço',
            ],
            'support' => [
                'type' => GraphQL::type('Support'), 
                'description' => 'O suporte associado ao serviço',
                'resolve' => function ($root, $args) {
                    return $root->support;
                },
            ],  
            'contact' => [
                'type' => GraphQL::type('Contact'), 
                'description' => 'O contato associado ao serviço',
                'resolve' => function ($root, $args) {
                    return $root->contact;
                },
            ],              

        ];
    }
}
