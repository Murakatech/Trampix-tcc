<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Freelancer;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FreelancerCvDownloadTest extends TestCase
{
    use RefreshDatabase;

    private function createFreelancerUserWithCv(): array
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'freelancer']);
        $user->createProfile('freelancer', [
            'cv_url' => 'cvs/test-cv.pdf',
        ]);
        $freelancer = $user->freelancer;

        // Criar arquivo no storage fake
        Storage::disk('public')->put('cvs/test-cv.pdf', 'dummy content');

        return [$user, $freelancer];
    }

    public function test_owner_can_download_own_cv()
    {
        [$user, $freelancer] = $this->createFreelancerUserWithCv();

        $this->actingAs($user);

        $response = $this->get(route('freelancers.download-cv', $freelancer));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('CV_' . $user->name . '.pdf', $response->headers->get('content-disposition'));
    }

    public function test_company_user_can_download_freelancer_cv()
    {
        [$freelancerOwner, $freelancer] = $this->createFreelancerUserWithCv();

        $companyUser = User::factory()->create(['role' => 'company']);
        $companyUser->createProfile('company', [
            'name' => $companyUser->name,
            'cnpj' => '',
            'sector' => '',
            'location' => '',
            'description' => '',
        ]);

        $this->actingAs($companyUser);

        $response = $this->get(route('freelancers.download-cv', $freelancer));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('CV_' . $freelancerOwner->name . '.pdf', $response->headers->get('content-disposition'));
    }

    public function test_other_freelancer_cannot_download_cv()
    {
        [$ownerUser, $freelancer] = $this->createFreelancerUserWithCv();

        $otherUser = User::factory()->create(['role' => 'freelancer']);
        $otherUser->createProfile('freelancer', []);

        $this->actingAs($otherUser);

        $response = $this->get(route('freelancers.download-cv', $freelancer));

        $response->assertStatus(403);
    }

    public function test_download_returns_404_when_file_missing()
    {
        $user = User::factory()->create(['role' => 'freelancer']);
        $user->createProfile('freelancer', [
            'cv_url' => 'cvs/missing.pdf',
        ]);
        $freelancer = $user->freelancer;

        Storage::fake('public');

        $this->actingAs($user);

        $response = $this->get(route('freelancers.download-cv', $freelancer));

        $response->assertStatus(404);
    }
}