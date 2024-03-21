<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Contact;

use App\Models\Contact;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;

class ContactsByClientQuery extends Query
{
    protected $attributes = [
        'name' => 'contact/ContactsByClient',
        'description' => 'Retorna os clientes que entraram em contato do dia'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Contact'));
    }

    public function args(): array
    {
        return [
            'data' => [
                'name' => 'data',
                'description' => 'Data da busca (formato: YYYY-MM-DD)',
                'type' => Type::nonNull(Type::string()),                
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectFields)
    {
        $select = $selectFields->getSelect();
        $with = $selectFields->getRelations();
            
        $query = Contact::whereData('created_at', $args['data'] )
            ->whereNotNull('nome_cliente')
            ->select($select)->with($with)
            ->get();

        return $query;
    }
}
