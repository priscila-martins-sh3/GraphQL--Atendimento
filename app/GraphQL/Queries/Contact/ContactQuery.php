<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Contact;

use Closure;
use App\Models\Contact;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class ContactQuery extends Query
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
        'name' => 'contact',
        'description' => 'Retorna um único contato com base no ID'
    ];

    public function type(): Type
    {
        return GraphQL::type('Contact');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'description' => 'ID do contato',
                'rules' =>
                [
                    'required',
                    'exists:contacts,id,deleted_at,NULL'
                ]
            ],
        ];
    }
    public function validationErrorMessages(array $args = []): array
    {
        return [
            'id.exists' => 'Contato não encontrado.',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectFields)
    {        
        $select = $selectFields->getSelect();
        $with = $selectFields->getRelations();
        
        $contact = Contact::with($with)->select($select)->findOrFail($args['id']);

        return $contact; 
    }
}

