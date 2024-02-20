<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Contact;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ContactType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Contact',
        'description' => 'A contact',
        'model' => Contact::class
    ];

    public function fields(): array
    {
        return [
            'id' => [                
                'type' => Type::nonNull(Type::int()),
                'description' => 'O ID do contato dentro do banco de dados'
            ],
            'nome_pessoa' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O nome da pessoa do contato',
            ],
            'nome_cliente' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O nome do cliente do contato',
            ],
            'area_atendimento' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A área de atendimento do contato',
            ],
            'service' => [
                'type' => Type::listOf(GraphQL::type('Service')), 
                'description' => 'Os serviços atribuídos ao contato',
                'selectable' => false, 
            ],

        ];
    }
}
