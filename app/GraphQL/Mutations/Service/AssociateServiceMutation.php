<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Service;

use App\Models\Service;
use App\Models\Support;
use Closure;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AssociateServiceMutation extends Mutation
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
        'name' => 'service/AssociateService',
        'description' => 'Associação de um suporte ao serviço'
    ];

    public function type(): Type
    {
        return GraphQL::type('Service');
    }

    public function args(): array
    {
        return [
            'service_id' => [
                'name' => 'service_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:services,id,deleted_at,NULL'],
            ],
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'service_id.exists' => 'Serviço não encontrado',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $serviceId = $args['service_id'];
        $service = Service::findOrFail($serviceId);       
        $contact = $service->contact;
                
        $support = Support::where('area_atuacao', $contact->area_atendimento)
                          ->where('livre', true)
                          ->first();
    
        if (!$support) {          
            return ['error' => 'Nenhum suporte disponível na mesma área'];
        }

        $service->update(['support_id' => $support->id]); 
       
        $support->livre = false;
        $support->save();

        return $service;
    }
}
