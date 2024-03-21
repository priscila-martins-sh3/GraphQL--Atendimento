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

class ServicesByAreaQuery extends Query
{
    protected $attributes = [
        'name' => 'contact/ContactByArea',
        'description' => 'Retorna os serviços do dia de acordo com a área de atendimento'
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
            'area_atendimento' => [
                'name' => 'area_atendimento',
                'description' => 'A área de atendimento buscado',
                'type' => Type::nonNull(Type::int()),                
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectFields)
    {
        $areaAtendimento = $args['area_atendimento'];

        $select = $selectFields->getSelect();
        $with = $selectFields->getRelations();
            
        $query = Service::whereData('created_at', $args['data'] )
        ->whereHas('contact', function ($query) use ($areaAtendimento) {
            $query->where('area_atendimento', $areaAtendimento);
        })
            ->select($select)->with($with)
            ->get();         

        return $query;
    }
}