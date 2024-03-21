<?php

namespace App\GraphQL\Validations;

use App\Models\Service;
use Illuminate\Support\Facades\Validator;

class ServiceValidation
{
    public static function make(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : null;

        $rules = [
            'tipo_servico'=>['required', 'in:' . Service::tiposValidosServico()],                    
            'encerramento' => ['boolean'],
            'informacoes' => ['nullable'],    
            'support_id' => ['nullable', 'integer', 'exists:supports,id,deleted_at,NULL' ],  
            'contact_id' => ['required', 'integer', 'exists:contacts,id,deleted_at,NULL'], 
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

        return $validator;
    }
}