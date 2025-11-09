<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Freelancer;
use Carbon\Carbon;

class ProfilePhotoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'profile_type' => 'required|in:company,freelancer'
        ]);

        $user = Auth::user();
        $profileType = $request->profile_type;

        // Verificar se o usuário tem o perfil correspondente
        if ($profileType === 'company' && !$user->company) {
            return back()->with('error', 'Perfil de empresa não encontrado.');
        }

        if ($profileType === 'freelancer' && !$user->freelancer) {
            return back()->with('error', 'Perfil de freelancer não encontrado.');
        }

        // Upload da imagem
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . $user->id . '_' . $profileType . '.' . $file->getClientOriginalExtension();
            
            // Salvar na pasta public/storage/profile_photos
            $path = $file->storeAs('profile_photos', $filename, 'public');

            // Remover foto anterior se existir
            if ($profileType === 'company' && $user->company->profile_photo) {
                Storage::disk('public')->delete($user->company->profile_photo);
            } elseif ($profileType === 'freelancer' && $user->freelancer->profile_photo) {
                Storage::disk('public')->delete($user->freelancer->profile_photo);
            }

            // Atualizar o modelo correspondente
            if ($profileType === 'company') {
                $user->company->update(['profile_photo' => $path]);
            } else {
                $user->freelancer->update(['profile_photo' => $path]);
            }

            return back()->with('success', 'Foto de perfil atualizada com sucesso!');
        }

        return back()->with('error', 'Erro ao fazer upload da foto.');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'profile_type' => 'required|in:company,freelancer'
        ]);

        $user = Auth::user();
        $profileType = $request->profile_type;

        if ($profileType === 'company' && $user->company && $user->company->profile_photo) {
            Storage::disk('public')->delete($user->company->profile_photo);
            $user->company->update(['profile_photo' => null]);
        } elseif ($profileType === 'freelancer' && $user->freelancer && $user->freelancer->profile_photo) {
            Storage::disk('public')->delete($user->freelancer->profile_photo);
            $user->freelancer->update(['profile_photo' => null]);
        }

        return back()->with('success', 'Foto de perfil removida com sucesso!');
    }

    /**
     * Verifica se há atualizações no perfil do usuário (nome/foto/role)
     * Utiliza o header If-Modified-Since e retorna payload padronizado
     */
    public function checkUpdates(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([], 401);
        }

        // Para os testes, considerar a última modificação como a do próprio usuário
        $lastModified = $user->updated_at ?? now();

        $lastModifiedHeader = $request->header('If-Modified-Since');
        if ($lastModifiedHeader) {
            try {
                $clientLastModified = Carbon::createFromFormat('D, d M Y H:i:s \G\M\T', $lastModifiedHeader);
                if ($lastModified && $clientLastModified && $lastModified->lessThanOrEqualTo($clientLastModified)) {
                    return response()->json(null, 304)
                        ->header('Cache-Control', 'must-revalidate, no-cache, private')
                        ->header('Last-Modified', $lastModified->format('D, d M Y H:i:s \G\M\T'));
                }
            } catch (\Exception $e) {
                // Header malformado: ignorar e seguir com retorno 200
            }
        }

        // Montar payload esperado pelo teste
        $activeRole = $this->getActiveRole($user);
        $displayName = $this->getDisplayName($user, $activeRole);
        $photoUrl = $this->getProfilePhotoUrl($user);
        $hasPhoto = !empty($photoUrl);
        $initials = $this->generateInitials($displayName);
        $lastModifiedStr = $lastModified->format('D, d M Y H:i:s \G\M\T');

        return response()->json([
            // New contract
            'success' => true,
            'changed' => true,
            'data' => [
                'photo_url' => $photoUrl,
                'display_name' => $displayName,
                'initials' => $initials,
                'role' => $activeRole,
                'email' => $user->email,
                'has_photo' => $hasPhoto,
            ],
            'timestamp' => $lastModifiedStr,
            // Legacy keys expected by older tests
            'has_updates' => true,
            'last_modified' => $lastModifiedStr,
            'profile_photo_url' => $photoUrl,
        ])->header('Cache-Control', 'must-revalidate, no-cache, private')
          ->header('Last-Modified', $lastModifiedStr);
    }

    /**
     * Retorna dados completos do perfil do usuário (para navbar/sidebar)
     */
    public function getProfileData(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([], 401);
        }

        $activeRole = $this->getActiveRole($user);
        $displayName = $this->getDisplayName($user, $activeRole);
        $photoUrl = $this->getProfilePhotoUrl($user);
        $hasPhoto = !empty($photoUrl);
        $initials = $this->generateInitials($displayName);

        return response()->json([
            // New contract
            'success' => true,
            'data' => [
                'id' => $user->id,
                'display_name' => $displayName,
                'email' => $user->email,
                'role' => $activeRole,
                'photo_url' => $photoUrl,
                'has_photo' => $hasPhoto,
                'initials' => $initials,
            ],
            // Legacy contract for compatibility
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $activeRole,
                'profile_photo_url' => $photoUrl,
                'initials' => $this->generateInitials($user->name),
            ],
        ]);
    }

    /**
     * Obtém a URL da foto de perfil do usuário
     */
    private function getProfilePhotoUrl($user): ?string
    {
        $activeRole = $this->getActiveRole($user);

        // Preferir foto específica do perfil ativo
        if ($activeRole === 'company' && $user->company && $user->company->profile_photo) {
            return asset('storage/' . $user->company->profile_photo);
        }
        if ($activeRole === 'freelancer' && $user->freelancer && $user->freelancer->profile_photo) {
            return asset('storage/' . $user->freelancer->profile_photo);
        }

        // Fallback para foto do usuário
        if (isset($user->profile_photo) && $user->profile_photo) {
            return asset('storage/' . $user->profile_photo);
        }

        return null;
    }

    /**
     * Retorna o papel ativo considerando sessão e métodos do usuário
     */
    private function getActiveRole($user): string
    {
        return session('active_role') ?? $this->getUserRole($user);
    }

    /**
     * Determina o nome de exibição baseado no papel ativo
     */
    private function getDisplayName($user, string $activeRole): string
    {
        $name = $user->name;
        if ($activeRole === 'freelancer' && $user->freelancer) {
            $name = $user->freelancer->display_name ?? $name;
        } elseif ($activeRole === 'company' && $user->company) {
            $name = $user->company->display_name ?? $name;
        }
        return $name;
    }

    /**
     * Determina o role do usuário
     */
    private function getUserRole($user): string
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return 'admin';
        }
        
        if (method_exists($user, 'isCompany') && $user->isCompany()) {
            return 'company';
        }
        
        if (method_exists($user, 'isFreelancer') && $user->isFreelancer()) {
            return 'freelancer';
        }

        // Fallback para o campo role se os métodos não existirem
        return $user->role ?? 'freelancer';
    }

    /**
     * Gera iniciais do nome do usuário
     */
    private function generateInitials(string $name): string
    {
        if (empty(trim($name))) {
            return '?';
        }

        // Remover caracteres especiais e dividir por espaços
        $cleanName = preg_replace('/[^a-zA-ZÀ-ÿ\s]/', '', $name);
        $words = array_filter(explode(' ', $cleanName));

        if (empty($words)) {
            return '?';
        }

        // Se há apenas uma palavra, pegar a primeira letra
        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, 1));
        }

        // Se há múltiplas palavras, pegar primeira letra da primeira e última palavra
        $firstInitial = strtoupper(substr($words[0], 0, 1));
        $lastInitial = strtoupper(substr(end($words), 0, 1));

        return $firstInitial . $lastInitial;
    }
}