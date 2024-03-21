<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Contact;

use App\Models\Contact;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteContactMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $permisao = ['admin'];
        try {
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return false;
        }
       
        $funcionario = $this->auth->tipo_funcionario;
      
        if (!$this->auth || !in_array($funcionario, $permisao)) {           
            return false;
        }       

        return (bool) $this->auth;        
    }
    
    protected $attributes = [
        'name' => 'deleteContact',
        'description' => 'Softdelete de um contato'
    ];

    public function type(): Type
    {
        return Type::boolean();
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'rules' =>
                [
                    'required',
                    'exists:contacts,id,deleted_at,NULL'
                ]
            ]
        ];
    }
    public function validationErrorMessages(array $args = []): array
    {
        return [
            'id.exists' => 'Contato não encontrado',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $contact = Contact::findOrFail($args['id']);
        $contact->delete();

        return true;
    }
}
