<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Support;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SupportType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Support',
        'description' => 'A support',
        'model' => Support::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O ID do suporte dentro do banco de dados',
            ],
            'area_atuacao' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A área de atuação do suporte',
            ],
            'livre' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Se o suporte está disponível ou não',
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O ID do usuário associado ao suporte',
            ],            
            'user' => [
                'type' => GraphQL::type('User'), 
                'description' => 'O usuário associado ao suporte',
                'resolve' => function ($root) {
                    return $root->user;
                },
            ],  
            'service' => [
                'type' => Type::listOf(GraphQL::type('Service')), 
                'description' => 'Os serviços atribuídos ao suporte',
                'selectable' => false, 
            ],

        ];
    }
}



