<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\user;

use App\GraphQL\Validations\UserValidation;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateMutation extends Mutation
{
    protected $attributes = [
        'name' => 'authenticate',
        'description' => 'Login do usuário no sistema'
    ];

    public function type(): Type
    {
        return Type::string();
    }

    public function args(): array
    {
        return [
            'email' => [
                'name' => 'email',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'email'],
              ],
              'password' => [
                'name' => 'password',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
              ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $credentials = [
            'email' => $args['email'],
            'password' => $args['password']
        ];

        $token = JWTAuth::attempt($credentials);

        if ($token == null) {
            throw new \Exception('Invalid Emailr or password.');
        }

        return $token;
    }
}
