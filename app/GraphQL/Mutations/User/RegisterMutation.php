<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use App\GraphQL\Validations\UserValidation;
use App\Models\Support;
use App\Models\User;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;

class RegisterMutation extends Mutation
{
    protected $attributes = [
        'name' => 'register',
        'description' => 'Registro de um usuário'
    ];

    public function type(): Type
    {
        return GraphQL::type('User');
    }

    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),                
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::nonNull(Type::string()),                
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::nonNull(Type::string()),                
            ],
            'tipo_funcionario' => [
                'name' => 'tipo_funcionario',
                'type' => Type::nonNull(Type::string()),                
            ],
            'area_atuacao' => [
                'name' => 'area_atuacao',
                'type' => Type::string(), 
            ]            
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $validator = UserValidation::make($args);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            throw ValidationException::withMessages($errors);
        }

        $user = User::create([
        	'name' => $args['name'],
        	'email' => $args['email'],
        	'password' => bcrypt($args['password']),
            'tipo_funcionario' => $args['tipo_funcionario'],
        ]);
        
        if ($args['tipo_funcionario'] === 'suporte') {
            Support::create([
                'area_atuacao' => $args['area_atuacao'],                
                'user_id' => $user->id,
                'livre' => true,
            ]);
        }

        return $user;
    }

}

