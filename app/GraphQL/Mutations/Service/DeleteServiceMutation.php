<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Service;

use App\Models\Service;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteServiceMutation extends Mutation
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
        'name' => 'service/DeleteService',
        'description' => 'Softdelete de um serviço'
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
                    'exists:services,id,deleted_at,NULL'
                ]
            ]
        ];
    }
    public function validationErrorMessages(array $args = []): array
    {
        return [
            'id.exists' => 'Serviço não encontrado',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $service = Service::findOrFail($args['id']);
        $service->delete();

        return true;
    }
}
