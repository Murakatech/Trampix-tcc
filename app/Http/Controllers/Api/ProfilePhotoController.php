<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Controller para gerenciar atualizações dinâmicas da foto de perfil
 * Implementa sistema de polling para verificação em tempo real
 */
class ProfilePhotoController extends Controller
{
    /**
     * Verifica se houve mudanças na foto de perfil do usuário
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function checkUpdates(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuário não autenticado'
                ], 401);
            }

            // Obter timestamp da última modificação da foto
            $lastModified = $request->header('If-Modified-Since');
            $currentPhotoPath = $this->getCurrentPhotoPath($user);
            $currentModified = $this->getPhotoLastModified($currentPhotoPath);
            
            // Verificar se houve mudanças
            if ($lastModified && strtotime($lastModified) >= $currentModified) {
                return response()->json([
                    'success' => true,
                    'changed' => false
                ], 304); // Not Modified
            }

            // Retornar dados atualizados da foto
            $photoData = $this->getPhotoData($user);
            
            return response()->json([
                'success' => true,
                'changed' => true,
                'data' => $photoData,
                'timestamp' => $currentModified
            ])->header('Last-Modified', gmdate('D, d M Y H:i:s', $currentModified) . ' GMT');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor',
                'message' => config('app.debug') ? $e->getMessage() : 'Erro ao verificar atualizações'
            ], 500);
        }
    }

    /**
     * Retorna dados completos do perfil para atualização da navbar
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getProfileData(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuário não autenticado'
                ], 401);
            }

            $activeRole = session('active_role');
            $profileData = $this->getCompleteProfileData($user, $activeRole);
            
            return response()->json([
                'success' => true,
                'data' => $profileData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar dados do perfil',
                'message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtém o caminho atual da foto de perfil
     * 
     * @param \App\Models\User $user
     * @return string|null
     */
    private function getCurrentPhotoPath($user): ?string
    {
        $activeRole = session('active_role');
        
        // Verificar foto específica do perfil ativo
        if ($activeRole === 'freelancer' && $user->freelancer && $user->freelancer->profile_photo_path) {
            return $user->freelancer->profile_photo_path;
        }
        
        if ($activeRole === 'company' && $user->company && $user->company->profile_photo_path) {
            return $user->company->profile_photo_path;
        }
        
        // Fallback para foto do usuário
        return $user->profile_photo_path;
    }

    /**
     * Obtém timestamp da última modificação da foto
     * 
     * @param string|null $photoPath
     * @return int
     */
    private function getPhotoLastModified(?string $photoPath): int
    {
        if (!$photoPath || !Storage::disk('public')->exists($photoPath)) {
            return time(); // Retorna timestamp atual se não há foto
        }
        
        return Storage::disk('public')->lastModified($photoPath);
    }

    /**
     * Obtém dados da foto de perfil formatados
     * 
     * @param \App\Models\User $user
     * @return array
     */
    private function getPhotoData($user): array
    {
        $activeRole = session('active_role');
        $photoPath = $this->getCurrentPhotoPath($user);
        
        // Determinar nome de exibição
        $displayName = $user->name;
        if ($activeRole === 'freelancer' && $user->freelancer) {
            $displayName = $user->freelancer->display_name ?? $user->name;
        } elseif ($activeRole === 'company' && $user->company) {
            $displayName = $user->company->display_name ?? $user->name;
        }

        return [
            'photo_url' => $photoPath ? asset('storage/' . $photoPath) : null,
            'photo_path' => $photoPath,
            'display_name' => $displayName,
            'initials' => $this->getInitials($displayName),
            'active_role' => $activeRole,
            'has_photo' => !is_null($photoPath)
        ];
    }

    /**
     * Obtém dados completos do perfil para sidebar
     * 
     * @param \App\Models\User $user
     * @param string|null $activeRole
     * @return array
     */
    private function getCompleteProfileData($user, ?string $activeRole): array
    {
        $photoData = $this->getPhotoData($user);
        
        return array_merge($photoData, [
            'email' => $user->email,
            'role' => $activeRole ?? $user->role,
            'permissions' => $this->getUserPermissions($user, $activeRole),
            'menu_items' => $this->getMenuItems($user, $activeRole)
        ]);
    }

    /**
     * Obtém iniciais do nome para placeholder
     * 
     * @param string $name
     * @return string
     */
    private function getInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';
        
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return $initials ?: 'U';
    }

    /**
     * Obtém permissões do usuário baseadas no papel ativo
     * 
     * @param \App\Models\User $user
     * @param string|null $activeRole
     * @return array
     */
    private function getUserPermissions($user, ?string $activeRole): array
    {
        $permissions = [];
        
        if ($activeRole === 'freelancer' || $user->isFreelancer()) {
            $permissions[] = 'view_jobs';
            $permissions[] = 'apply_jobs';
            $permissions[] = 'manage_applications';
        }
        
        if ($activeRole === 'company' || $user->isCompany()) {
            $permissions[] = 'create_jobs';
            $permissions[] = 'manage_jobs';
            $permissions[] = 'view_applications';
        }
        
        if ($user->isAdmin()) {
            $permissions[] = 'admin_access';
            $permissions[] = 'manage_users';
            $permissions[] = 'manage_system';
        }
        
        return $permissions;
    }

    /**
     * Obtém itens do menu baseados no papel do usuário
     * 
     * @param \App\Models\User $user
     * @param string|null $activeRole
     * @return array
     */
    private function getMenuItems($user, ?string $activeRole): array
    {
        $items = [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fa-house']
        ];
        
        if ($activeRole === 'freelancer' || $user->isFreelancer()) {
            $items[] = ['label' => 'Buscar Vagas', 'route' => 'vagas.index', 'icon' => 'fa-magnifying-glass'];
            $items[] = ['label' => 'Minhas Candidaturas', 'route' => 'applications.index', 'icon' => 'fa-file-text'];
        }
        
        if ($activeRole === 'company' || $user->isCompany()) {
            $items[] = ['label' => 'Minhas Vagas', 'route' => 'job_vacancies.index', 'icon' => 'fa-briefcase'];
            $items[] = ['label' => 'Perfil da Empresa', 'route' => 'profile.edit', 'icon' => 'fa-building'];
        }
        
        if ($user->isAdmin()) {
            $items[] = ['label' => 'Usuários', 'route' => 'admin.users', 'icon' => 'fa-users'];
            $items[] = ['label' => 'Empresas', 'route' => 'admin.companies', 'icon' => 'fa-building'];
            $items[] = ['label' => 'Freelancers', 'route' => 'admin.freelancers', 'icon' => 'fa-id-card'];
        }
        
        return $items;
    }
}