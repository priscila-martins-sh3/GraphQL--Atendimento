<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Service;

use App\Models\Service;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class FinishedServiceMutation extends Mutation
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        $permisao = ['suporte'];
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
        'name' => 'service/FinishedService',
        'description' => 'Finalização de um serviço'
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
        $user = auth()->user();
        $serviceId = $args['service_id'];
        $service = Service::findOrFail($serviceId);

        $supports = $user->supports;

        $sameSupport = $supports->first(function ($support) use ($service) {
            return $support->id === $service->support_id; 
        });

        if ($sameSupport && $service->encerrado == false) {
            $service->update(['encerrado' => true]);
        } else {
            throw new \Exception("Não é possível finalizar o serviço");
        }

        $supports->livre = true;
        $supports->save();

        return $service;
    }
}
