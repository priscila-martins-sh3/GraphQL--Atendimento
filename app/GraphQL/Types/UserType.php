<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'A user',
        'model' => User::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'description' => 'O ID do usuário dentro do banco de dados'
            ],
            'name' => [                
                'type' => Type::nonNull(Type::string()),
                'description' => 'O nome do usuário'
            ],
            'email' => [                
                'type' => Type::nonNull(Type::string()),
                'description' => 'O e-mail do usuário'
            ],            
            'tipo_funcionario' => [                
                'type' => Type::nonNull(Type::string()),
                'description' => 'O tipo de funcionário do usuário'
            ],
            'supports' => [
                'type' => Type::listOf(GraphQL::type('Support')), 
                'description' => 'Os suportes atribuídos ao usuário',
                //'selectable' => false,
                'resolve' => function ($root, $args) {
                    return $root->suports;
                }
            ],

        ];
    }
}



