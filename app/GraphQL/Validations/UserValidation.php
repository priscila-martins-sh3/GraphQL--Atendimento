<?php

namespace App\GraphQL\Validations;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserValidation
{
    public static function make(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : null;

        $rules = [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6'],
            'tipo_funcionario' => ['required', 'in:' .  User::tiposValidos()],
            'area_atuacao' => [$data['tipo_funcionario'] === 'suporte' ? 'required' : 'nullable'],       
              
        ];    
        
        if (!is_null($id)) {
            $adaptativeRules = [];
            foreach ($rules as $property => $propertyRules) {
                foreach ($propertyRules as $rule) {
                    if ($rule !== 'required') {
                        $adaptativeRules[$property][] = $rule;
                    }
                }
            }
            $rules = $adaptativeRules;
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $validator;
        }   
        
        $validator->after(function ($validator) use ($data) {           

            $tipo_funcionario = $data['tipo_funcionario'];
            $area_atuacao = $data['area_atuacao'];            

            if ($tipo_funcionario === 'suporte') {             
                if (count($area_atuacao) === 1 && $area_atuacao[0] === "") {
                    $validator->errors()->add('area_atuacao', 'A área de atuação do suporte deve ser definida.');
                }
            }
        });

        return $validator;
    }
}


