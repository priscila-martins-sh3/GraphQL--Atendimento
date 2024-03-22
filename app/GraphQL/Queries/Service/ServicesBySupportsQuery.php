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
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServicesBySupportsQuery extends Query
{
   

    protected $attributes = [
        'name' => 'service/ServicesBySupports',
        'description' => 'Retorna os serviços do dia de acordo com o suporte'
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
            'support_id' => [
                'name' => 'support_id',
                'description' => 'O ID do suporte buscado',
                'type' => Type::nonNull(Type::int()), 
                'rules' => ['required', 'exists:supports,id,deleted_at,NULL'],               
            ],
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'service_id.exists' => 'ID do suporte não encontrado',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectFields)
    {
        $select = $selectFields->getSelect();
        $with = $selectFields->getRelations();
            
        $query = Service::whereDate('created_at', $args['data'] )
            ->where('support_id', $args['support_id'])
            ->select($select)->with($with)
            ->get();
        
        if ($query->isEmpty()){
            return null;
        }    

        return $query;
    }
}
