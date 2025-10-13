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

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'freelancer',
            'cv' => $cv,
        ]);
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');
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

        $response = $this->actingAs($user)->patch(route('profile.update'), [
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

        $response = $this->actingAs($user)->patch(route('profile.update'), [
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

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'freelancer',
            'cv' => $bad,
        ]);

        $response->assertSessionHasErrors(['cv']);
    }

    public function test_upload_cv_on_profile_creation_sets_path(): void
    {
        $user = User::factory()->create();

        Storage::fake('public');
        $cv = UploadedFile::fake()->create('cv.docx', 10, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'section' => 'freelancer',
            'cv' => $cv,
            'bio' => 'Novo perfil com CV',
        ]);
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');
        $freelancer = Freelancer::where('user_id', $user->id)->first();
        $this->assertNotNull($freelancer);
        $this->assertNotNull($freelancer->cv_url);
        Storage::disk('public')->assertExists($freelancer->cv_url);
        $this->assertStringStartsWith('cvs/', $freelancer->cv_url);
    }
}