<?php

namespace App\GraphQL\Validations;

use Illuminate\Support\Facades\Validator;

class ContactValidation
{
    public static function make(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : null;

        $rules = [
            'nome_pessoa' => ['required', 'string', 'max:150'],
            'nome_client' => ['required', 'string', 'max:150'],
            'area_atendimento' => ['required', 'string', 'max:150'],
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

        return $validator;
    }
}