<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_check_profile_updates_for_authenticated_user()
    {
        // Arrange
        $user = User::factory()->create([
            'updated_at' => now()
        ]);

        // Act
        $response = $this->actingAs($user)
            ->getJson('/api/profile/check-updates');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'has_updates',
                'last_modified',
                'profile_photo_url'
            ]);
    }

    /** @test */
    public function it_returns_not_modified_when_no_updates()
    {
        // Arrange
        $user = User::factory()->create([
            'updated_at' => now()->subHour()
        ]);

        $lastModified = $user->updated_at->format('D, d M Y H:i:s \G\M\T');

        // Act
        $response = $this->actingAs($user)
            ->withHeaders(['If-Modified-Since' => $lastModified])
            ->getJson('/api/profile/check-updates');

        // Assert
        $response->assertStatus(304);
    }

    /** @test */
    public function it_returns_updates_when_profile_modified_after_if_modified_since()
    {
        // Arrange
        $user = User::factory()->create([
            'updated_at' => now()
        ]);

        $oldDate = now()->subHour()->format('D, d M Y H:i:s \G\M\T');

        // Act
        $response = $this->actingAs($user)
            ->withHeaders(['If-Modified-Since' => $oldDate])
            ->getJson('/api/profile/check-updates');

        // Assert
        $response->assertStatus(200)
            ->assertJson(['has_updates' => true]);
    }

    /** @test */
    public function it_can_get_profile_data_for_authenticated_user()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        // Act
        $response = $this->actingAs($user)
            ->getJson('/api/profile/data');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'profile_photo_url',
                    'initials'
                ]
            ])
            ->assertJson([
                'user' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'initials' => 'JD'
                ]
            ]);
    }

    /** @test */
    public function it_requires_authentication_for_check_updates()
    {
        // Act
        $response = $this->getJson('/api/profile/check-updates');

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function it_requires_authentication_for_profile_data()
    {
        // Act
        $response = $this->getJson('/api/profile/data');

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function it_handles_user_without_profile_photo()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Jane Smith'
        ]);

        // Act
        $response = $this->actingAs($user)
            ->getJson('/api/profile/data');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'profile_photo_url' => null,
                    'initials' => 'JS'
                ]
            ]);
    }

    /** @test */
    public function it_returns_correct_role_for_different_user_types()
    {
        // Test Freelancer
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        
        $response = $this->actingAs($freelancer)
            ->getJson('/api/profile/data');
        
        $response->assertJson(['user' => ['role' => 'freelancer']]);

        // Test Company
        $company = User::factory()->create(['role' => 'company']);
        
        $response = $this->actingAs($company)
            ->getJson('/api/profile/data');
        
        $response->assertJson(['user' => ['role' => 'company']]);

        // Test Admin
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
            ->getJson('/api/profile/data');
        
        $response->assertJson(['user' => ['role' => 'admin']]);
    }

    /** @test */
    public function it_handles_malformed_if_modified_since_header()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->withHeaders(['If-Modified-Since' => 'invalid-date'])
            ->getJson('/api/profile/check-updates');

        // Assert
        $response->assertStatus(200); // Should still work, just ignore the header
    }

    /** @test */
    public function it_returns_correct_cache_headers()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->getJson('/api/profile/check-updates');

        // Assert
        $response->assertStatus(200)
            ->assertHeader('Cache-Control', 'must-revalidate, no-cache, private')
            ->assertHeader('Last-Modified');
    }

    /** @test */
    public function it_generates_correct_initials_for_various_names()
    {
        $testCases = [
            ['name' => 'John Doe', 'expected' => 'JD'],
            ['name' => 'Maria Silva Santos', 'expected' => 'MS'],
            ['name' => 'José', 'expected' => 'J'],
            ['name' => 'Ana Paula', 'expected' => 'AP'],
            ['name' => '', 'expected' => '?'],
            ['name' => '123', 'expected' => '?'],
        ];

        foreach ($testCases as $testCase) {
            $user = User::factory()->create(['name' => $testCase['name']]);
            $expectedInitials = $testCase['expected'];

            $response = $this->actingAs($user)
                ->getJson('/api/profile/data');

            $response->assertStatus(200)
                ->assertJsonPath('user.initials', $expectedInitials);
        }
    }
}