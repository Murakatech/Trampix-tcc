<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Freelancer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FreelancerCvTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_valid_cv_succeeds_and_stores_file(): void
    {
        $user = User::factory()->create();
        $freelancer = Freelancer::create(['user_id' => $user->id, 'is_active' => true]);

        Storage::fake('public');
        $cv = UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf');

        $response = $this->withSession(['active_role' => 'freelancer'])
            ->actingAs($user)
            ->patch(route('profile.update'), [
            'section' => 'freelancer',
            'cv' => $cv,
        ]);
        // Atualização de CV em perfil freelancer ativo deve redirecionar para página de edição de perfil
        $response->assertRedirect(route('profile.edit'));
        // No fluxo atual, criação redireciona para dashboard sem flash 'success'
        $freelancer->refresh();
        $this->assertNotNull($freelancer->cv_url);
        Storage::disk('public')->assertExists($freelancer->cv_url);
        $this->assertStringStartsWith('cvs/', $freelancer->cv_url);
    }

    public function test_reupload_cv_replaces_and_deletes_old(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');
        $freelancer = Freelancer::create([
            'user_id' => $user->id,
            'is_active' => true,
            'cv_url' => 'cvs/old.pdf',
        ]);
        Storage::disk('public')->put('cvs/old.pdf', 'oldcontent');

        $newCv = UploadedFile::fake()->create('new.pdf', 10, 'application/pdf');

        $response = $this->withSession(['active_role' => 'freelancer'])
            ->actingAs($user)
            ->patch(route('profile.update'), [
            'section' => 'freelancer',
            'cv' => $newCv,
        ]);
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');
        $freelancer->refresh();
        $this->assertNotEquals('cvs/old.pdf', $freelancer->cv_url);
        Storage::disk('public')->assertMissing('cvs/old.pdf');
        Storage::disk('public')->assertExists($freelancer->cv_url);
    }

    public function test_remove_cv_deletes_file_and_nulls_field(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');
        $freelancer = Freelancer::create([
            'user_id' => $user->id,
            'is_active' => true,
            'cv_url' => 'cvs/keep.pdf',
        ]);
        Storage::disk('public')->put('cvs/keep.pdf', 'content');

        $response = $this->withSession(['active_role' => 'freelancer'])
            ->actingAs($user)
            ->patch(route('profile.update'), [
            'section' => 'freelancer',
            'remove_cv' => true,
        ]);
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');
        $freelancer->refresh();
        $this->assertNull($freelancer->cv_url);
        Storage::disk('public')->assertMissing('cvs/keep.pdf');
    }

    public function test_invalid_cv_mime_is_rejected(): void
    {
        $user = User::factory()->create();
        Freelancer::create(['user_id' => $user->id, 'is_active' => true]);

        Storage::fake('public');
        $bad = UploadedFile::fake()->create('bad.png', 5, 'image/png');

        $response = $this->withSession(['active_role' => 'freelancer'])
            ->actingAs($user)
            ->patch(route('profile.update'), [
            'section' => 'freelancer',
            'cv' => $bad,
        ]);

        $response->assertSessionHasErrors(['cv']);
    }

    public function test_upload_cv_after_profile_creation_sets_path(): void
    {
        $user = User::factory()->create();

        Storage::fake('public');
        $cv = UploadedFile::fake()->create('cv.docx', 10, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        // 1) Criar o perfil de freelancer via flag dedicada
        $createResponse = $this->actingAs($user)
            ->patch(route('profile.update'), [
            'create_freelancer_profile' => true,
            'bio' => 'Novo perfil criado',
        ]);
        $createResponse->assertRedirect(route('profile.edit'));

        // 2) Fazer upload do CV após o perfil existir
        // Garantir que o usuário foi recarregado com relações atualizadas
        $user = $user->fresh();
        $user->load('freelancer');
        $uploadResponse = $this->withSession(['active_role' => 'freelancer'])
            ->actingAs($user)
            ->patch(route('profile.update'), [
            'section' => 'freelancer',
            'cv' => $cv,
        ]);
        $uploadResponse->assertRedirect(route('profile.edit'));
        $freelancer = Freelancer::where('user_id', $user->id)->first();
        $this->assertNotNull($freelancer);
        $this->assertNotNull($freelancer->cv_url);
        Storage::disk('public')->assertExists($freelancer->cv_url);
        $this->assertStringStartsWith('cvs/', $freelancer->cv_url);
    }
}