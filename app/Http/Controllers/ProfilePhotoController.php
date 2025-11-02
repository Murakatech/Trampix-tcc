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
     * Verifica se há atualizações no perfil do usuário
     * Utiliza o header If-Modified-Since para otimização
     */
    public function checkUpdates(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $lastModified = $user->updated_at;
        $lastModifiedHeader = $request->header('If-Modified-Since');

        // Verificar se o cliente tem uma versão atualizada
        if ($lastModifiedHeader) {
            try {
                $clientLastModified = Carbon::createFromFormat('D, d M Y H:i:s \G\M\T', $lastModifiedHeader);
                
                if ($lastModified->lessThanOrEqualTo($clientLastModified)) {
                    return response()->json(null, 304); // Not Modified
                }
            } catch (\Exception $e) {
                // Se o header estiver malformado, continuar normalmente
            }
        }

        $profilePhotoUrl = $this->getProfilePhotoUrl($user);

        return response()->json([
            'has_updates' => true,
            'last_modified' => $lastModified->format('D, d M Y H:i:s \G\M\T'),
            'profile_photo_url' => $profilePhotoUrl
        ])->header('Cache-Control', 'no-cache, must-revalidate')
          ->header('Last-Modified', $lastModified->format('D, d M Y H:i:s \G\M\T'));
    }

    /**
     * Retorna dados completos do perfil do usuário
     */
    public function getProfileData(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $profilePhotoUrl = $this->getProfilePhotoUrl($user);
        $role = $this->getUserRole($user);
        $initials = $this->generateInitials($user->name);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
                'profile_photo_url' => $profilePhotoUrl,
                'initials' => $initials
            ]
        ]);
    }

    /**
     * Obtém a URL da foto de perfil do usuário
     */
    private function getProfilePhotoUrl($user): ?string
    {
        // Verificar se há foto no perfil principal do usuário
        if (isset($user->profile_photo) && $user->profile_photo) {
            return asset('storage/' . $user->profile_photo);
        }

        // Verificar foto no perfil de empresa
        if ($user->company && $user->company->profile_photo) {
            return asset('storage/' . $user->company->profile_photo);
        }

        // Verificar foto no perfil de freelancer
        if ($user->freelancer && $user->freelancer->profile_photo) {
            return asset('storage/' . $user->freelancer->profile_photo);
        }

        return null;
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