<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Services\RecommendationService;
use App\Models\Recommendation;

class ConnectController extends Controller
{
    /**
     * Tela principal do módulo Conectar
     */
    public function index()
    {
        return view('connect.index');
    }

    /**
     * Retorna JSON do próximo card real a partir de recommendations pendentes.
     */
    public function next(Request $request, RecommendationService $service)
    {
        // Limite por sessão: 20 cards
        $count = (int)session()->get('connect_cards_shown', 0);
        if ($count >= 20) {
            return response()->json(['done' => true], 204);
        }

        $user = $request->user();
        $rec = $service->nextCardFor($user);
        // Fallback: se não houver batch gerado ainda, tenta gerar agora e buscar novamente
        if (!$rec) {
            try {
                $service->generateDailyBatchFor($user, 50);
            } catch (\Throwable $e) {
                // ignora e segue
            }
            $rec = $service->nextCardFor($user);
        }
        if (!$rec) {
            return response()->json(['empty' => true], 204);
        }

        // Incrementa contador de sessão
        session()->put('connect_cards_shown', $count + 1);

        return response()->json($this->mapRecommendationToCard($rec));
    }

    /**
     * Recebe uma decisão do usuário e aplica na recommendation; pode retornar {match:true}.
     */
    public function decide(Request $request, RecommendationService $service)
    {
        $validated = $request->validate([
            'recommendation_id' => ['required', 'integer'],
            'action' => ['required', 'in:liked,rejected,saved,undo'],
        ]);

        $user = $request->user();
        $result = $service->decide($user, (int)$validated['recommendation_id'], $validated['action']);

        return response()->json($result);
    }

    private function mapRecommendationToCard(Recommendation $rec): array
    {
        if ($rec->target_type === 'job_vacancy') {
            $job = $rec->targetJob; // relation
            $companyName = $job?->company?->display_name ?: ($job?->company?->name ?: 'Empresa');
            return [
                'id' => $rec->id,
                'type' => 'job',
                'score' => round((float)$rec->score, 2),
                'payload' => [
                    'title' => $job?->title ?: 'Vaga',
                    'company' => $companyName,
                    'location' => $job?->company?->location ?: '-',
                    'mode' => $job?->location_type ?: '-',
                    'range' => $job?->salary_range ?: '-',
                    'skills' => [], // opcionalmente re-extrair do texto
                    'summary' => Str::limit($job?->description ?: '', 180),
                ],
            ];
        }

        if ($rec->target_type === 'freelancer') {
            $f = $rec->targetFreelancer;
            $skills = $f?->skills()->pluck('name')->all() ?? [];
            return [
                'id' => $rec->id,
                'type' => 'freelancer',
                'score' => round((float)$rec->score, 2),
                'payload' => [
                    'title' => $f?->display_name ?: 'Freelancer',
                    'location' => $f?->location ?: '-',
                    'mode' => '—',
                    'range' => $f?->hourly_rate ? ('R$ '.number_format((float)$f->hourly_rate, 2, ',', '.').'/h') : '-',
                    'skills' => $skills,
                    'summary' => Str::limit($f?->bio ?: '', 180),
                ],
            ];
        }

        return [
            'id' => $rec->id,
            'type' => 'unknown',
            'score' => round((float)$rec->score, 2),
            'payload' => [ 'title' => '—' ]
        ];
    }
}