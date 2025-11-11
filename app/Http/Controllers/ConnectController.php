<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     * Retorna JSON de um card de exemplo (stub)
     */
    public function next(Request $request)
    {
        $types = ['job', 'freelancer'];
        $skillsPool = ['Laravel', 'PHP', 'Alpine.js', 'Tailwind', 'MySQL', 'REST', 'Docker', 'CI/CD', 'UX'];
        $locations = ['Remoto', 'São Paulo, SP', 'Rio de Janeiro, RJ', 'Belo Horizonte, MG'];
        $modes = ['Tempo Integral', 'Meio Período', 'Contrato'];

        $pickSkills = fn($n) => collect($skillsPool)->shuffle()->take($n)->values()->all();

        $type = $types[array_rand($types)];
        $id = random_int(1000, 9999);

        $data = [
            'id' => $id,
            'type' => $type,
            'score' => random_int(50, 99),
            'payload' => [
                'title' => $type === 'job' ? 'Desenvolvedor Laravel' : 'Freelancer Full-Stack',
                'location' => $locations[array_rand($locations)],
                'mode' => $modes[array_rand($modes)],
                'range' => $type === 'job' ? 'R$ 8k–12k' : 'R$ 80–120/h',
                'skills' => $pickSkills(random_int(3, 6)),
                'summary' => 'Card de exemplo gerado como stub para navegação do módulo Conectar. Sem lógica real de match ainda.'
            ],
        ];

        return response()->json($data);
    }

    /**
     * Recebe uma decisão do usuário e retorna stub {match:false}
     */
    public function decide(Request $request)
    {
        $validated = $request->validate([
            'recommendation_id' => ['required', 'integer'],
            'action' => ['required', 'in:liked,rejected,saved'],
        ]);

        return response()->json([
            'ok' => true,
            'match' => false,
            'received' => $validated,
        ]);
    }
}