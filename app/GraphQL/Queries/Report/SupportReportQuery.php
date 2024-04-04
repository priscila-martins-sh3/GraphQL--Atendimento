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
        return Type::listOf(GraphQL::type('ReportSupport'));
        
    }

    public function args(): array
    {
        return [

        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $relatorio = [];
        //$suportes = Support::all();   
        $suportes = Support::with('user')->get(); // Carregue os suportes e seus usuários associados     
              
        foreach ($suportes as $suporte) {
            $services = $suporte->services;
            $servicesEncerrados = $services->where('encerrado', true)->count();
            $serviceAtual = $services->where('encerrado', false)->first();

            $servicesPorTipo = $services->select('tipo_servico', DB::raw('count(*) as total'))
                                        ->groupBy('tipo_servico')
                                        ->get();
            var_dump($servicesPorTipo);
            $clienteMaisAtendido = $services->join('contacts', 'services.contact_id', '=', 'contacts.id')
                                         ->select('contacts.nome_cliente', DB::raw('count(*) as total'))
                                         ->groupBy('contacts.nome_cliente')
                                         ->orderByDesc('total')
                                         ->first();                                            
         
            $relatorio[] = [
                'nome_suporte' => $suporte->user->name,
                'area_atuacao' => $suporte->area_atuacao, 
                'services_encerrados' => $servicesEncerrados,
                'service_atual' => $serviceAtual,
                'services_tipo' => $servicesPorTipo,                
                'cliente_mais_atendido' => $clienteMaisAtendido ? $clienteMaisAtendido->nome_cliente : null,
                'qtidade_cliente' => $clienteMaisAtendido ? $clienteMaisAtendido->total : null,
            ];
        }

        return $relatorio;
        }
}

