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

class ServicesByAreaQuery extends Query
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        try {
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return false;
        }
        return (bool) $this->auth;
    }

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
                'type' => Type::nonNull(Type::string()),                
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectFields)
    {
        $areaAtendimento = $args['area_atendimento'];

        $select = $selectFields->getSelect();
        $with = $selectFields->getRelations();
            
        $query = Service:: whereDate('services.created_at', $args['data'] )
            ->join('contacts', 'services.contact_id', '=', 'contacts.id')
            ->where('contacts.area_atendimento', $areaAtendimento)            
            ->select($select)->with($with)
            ->get();         

        return $query;
    }
}


        