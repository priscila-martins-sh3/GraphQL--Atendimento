<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Report;

use App\Models\Support;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class SupportReportQuery extends Query
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        try {
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return false;
        }
        return (bool) $this->auth;
    }

    protected $attributes = [
        'name' => 'report/SupportReport',
        'description' => 'Relatório de suportes'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('SupportReport'));
        
    }

    public function args(): array
    {
        return [

        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $relatorio = [];
        $suportes = Support::all();   
      
        foreach ($suportes as $suporte) {
            $services = $suporte->services()->get();
            
            $servicesEncerrados = $services->where('encerrado', true)->count();
            $serviceAtual = $services->where('encerrado', false)->first();
            $serviceAtualId = $serviceAtual ? $serviceAtual->id : null;
            
            $servicesPorTipo = $services->groupBy('tipo_servico')->map(function ($items) {
                return [
                    'serviceType' => $items->first()->tipo_servico,
                    'quantity' => $items->count()
                ];
            })->values()->toArray();                       
        
            $clienteMaisAtendido = $services->groupBy('contact.nome_cliente')
                ->map(function ($items) {
                    return [
                        'nome_cliente' => $items->first()->contact->nome_cliente,
                        'total' => $items->count()
                    ];
                })
                ->sortByDesc('total')
                ->first();                                           
         
            $relatorio[] = [
                'nome_suporte' => $suporte->user->name,
                'area_atuacao' => $suporte->area_atuacao, 
                'services_encerrados' => $servicesEncerrados,
                'service_atual' => $serviceAtualId,
                'services_tipo' => $servicesPorTipo,                
                'cliente_mais_atendido' => $clienteMaisAtendido ? $clienteMaisAtendido['nome_cliente'] : null,
                'qtidade_cliente' => $clienteMaisAtendido ? $clienteMaisAtendido['total'] : null,
            ];
        }

        return $relatorio;
        }
}

