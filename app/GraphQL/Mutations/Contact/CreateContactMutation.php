<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Contact;

use Closure;
use App\Models\Contact;
use App\GraphQL\Validations\ContactValidation;
use Illuminate\Validation\ValidationException;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CreateContactMutation extends Mutation
{
    

    protected $attributes = [
        'name' => 'createContact',
        'description' => 'Cria um novo contato'
    ];

    public function type(): Type
    {
        return GraphQL::type('Contact');
    }

    public function args(): array
    {
        return [
            'nome_pessoa' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O nome da pessoa do contato',
            ],
            'nome_cliente' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O nome do cliente do contato',
            ],
            'area_atendimento' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A área de atendimento do contato',
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $validator = ContactValidation::make($args);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            throw ValidationException::withMessages($errors);
        }

        $contact = new Contact();
        $contact->fill($args);
        $contact->save();

        return $contact;
    }
}
