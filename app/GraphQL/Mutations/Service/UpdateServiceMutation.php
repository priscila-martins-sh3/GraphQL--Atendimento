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

class UpdateServiceMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $permisao = ['admin', 'recepcionista', 'suporte' ];
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
        'name' => 'service/UpdateService',
        'description' => 'Atualiza um serviço'
    ];

    public function type(): Type
    {
        return GraphQL::type('Service');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'rules' => ['required', 'exists:services,id,deleted_at,NULL'],
            ],
            'tipo_servico' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'O tipo de serviço ',
            ],            
            'encerrado' => [
                'type' => Type::boolean(),
                'description' => 'Se o serviço foi finalizado ou não',
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

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'id.exists' => 'Serviço não encontrado.',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $service = Service::findorFail($args['id']);
        
        $validator = ServiceValidation::make($args);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            throw ValidationException::withMessages($errors);
        }
        
        $service->update($args);
        $service = $service->fresh();

        return $service;
    }
}
