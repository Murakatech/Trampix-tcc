<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_invalid_file_is_rejected_for_freelancer(): void
    {
        $user = User::factory()->create();
        Freelancer::create(['user_id' => $user->id, 'is_active' => true]);

        Storage::fake('public');
        $invalid = UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf');

        $response = $this->actingAs($user)->post(route('profile.photo.upload'), [
            'profile_type' => 'freelancer',
            'profile_photo' => $invalid,
        ]);

        $response->assertSessionHasErrors(['profile_photo']);
    }

    public function test_upload_invalid_file_is_rejected_for_company(): void
    {
        $user = User::factory()->create();
        Company::create(['user_id' => $user->id, 'name' => 'ACME', 'is_active' => true]);

        Storage::fake('public');
        $invalid = UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf');

        $response = $this->actingAs($user)->post(route('profile.photo.upload'), [
            'profile_type' => 'company',
            'profile_photo' => $invalid,
        ]);

        $response->assertSessionHasErrors(['profile_photo']);
    }

    public function test_delete_freelancer_photo_removes_file_and_clears_model(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');

        $freelancer = Freelancer::create([
            'user_id' => $user->id,
            'is_active' => true,
            'profile_photo' => 'profile_photos/f1.jpg',
        ]);

        Storage::disk('public')->put('profile_photos/f1.jpg', 'img');

        $response = $this->actingAs($user)->delete(route('profile.photo.delete'), [
            'profile_type' => 'freelancer',
        ]);

        $response->assertSessionHasNoErrors();
        $freelancer->refresh();
        $this->assertNull($freelancer->profile_photo);
        Storage::disk('public')->assertMissing('profile_photos/f1.jpg');
    }

    public function test_delete_company_photo_removes_file_and_clears_model(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'ACME',
            'is_active' => true,
            'profile_photo' => 'profile_photos/c1.jpg',
        ]);

        Storage::disk('public')->put('profile_photos/c1.jpg', 'img');

        $response = $this->actingAs($user)->delete(route('profile.photo.delete'), [
            'profile_type' => 'company',
        ]);

        $response->assertSessionHasNoErrors();
        $company->refresh();
        $this->assertNull($company->profile_photo);
        Storage::disk('public')->assertMissing('profile_photos/c1.jpg');
    }

    public function test_upload_fails_when_profile_not_found(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');
        $file = UploadedFile::fake()->create('photo.jpg', 10, 'image/jpeg');

        // Sem perfil freelancer/empresa
        $responseFreelancer = $this->actingAs($user)->post(route('profile.photo.upload'), [
            'profile_type' => 'freelancer',
            'profile_photo' => $file,
        ]);
        $responseFreelancer->assertSessionHas('error');

        $responseCompany = $this->actingAs($user)->post(route('profile.photo.upload'), [
            'profile_type' => 'company',
            'profile_photo' => $file,
        ]);
        $responseCompany->assertSessionHas('error');
    }

    public function test_upload_valid_image_succeeds_for_freelancer(): void
    {
        $user = User::factory()->create();
        $freelancer = Freelancer::create(['user_id' => $user->id, 'is_active' => true]);

        Storage::fake('public');
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO0l3foAAAAASUVORK5CYII=');
        $file = UploadedFile::fake()->createWithContent('photo.png', $png);

        $response = $this->actingAs($user)->post(route('profile.photo.upload'), [
            'profile_type' => 'freelancer',
            'profile_photo' => $file,
        ]);

        $response->assertSessionHas('success');
        $freelancer->refresh();
        $this->assertNotNull($freelancer->profile_photo);
        Storage::disk('public')->assertExists($freelancer->profile_photo);
        $this->assertStringStartsWith('profile_photos/', $freelancer->profile_photo);
    }

    public function test_upload_valid_image_succeeds_for_company(): void
    {
        $user = User::factory()->create();
        $company = Company::create(['user_id' => $user->id, 'name' => 'ACME', 'is_active' => true]);

        Storage::fake('public');
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO0l3foAAAAASUVORK5CYII=');
        $file = UploadedFile::fake()->createWithContent('logo.png', $png);

        $response = $this->actingAs($user)->post(route('profile.photo.upload'), [
            'profile_type' => 'company',
            'profile_photo' => $file,
        ]);

        $response->assertSessionHas('success');
        $company->refresh();
        $this->assertNotNull($company->profile_photo);
        Storage::disk('public')->assertExists($company->profile_photo);
        $this->assertStringStartsWith('profile_photos/', $company->profile_photo);
    }

    public function test_reupload_replaces_and_deletes_old_for_freelancer(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');
        $freelancer = Freelancer::create([
            'user_id' => $user->id,
            'is_active' => true,
            'profile_photo' => 'profile_photos/old_f.png',
        ]);
        Storage::disk('public')->put('profile_photos/old_f.png', 'oldimg');

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO0l3foAAAAASUVORK5CYII=');
        $file = UploadedFile::fake()->createWithContent('new.png', $png);

        $response = $this->actingAs($user)->post(route('profile.photo.upload'), [
            'profile_type' => 'freelancer',
            'profile_photo' => $file,
        ]);

        $response->assertSessionHas('success');
        $freelancer->refresh();
        $this->assertNotEquals('profile_photos/old_f.png', $freelancer->profile_photo);
        Storage::disk('public')->assertMissing('profile_photos/old_f.png');
        Storage::disk('public')->assertExists($freelancer->profile_photo);
    }

    public function test_reupload_replaces_and_deletes_old_for_company(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'ACME',
            'is_active' => true,
            'profile_photo' => 'profile_photos/old_c.png',
        ]);
        Storage::disk('public')->put('profile_photos/old_c.png', 'oldimg');

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO0l3foAAAAASUVORK5CYII=');
        $file = UploadedFile::fake()->createWithContent('new.png', $png);

        $response = $this->actingAs($user)->post(route('profile.photo.upload'), [
            'profile_type' => 'company',
            'profile_photo' => $file,
        ]);

        $response->assertSessionHas('success');
        $company->refresh();
        $this->assertNotEquals('profile_photos/old_c.png', $company->profile_photo);
        Storage::disk('public')->assertMissing('profile_photos/old_c.png');
        Storage::disk('public')->assertExists($company->profile_photo);
    }
}
