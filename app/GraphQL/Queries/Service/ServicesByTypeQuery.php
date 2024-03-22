<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Service;

use App\Models\Service;
use Closure;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class ServicesByTypeQuery extends Query
{
    protected $attributes = [
        'name' => 'service/ServicesByType',
        'description' => 'Retorna os serviços do dia de acordo com o tipos de atendimentos'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Service'));
    }

    public function args(): array
    {
        return [
            'data' => [
                'name' => 'data',
                'description' => 'Data da busca (formato: YYYY-MM-DD)',
                'type' => Type::nonNull(Type::string()),                
            ],
            'tipo_servico' => [
                'name' => 'tipo_servico',
                'description' => 'O tipo de servico buscado',
                'type' => Type::nonNull(Type::string()),                
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectFields)
    {
        $select = $selectFields->getSelect();
        $with = $selectFields->getRelations();
            
        $query = Service::whereDate('created_at', $args['data'] )
            ->where('tipo_servico', $args['tipo_servico'])
            ->select($select)->with($with)
            ->get();

        return $query;
    }
}
