<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Service;

use App\GraphQL\Validations\ServiceValidation;
use App\Models\Service;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateServiceMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $permisao = ['admin', 'recepcionista'];
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
        'name' => 'service/CreateService',
        'description' => 'Cria um novo serviço '
    ];

    public function type(): Type
    {
        return GraphQL::type('Service');
    }

    public function args(): array
    {
        return [
            'tipo_servico' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O tipo de serviço que quer atendimento',
            ],                        
            'informacoes' => [
                'type' => Type::string(),
                'description' => 'Informações adicionais do serviço',
            ],
            'support_id' => [
                'type' => Type::int(),
                'description' => 'O ID do suporte associado ao serviço',
            ],
            'contact_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'O ID do contato associado ao serviço',
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $validator = ServiceValidation::make($args);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            throw ValidationException::withMessages($errors);
        }

        $service = new Service();
        $service->fill($args);
        $service->save();

        return $service;
    }
}
