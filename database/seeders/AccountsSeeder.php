<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Freelancer;
use App\Models\Company;
use App\Models\Segment;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1) Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@trampix.com'],
            [
                'name' => 'Administrador',
                'role' => 'admin',
                'password' => Hash::make('Trampix@123'),
                'email_verified_at' => now(),
            ]
        );

        // Helper: get Tecnologia e Informação segment as default
        $techSegment = Segment::firstWhere('name', 'Tecnologia e Informação');

        // 2) Freelancer (Muraka)
        $murakaUser = User::updateOrCreate(
            ['email' => 'muraka@trampix.com'],
            [
                'name' => 'Muraka',
                'role' => 'user',
                'password' => Hash::make('Muraka@123'),
                'email_verified_at' => now(),
            ]
        );

        // Try to copy a profile image for Muraka from the provided local path
        $murakaPhotoPath = null;
        $murakaLocalCandidates = [
            'C:\\Users\\Muraka\\Pictures\\Imagens\\muraka.jpg',
            'C:\\Users\\Muraka\\Pictures\\Imagens\\muraka.png',
            'C:\\Users\\Muraka\\Pictures\\Imagens\\muraka.jpeg',
        ];
        foreach ($murakaLocalCandidates as $local) {
            if (file_exists($local)) {
                $filename = 'muraka_' . Str::random(6) . '.' . pathinfo($local, PATHINFO_EXTENSION);
                $dest = 'profile_photos/' . $filename;
                // Copy to public storage disk
                Storage::disk('public')->put($dest, file_get_contents($local));
                $murakaPhotoPath = $dest;
                break;
            }
        }

        // Create active freelancer profile
        $murakaFreelancer = $murakaUser->createProfile('freelancer', [
            'display_name' => 'Muraka',
            'bio' => 'Desenvolvedor full-stack com foco em Laravel, Vue.js e arquitetura limpa.',
            'linkedin_url' => 'https://www.linkedin.com/in/muraka',
            'cv_url' => null,
            'whatsapp' => '11999999999',
            'location' => 'São Paulo/SP',
            'hourly_rate' => 120.00,
            'availability' => 'freelance',
            'profile_photo' => $murakaPhotoPath, // may be null if not copied
            'segment_id' => $techSegment?->id,
        ]);

        // 3) Company (MurakaTech)
        $murakaTechUser = User::updateOrCreate(
            ['email' => 'murakatech@trampix.com'],
            [
                'name' => 'MurakaTech',
                'role' => 'user',
                'password' => Hash::make('MurakaTech@123'),
                'email_verified_at' => now(),
            ]
        );

        // Try to copy a logo for MurakaTech from the provided local path
        $murakaTechLogoPath = null;
        $murakaTechLocalCandidates = [
            'C:\\Users\\Muraka\\Pictures\\Imagens\\murakatech.png',
            'C:\\Users\\Muraka\\Pictures\\Imagens\\murakatech.jpg',
            'C:\\Users\\Muraka\\Pictures\\Imagens\\murakatech.jpeg',
        ];
        foreach ($murakaTechLocalCandidates as $local) {
            if (file_exists($local)) {
                $filename = 'murakatech_' . Str::random(6) . '.' . pathinfo($local, PATHINFO_EXTENSION);
                $dest = 'profile_photos/' . $filename;
                Storage::disk('public')->put($dest, file_get_contents($local));
                $murakaTechLogoPath = $dest;
                break;
            }
        }

        $murakaTechCompany = $murakaTechUser->createProfile('company', [
            'display_name' => 'MurakaTech',
            'name' => 'Muraka Tecnologia Ltda',
            'cnpj' => '12.345.678/0001-90',
            'sector' => 'Tecnologia',
            'location' => 'São Paulo/SP',
            'description' => 'Empresa focada em soluções digitais, desenvolvimento de software e consultoria técnica.',
            'website' => 'https://murakatech.com.br',
            'linkedin_url' => 'https://www.linkedin.com/company/murakatech',
            'email' => 'contato@murakatech.com.br',
            'phone' => '1133334444',
            'company_size' => 'Pequena',
            'employees_count' => 15,
            'founded_year' => 2021,
            'profile_photo' => $murakaTechLogoPath, // may be null
            'segment_id' => $techSegment?->id,
        ]);

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('Accounts seeded: admin, Muraka (freelancer), MurakaTech (company).');
            if (!$murakaPhotoPath) {
                $this->command->warn('Muraka profile photo not found in C:\\Users\\Muraka\\Pictures\\Imagens. Using no photo.');
            }
            if (!$murakaTechLogoPath) {
                $this->command->warn('MurakaTech logo not found in C:\\Users\\Muraka\\Pictures\\Imagens. Using no logo.');
            }
        }
    }
}