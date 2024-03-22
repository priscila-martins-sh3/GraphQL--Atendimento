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

class ContactsQuery extends Query
{
    

    protected $attributes = [
        'name' => 'contacts',
        'description' => 'Retorna todos os contatos'
    ];

    public function type(): Type
    {        
        return Type::listOf(GraphQL::type('Contact'));
    }

    public function args(): array
    {
        return [

        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectFields)
    {
        $select = $selectFields->getSelect();
        $with = $selectFields->getRelations();
    
        $contacts = Contact::select($select)->with($with)->get();
        
        return $contacts;
    }
}

