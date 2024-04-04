<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ServiceTypeCountType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ServiceTypeCount',
        'description' => 'Tipo para contabilizar o tipo de serviço'
    ];

    public function fields(): array
    {
        return [
            'serviceType' => [
                'type' => Type::string(),
                'description' => 'O nome do tipo de serviço',
            ],
            'quantity' => [
                'type' => Type::int(),
                'description' => 'A quantidade do tipo de serviço',
            ],
        ];
    }
}
