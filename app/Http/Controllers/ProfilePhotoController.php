<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Freelancer;

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
}